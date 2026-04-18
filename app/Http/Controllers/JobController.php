<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Models\AuditLog;
use App\Models\BillingAgreement;
use App\Models\Job;
use App\Models\JobAppeal;
use App\Models\JobCondition;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use App\Services\SeoGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function __construct(
        private SeoGeneratorService $seoGenerator
    ) {}

    /**
     * 求人登録フォーム表示
     */
    public function create()
    {
        $areas           = MasterArea::active()->orderBy('prefecture')->orderBy('sort_order')->get()->groupBy('prefecture');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');

        $agreementText    = config('billing.agreement_text');
        $agreementVersion = config('billing.agreement_version');

        return view('jobs.create', compact(
            'areas', 'jobTypes', 'employmentTypes',
            'conditions', 'appeals',
            'agreementText', 'agreementVersion'
        ));
    }

    /**
     * 求人登録処理
     */
    public function store(StoreJobRequest $request)
    {
        DB::transaction(function () use ($request, &$job) {
            // 1. 求人レコード作成（token は Model の boot で自動生成）
            $job = Job::create([
                'area_id'            => $request->area_id,
                'job_type_id'        => $request->job_type_id,
                'employment_type_id' => $request->employment_type_id,
                'status'             => Job::STATUS_DRAFT,
                'contact_email'      => $request->contact_email,
                'contact_phone'      => $request->contact_phone,
                'title'              => '（生成中）',
            ]);

            // 2. 勤務条件を保存
            foreach ($request->conditions as $conditionId) {
                JobCondition::create([
                    'job_id'       => $job->id,
                    'condition_id' => $conditionId,
                ]);
            }

            // 3. アピールポイントを保存
            foreach ($request->appeals as $appealId) {
                JobAppeal::create([
                    'job_id'    => $job->id,
                    'appeal_id' => $appealId,
                ]);
            }

            // 4. 同意情報を保存
            BillingAgreement::create([
                'job_id'                  => $job->id,
                'agreement_flag'          => true,
                'agreement_text'          => config('billing.agreement_text'),
                'agreement_text_version'  => config('billing.agreement_version'),
                'agreed_at'               => now(),
                'agreed_ip'               => $request->ip(),
                'user_agent'              => $request->userAgent(),
            ]);

            // 5. SEOテキスト自動生成
            $job->load(['area', 'jobType', 'employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            $this->seoGenerator->generate($job);

            // 6. ステータスを公開に変更
            $job->update(['status' => Job::STATUS_ACTIVE]);

            // 7. 監査ログ
            AuditLog::record(
                AuditLog::ENTITY_JOB,
                $job->id,
                AuditLog::ACTION_JOB_CREATED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'area_id'            => $job->area_id,
                    'job_type_id'        => $job->job_type_id,
                    'employment_type_id' => $job->employment_type_id,
                    'contact_email'      => $job->contact_email,
                    'status'             => $job->status,
                ]
            );

            AuditLog::record(
                AuditLog::ENTITY_AGREEMENT,
                $job->id,
                AuditLog::ACTION_AGREEMENT_SAVED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'job_id'                 => $job->id,
                    'agreement_text_version' => config('billing.agreement_version'),
                    'agreed_ip'              => $request->ip(),
                ]
            );

            AuditLog::record(
                AuditLog::ENTITY_JOB,
                $job->id,
                AuditLog::ACTION_LP_GENERATED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'seo_title'        => $job->seo_title,
                    'meta_description' => $job->meta_description,
                ]
            );
        });

        return redirect()->route('jobs.complete', ['token' => $job->token]);
    }

    /**
     * 求人登録完了画面
     */
    public function complete(string $token)
    {
        $job = Job::scopeByToken(Job::query(), $token)->firstOrFail();
        $editUrl = route('jobs.edit', ['token' => $token]);

        return view('jobs.complete', compact('job', 'editUrl'));
    }

    /**
     * 求人編集フォーム表示
     */
    public function edit(string $token)
    {
        $job = Job::with(['jobConditions', 'jobAppeals'])
            ->where('token', $token)
            ->firstOrFail();

        $areas           = MasterArea::active()->orderBy('prefecture')->orderBy('sort_order')->get()->groupBy('prefecture');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');

        $selectedConditions = $job->jobConditions->pluck('condition_id')->toArray();
        $selectedAppeals    = $job->jobAppeals->pluck('appeal_id')->toArray();

        return view('jobs.edit', compact(
            'job', 'areas', 'jobTypes', 'employmentTypes',
            'conditions', 'appeals',
            'selectedConditions', 'selectedAppeals'
        ));
    }

    /**
     * 求人更新処理
     */
    public function update(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $request->validate([
            'area_id'            => ['required', 'integer', 'exists:master_areas,id'],
            'job_type_id'        => ['required', 'integer', 'exists:master_job_types,id'],
            'employment_type_id' => ['required', 'integer', 'exists:master_employment_types,id'],
            'conditions'         => ['required', 'array', 'min:1'],
            'conditions.*'       => ['integer', 'exists:master_conditions,id'],
            'appeals'            => ['required', 'array', 'min:1'],
            'appeals.*'          => ['integer', 'exists:master_appeals,id'],
            'contact_email'      => ['required', 'email'],
            'contact_phone'      => ['required', 'string'],
        ]);

        DB::transaction(function () use ($request, $job) {
            $job->update([
                'area_id'            => $request->area_id,
                'job_type_id'        => $request->job_type_id,
                'employment_type_id' => $request->employment_type_id,
                'contact_email'      => $request->contact_email,
                'contact_phone'      => $request->contact_phone,
            ]);

            // 条件・アピールを一旦削除して再登録
            $job->jobConditions()->delete();
            $job->jobAppeals()->delete();

            foreach ($request->conditions as $conditionId) {
                JobCondition::create(['job_id' => $job->id, 'condition_id' => $conditionId]);
            }
            foreach ($request->appeals as $appealId) {
                JobAppeal::create(['job_id' => $job->id, 'appeal_id' => $appealId]);
            }

            // SEO再生成
            $job->load(['area', 'jobType', 'employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            app(SeoGeneratorService::class)->generate($job);
        });

        return redirect()->route('jobs.complete', ['token' => $token])->with('updated', true);
    }
}
