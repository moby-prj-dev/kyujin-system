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
        $monitorCutoff    = \App\Models\Setting::monitorCutoffDate();

        return view('jobs.create', compact(
            'areas', 'jobTypes', 'employmentTypes', 'conditions', 'appeals',
            'agreementText', 'agreementVersion', 'monitorCutoff'
        ));
    }

    public function store(StoreJobRequest $request)
    {
        // prepareForValidation で正規化済み
        $email = $request->contact_email;
        $phone = $request->contact_phone;

        $activeStatuses = [Job::STATUS_PENDING, Job::STATUS_DRAFT, Job::STATUS_ACTIVE, Job::STATUS_PAUSED];

        // 同じメール or 電話番号の既存求人を取得(スタンダードプランなら最大3件まで許可)
        $existingJobs = Job::where(function ($q) use ($email, $phone) {
                $q->where('contact_email', $email)->orWhere('contact_phone', $phone);
            })
            ->whereIn('status', $activeStatuses)
            ->get();

        $existingCount = $existingJobs->count();
        $isStandardAccount = $existingJobs->contains(fn($j) => $j->isStandard());
        // プラン決定順:
        // 1. 既存の求人がスタンダード → 新規求人もスタンダードに揃える(アカウント=事業所単位で管理)
        // 2. 既存がなくフォームで選択されたプラン → 選択プランを尊重
        // 3. デフォルト → basic
        $selectedPlan = $request->input('plan', 'basic') === 'standard' ? Job::PLAN_STANDARD : Job::PLAN_BASIC;
        $inheritPlan = $isStandardAccount ? Job::PLAN_STANDARD : $selectedPlan;
        // ベーシック契約が既に存在する場合(=isStandardAccount=false かつ existingCount>0)は、
        // 新規求人がスタンダード希望でも "同じ連絡先の全求人の同期更新" が必要になり複雑。
        // シンプルに: 既存契約ありならプラン変更は管理URL経由でお願いする方針
        $maxAllowed  = $isStandardAccount ? Job::STANDARD_MAX_JOBS : 1;

        if ($existingCount >= $maxAllowed) {
            // ベーシック: 1件以上 → 従来通り重複エラーページへ
            // スタンダード: 3件を超えている → 上限メッセージ
            $emailMatch = $existingJobs->contains(fn($j) => $j->contact_email === $email);
            $phoneMatch = $existingJobs->contains(fn($j) => $j->contact_phone === $phone);
            $pattern = ($emailMatch && $phoneMatch) ? 'both' : ($emailMatch ? 'email' : 'phone');

            if ($isStandardAccount) {
                return back()->withInput()->withErrors([
                    'contact_email' => 'スタンダードプランの掲載上限(3件)に達しています。既存の求人を停止するか、サポートまでお問い合わせください。',
                ]);
            }

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

        DB::transaction(function () use ($request, $email, $inheritPlan, &$job) {
            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('job_photos', 'public')
                : null;

            $job = Job::create([
                'company_name'             => $request->company_name,
                'status'                   => Job::STATUS_PENDING,
                'plan'                     => $inheritPlan,
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

        // 管理者にも仮登録の通知
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new \App\Mail\AdminJobPendingMail($job));
            } catch (\Throwable $e) {
                Log::warning('管理者への申込通知メール送信失敗: ' . $e->getMessage());
            }
        }

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

        $applications = $job->applications()->with('job')->orderByDesc('applied_at')->paginate(20);

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

        $monitorCutoff = \App\Models\Setting::monitorCutoffDate();

        // スタンダードプラン向け 応募データ分析(直近6ヶ月)
        $analytics = null;
        if ($job->isStandard()) {
            $analytics = $this->buildJobAnalytics($job);
        }

        return view('jobs.manage', compact(
            'job', 'areas', 'jobTypes', 'employmentTypes', 'conditions', 'appeals',
            'selectedAreas', 'selectedJobTypes', 'selectedEmploymentTypes',
            'selectedConditions', 'selectedAppeals', 'applications',
            'billingSummaries', 'trialEnded', 'hasUnpaid', 'hasOverdue',
            'companyValidCount', 'companyBillableCount', 'freeQuotaRemaining',
            'jobValidCount', 'jobInvalidCount', 'jobBillableCount',
            'monitorCutoff', 'analytics'
        ));
    }

    private function buildJobAnalytics(Job $job): array
    {
        $months = collect();
        $labels = [];
        $counts = [];
        $validCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end   = (clone $start)->endOfMonth();
            $labels[] = $start->format('Y/n月');
            $counts[]      = $job->applications()->whereBetween('applied_at', [$start, $end])->count();
            $validCounts[] = $job->applications()->whereBetween('applied_at', [$start, $end])->where('is_valid', true)->count();
        }

        $lineCount = $job->applications()->where('application_type', \App\Models\Application::TYPE_LINE)->count();
        $webCount  = $job->applications()->where('application_type', \App\Models\Application::TYPE_FORM)->count();

        return [
            'labels'       => $labels,
            'counts'       => $counts,
            'valid_counts' => $validCounts,
            'total'        => array_sum($counts),
            'valid_total'  => array_sum($validCounts),
            'line_count'   => $lineCount,
            'web_count'    => $webCount,
        ];
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

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CLOSED, AuditLog::ACTOR_SYSTEM, [
            'paused_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('jobs.manage', ['token' => $token])->with('updated', true);
    }

    public function updatePlan(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $request->validate([
            'plan' => ['required', 'in:basic,standard'],
        ]);

        $newPlan = $request->input('plan');
        // 同じ連絡先の全求人のプランを同期(=事業所アカウント単位で管理)
        Job::where(function ($q) use ($job) {
            $q->where('contact_email', $job->contact_email)->orWhere('contact_phone', $job->contact_phone);
        })->update(['plan' => $newPlan]);

        $msg = $newPlan === Job::PLAN_STANDARD
            ? 'スタンダードプランに切替しました。LINE応募・優先上位表示・分析画面がご利用いただけます。'
            : 'ベーシックプランに戻しました。';

        return redirect()->route('jobs.manage', ['token' => $token])->with('plan_updated', $msg);
    }

    public function updateNotifications(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        if (!$job->isStandard()) {
            return redirect()->route('jobs.manage', ['token' => $token])
                ->withErrors(['secondary_emails' => '応募通知の追加宛先はスタンダードプラン限定機能です。']);
        }

        $request->validate([
            'secondary_emails'   => ['nullable', 'array', 'max:5'],
            'secondary_emails.*' => ['nullable', 'email', 'max:200'],
        ]);

        $emails = array_values(array_unique(array_filter(
            array_map('trim', (array) $request->input('secondary_emails', [])),
            fn($e) => $e !== '' && $e !== $job->contact_email
        )));

        $job->update([
            'secondary_emails' => empty($emails) ? null : $emails,
        ]);

        return redirect()->route('jobs.manage', ['token' => $token])->with('updated', true);
    }

    public function reopen(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $expiresAt = $job->expires_at;
        if ($job->paused_at && $expiresAt && $expiresAt->isFuture()) {
            // 通常のケース: 一時停止中の期間を延長
            $pausedSeconds = now()->diffInSeconds($job->paused_at);
            $expiresAt = $expiresAt->addSeconds($pausedSeconds);
        } elseif (!$expiresAt || $expiresAt->isPast()) {
            // 期限切れ求人の再開: 掲載期間を3ヶ月新規化
            $expiresAt = now()->addMonths(3);
        }

        $job->update([
            'status'     => Job::STATUS_ACTIVE,
            'paused_at'  => null,
            'expires_at' => $expiresAt,
        ]);

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_REOPENED, AuditLog::ACTOR_SYSTEM, [
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

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CONTINUED, AuditLog::ACTOR_SYSTEM, [
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

        // 永久無料企業
        $isPermanentlyFree = Job::where('contact_email', $email)
            ->whereNotNull('email_verified_at')
            ->where('is_permanently_free', true)
            ->exists();
        if ($isPermanentlyFree) return false;

        // monitor_ends_at が設定されていれば、解除後も期限で判定
        $monitorJob = Job::where('contact_email', $email)
            ->whereNotNull('email_verified_at')
            ->whereNotNull('monitor_ends_at')
            ->orderBy('email_verified_at')
            ->first();

        if ($monitorJob) {
            if ($monitorJob->monitor_ends_at->isPast()) return true;
            $validCount = \App\Models\Application::whereHas('job', fn($q) => $q->where('contact_email', $email))
                ->where('is_valid', true)->count();
            return $validCount >= 3;
        }

        // 通常企業：初日から課金
        return Job::where('contact_email', $email)
            ->whereNotNull('email_verified_at')
            ->exists();
    }

    public function destroy(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_DELETED, AuditLog::ACTOR_SYSTEM, []);

        $job->delete();

        return redirect('/')->with('success', '求人を完全削除しました。');
    }
}
