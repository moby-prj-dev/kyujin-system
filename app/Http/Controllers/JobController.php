<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Mail\JobContinuedMail;
use App\Mail\JobVerificationMail;
use App\Models\AuditLog;
use App\Models\BillingAgreement;
use App\Models\Job;
use App\Models\JobAppeal;
use App\Models\JobArea;
use App\Models\JobCondition;
use App\Models\JobEmploymentType;
use App\Models\JobJobType;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use App\Services\SeoGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function __construct(
        private SeoGeneratorService $seoGenerator
    ) {}

    public function create()
    {
        $areas           = MasterArea::active()->where('prefecture', '沖縄県')->orderBy('sort_order')->get()->groupBy('region');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');
        $agreementText    = config('billing.agreement_text');
        $agreementVersion = config('billing.agreement_version');

        return view('jobs.create', compact(
            'areas', 'jobTypes', 'employmentTypes', 'conditions', 'appeals',
            'agreementText', 'agreementVersion'
        ));
    }

    public function store(StoreJobRequest $request)
    {
        // prepareForValidation で正規化済み
        $email = $request->contact_email;
        $phone = $request->contact_phone;

        $activeStatuses = [Job::STATUS_PENDING, Job::STATUS_DRAFT, Job::STATUS_ACTIVE, Job::STATUS_PAUSED];

        $emailMatch = Job::where('contact_email', $email)->whereIn('status', $activeStatuses)->exists();
        $phoneMatch = Job::where('contact_phone', $phone)->whereIn('status', $activeStatuses)->exists();

        if ($emailMatch || $phoneMatch) {
            $pattern = ($emailMatch && $phoneMatch) ? 'both' : ($emailMatch ? 'email' : 'phone');
            return redirect()->route('jobs.duplicate')
                ->with('duplicate_pattern', $pattern)
                ->with('duplicate_email', $emailMatch ? $email : null);
        }

        // 期限超過未払いチェック
        $hasOverdue = \App\Models\BillingSummary::where('contact_email', $email)
            ->where('status', \App\Models\BillingSummary::STATUS_OVERDUE)
            ->exists();
        if ($hasOverdue) {
            return back()->withInput()->withErrors([
                'contact_email' => 'お支払い期限を過ぎた未払い請求があります。未払い解消後に再度お試しください。',
            ]);
        }

        // Section 6: サーバー側でトライアル終了を再確認
        $trialEnded = $this->isTrialEnded($email);
        if ($trialEnded && $request->input('trial_confirmed') !== '1') {
            return back()->withInput()->withErrors([
                'trial_confirmed' => '課金条件への確認が必要です。再度フォームから送信してください。',
            ]);
        }

        DB::transaction(function () use ($request, $email, &$job) {
            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('job_photos', 'public')
                : null;

            $job = Job::create([
                'company_name'             => $request->company_name,
                'status'                   => Job::STATUS_PENDING,
                'contact_email'            => $email,
                'contact_phone'            => $request->contact_phone,
                'free_text'                => $request->free_text,
                'salary_type'              => $request->salary_type,
                'salary_min'               => $request->salary_min,
                'salary_max'               => $request->salary_max ?: null,
                'salary_note'              => $request->salary_note,
                'photo_path'               => $photoPath,
                'title'                    => $request->filled('title') ? $request->title : '（生成中）',
                'email_verification_token' => Str::random(64),
            ]);

            foreach ($request->areas as $id) {
                JobArea::create(['job_id' => $job->id, 'area_id' => $id]);
            }
            foreach ($request->job_types as $id) {
                JobJobType::create(['job_id' => $job->id, 'job_type_id' => $id]);
            }
            foreach ($request->employment_types as $id) {
                JobEmploymentType::create(['job_id' => $job->id, 'employment_type_id' => $id]);
            }
            foreach ($request->conditions as $id) {
                JobCondition::create(['job_id' => $job->id, 'condition_id' => $id]);
            }
            foreach ($request->appeals as $id) {
                JobAppeal::create(['job_id' => $job->id, 'appeal_id' => $id]);
            }

            BillingAgreement::create([
                'job_id'                 => $job->id,
                'agreement_flag'         => true,
                'agreement_text'         => config('billing.agreement_text'),
                'agreement_text_version' => config('billing.agreement_version'),
                'agreed_at'              => now(),
                'agreed_ip'              => $request->ip(),
                'user_agent'             => $request->userAgent(),
            ]);

            AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
                'contact_email' => $job->contact_email,
                'status'        => $job->status,
            ]);
            AuditLog::record(AuditLog::ENTITY_AGREEMENT, $job->id, AuditLog::ACTION_AGREEMENT_SAVED, AuditLog::ACTOR_SYSTEM, [
                'agreement_text_version' => config('billing.agreement_version'),
                'agreed_ip'              => $request->ip(),
            ]);
        });

        Mail::to($email)->send(new JobVerificationMail($job));

        return redirect()->route('jobs.verify_sent', ['email' => $email]);
    }

    public function verifySent(Request $request)
    {
        return view('jobs.verify_sent', ['email' => $request->query('email', '')]);
    }

    public function duplicate()
    {
        if (!session('duplicate_pattern')) {
            return redirect()->route('jobs.create');
        }
        return view('jobs.duplicate');
    }

    public function manage(string $token)
    {
        $job = Job::with(['jobAreas.area', 'jobJobTypes', 'jobEmploymentTypes', 'jobConditions', 'jobAppeals'])
            ->where('token', $token)
            ->firstOrFail();

        $areas           = MasterArea::active()->where('prefecture', '沖縄県')->orderBy('sort_order')->get()->groupBy('region');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');

        $selectedAreas           = $job->jobAreas->pluck('area_id')->toArray();
        $selectedJobTypes        = $job->jobJobTypes->pluck('job_type_id')->toArray();
        $selectedEmploymentTypes = $job->jobEmploymentTypes->pluck('employment_type_id')->toArray();
        $selectedConditions      = $job->jobConditions->pluck('condition_id')->toArray();
        $selectedAppeals         = $job->jobAppeals->pluck('appeal_id')->toArray();

        $applications = $job->applications()->orderByDesc('applied_at')->paginate(20);

        $billingSummaries = \App\Models\BillingSummary::where('contact_email', $job->contact_email)
            ->orderByDesc('billing_month')
            ->get();

        $trialEnded  = $this->isTrialEnded($job->contact_email);
        $hasUnpaid   = $billingSummaries->whereIn('status', [\App\Models\BillingSummary::STATUS_UNPAID])->isNotEmpty();
        $hasOverdue  = $billingSummaries->whereIn('status', [\App\Models\BillingSummary::STATUS_OVERDUE])->isNotEmpty();

        // 会社全体の有効応募数・課金対象数（課金判定・無料枠計算に使用）
        $companyValidCount    = \App\Models\Application::whereHas('job', fn($q) => $q->where('contact_email', $job->contact_email))
            ->where('is_valid', true)->count();
        $companyBillableCount = \App\Models\Application::whereHas('job', fn($q) => $q->where('contact_email', $job->contact_email))
            ->where('is_billable', true)->count();
        $freeQuotaRemaining   = max(0, 3 - $companyValidCount);

        // この求人単体の応募内訳（表示用）
        $jobValidCount    = $job->applications()->where('is_valid', true)->count();
        $jobInvalidCount  = $job->applications()->where('is_valid', false)->count();
        $jobBillableCount = $job->applications()->where('is_billable', true)->count();

        return view('jobs.manage', compact(
            'job', 'areas', 'jobTypes', 'employmentTypes', 'conditions', 'appeals',
            'selectedAreas', 'selectedJobTypes', 'selectedEmploymentTypes',
            'selectedConditions', 'selectedAppeals', 'applications',
            'billingSummaries', 'trialEnded', 'hasUnpaid', 'hasOverdue',
            'companyValidCount', 'companyBillableCount', 'freeQuotaRemaining',
            'jobValidCount', 'jobInvalidCount', 'jobBillableCount'
        ));
    }

    public function update(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $request->validate([
            'company_name'       => ['required', 'string', 'max:100'],
            'title'              => ['required', 'string', 'max:60'],
            'areas'              => ['required', 'array', 'min:1'],
            'areas.*'            => ['integer', 'exists:master_areas,id'],
            'job_types'          => ['required', 'array', 'min:1'],
            'job_types.*'        => ['integer', 'exists:master_job_types,id'],
            'employment_types'   => ['required', 'array', 'min:1'],
            'employment_types.*' => ['integer', 'exists:master_employment_types,id'],
            'conditions'         => ['required', 'array', 'min:1'],
            'conditions.*'       => ['integer', 'exists:master_conditions,id'],
            'appeals'            => ['required', 'array', 'min:1'],
            'appeals.*'          => ['integer', 'exists:master_appeals,id'],
            'free_text'          => ['nullable', 'string', 'max:2000'],
            'salary_type'        => ['required', 'in:monthly,hourly,daily,yearly,other'],
            'salary_min'         => ['required', 'integer', 'min:1'],
            'salary_max'         => ['nullable', 'integer', 'min:1', 'gte:salary_min'],
            'salary_note'        => ['nullable', 'string', 'max:500'],
            'photo'              => ['nullable', 'image', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $job) {
            $photoPath = $job->photo_path;
            if ($request->hasFile('photo')) {
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('job_photos', 'public');
            }

            $job->update([
                'company_name' => $request->company_name,
                'title'        => $request->title,
                'free_text'    => $request->free_text,
                'salary_type'  => $request->salary_type,
                'salary_min'   => $request->salary_min,
                'salary_max'   => $request->salary_max ?: null,
                'salary_note'  => $request->salary_note,
                'photo_path'   => $photoPath,
            ]);

            $job->jobAreas()->delete();
            $job->jobJobTypes()->delete();
            $job->jobEmploymentTypes()->delete();
            $job->jobConditions()->delete();
            $job->jobAppeals()->delete();

            foreach ($request->areas as $id) {
                JobArea::create(['job_id' => $job->id, 'area_id' => $id]);
            }
            foreach ($request->job_types as $id) {
                JobJobType::create(['job_id' => $job->id, 'job_type_id' => $id]);
            }
            foreach ($request->employment_types as $id) {
                JobEmploymentType::create(['job_id' => $job->id, 'employment_type_id' => $id]);
            }
            foreach ($request->conditions as $id) {
                JobCondition::create(['job_id' => $job->id, 'condition_id' => $id]);
            }
            foreach ($request->appeals as $id) {
                JobAppeal::create(['job_id' => $job->id, 'appeal_id' => $id]);
            }

            $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            app(SeoGeneratorService::class)->generate($job);
        });

        return redirect()->route('jobs.manage', ['token' => $token])->with('updated', true);
    }

    public function close(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        $job->update([
            'status'    => Job::STATUS_PAUSED,
            'paused_at' => now(),
        ]);

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
            'action'    => 'closed',
            'paused_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('jobs.manage', ['token' => $token])->with('updated', true);
    }

    public function reopen(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $expiresAt = $job->expires_at;
        if ($job->paused_at && $expiresAt) {
            $pausedSeconds = now()->diffInSeconds($job->paused_at);
            $expiresAt = $expiresAt->addSeconds($pausedSeconds);
        }

        $job->update([
            'status'     => Job::STATUS_ACTIVE,
            'paused_at'  => null,
            'expires_at' => $expiresAt,
        ]);

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
            'action'     => 'reopened',
            'expires_at' => $expiresAt?->toDateTimeString(),
        ]);

        return redirect()->route('jobs.manage', ['token' => $token])->with('updated', true);
    }

    public function continue(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        // 二重継続防止
        if ($job->continued_at) {
            return redirect()->route('jobs.manage', ['token' => $token])
                ->with('continued', true);
        }

        // 継続可能期間チェック（終了間近でない場合は無効）
        $warningDays = config('billing.continue_warning_days', 7);
        if (! $job->expires_at || now()->diffInDays($job->expires_at, false) > $warningDays) {
            return redirect()->route('jobs.manage', ['token' => $token]);
        }

        $job->update(['continued_at' => now()]);

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
            'action'       => 'continued',
            'continued_at' => now()->toDateTimeString(),
        ]);

        try {
            Mail::to(config('billing.admin_email'))->send(new JobContinuedMail($job));
            $job->update(['continue_notified_at' => now()]);
        } catch (\Exception $e) {
            Log::error("継続通知メール送信失敗 [{$job->contact_email}]: " . $e->getMessage());
        }

        return redirect()->route('jobs.manage', ['token' => $token])
            ->with('continued', true);
    }

    public function checkTrial(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = strtolower(trim($request->query('email', '')));
        return response()->json(['trial_ended' => $this->isTrialEnded($email)]);
    }

    private function isTrialEnded(string $email): bool
    {
        if (empty($email)) return false;

        $firstJob = Job::where('contact_email', $email)
            ->whereNotNull('email_verified_at')
            ->orderBy('email_verified_at')
            ->first();

        if (! $firstJob) return false;

        return ($firstJob->expires_at && now()->greaterThan($firstJob->expires_at))
            || \App\Models\Application::whereHas('job', fn($q) => $q->where('contact_email', $email))
                ->where('is_valid', true)->count() >= 3;
    }

    public function destroy(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
            'action' => 'soft_deleted',
        ]);

        $job->delete();

        return redirect('/')->with('success', '求人を完全削除しました。');
    }
}
