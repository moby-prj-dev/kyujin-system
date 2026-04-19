<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
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
use Illuminate\Support\Facades\Storage;

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
        DB::transaction(function () use ($request, &$job) {
            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('job_photos', 'public')
                : null;

            $job = Job::create([
                'status'        => Job::STATUS_DRAFT,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'free_text'     => $request->free_text,
                'photo_path'    => $photoPath,
                'title'         => '（生成中）',
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

            $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            $this->seoGenerator->generate($job);

            $job->update([
                'status'     => Job::STATUS_ACTIVE,
                'expires_at' => now()->addMonths(3),
            ]);

            AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
                'contact_email' => $job->contact_email,
                'status'        => $job->status,
            ]);
            AuditLog::record(AuditLog::ENTITY_AGREEMENT, $job->id, AuditLog::ACTION_AGREEMENT_SAVED, AuditLog::ACTOR_SYSTEM, [
                'agreement_text_version' => config('billing.agreement_version'),
                'agreed_ip'              => $request->ip(),
            ]);
            AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_LP_GENERATED, AuditLog::ACTOR_SYSTEM, [
                'seo_title'        => $job->seo_title,
                'meta_description' => $job->meta_description,
            ]);
        });

        return redirect()->route('jobs.manage', ['token' => $job->token]);
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

        return view('jobs.manage', compact(
            'job', 'areas', 'jobTypes', 'employmentTypes', 'conditions', 'appeals',
            'selectedAreas', 'selectedJobTypes', 'selectedEmploymentTypes',
            'selectedConditions', 'selectedAppeals', 'applications'
        ));
    }

    public function update(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $request->validate([
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
            'contact_email'      => ['required', 'email'],
            'contact_phone'      => ['required', 'regex:/^[0-9]{10,11}$/'],
            'free_text'          => ['nullable', 'string', 'max:2000'],
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
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'free_text'     => $request->free_text,
                'photo_path'    => $photoPath,
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
            'status'    => Job::STATUS_CLOSED,
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
