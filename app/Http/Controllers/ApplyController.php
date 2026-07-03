<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FormApplicationDetail;
use App\Models\FormDesiredAppeal;
use App\Models\FormDesiredCondition;
use App\Models\FormDesiredEmploymentType;
use App\Models\FormDesiredJobType;
use App\Models\Job;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use App\Services\ApplicationValidationService;
use App\Services\LineMatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplyController extends Controller
{
    private function findActiveJob(string $token): Job
    {
        $job = Job::with([
            'jobAreas.area',
            'jobJobTypes.jobType',
            'jobEmploymentTypes.employmentType',
            'jobConditions.condition',
            'jobAppeals.appeal',
        ])->where('token', $token)->where('status', 'active')->firstOrFail();

        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(404);
        }
        return $job;
    }

    private function parseConditions(Request $request): array
    {
        return [
            'area_ids'            => array_values(array_filter(array_map('intval', (array) $request->input('area_ids', [])))),
            'job_type_ids'        => array_values(array_filter(array_map('intval', (array) $request->input('job_type_ids', [])))),
            'employment_type_ids' => array_values(array_filter(array_map('intval', (array) $request->input('employment_type_ids', [])))),
            'condition_ids'       => array_values(array_filter(array_map('intval', (array) $request->input('condition_ids', [])))),
            'appeal_ids'          => array_values(array_filter(array_map('intval', (array) $request->input('appeal_ids', [])))),
        ];
    }

    private function hasConditions(array $conditions): bool
    {
        return !empty($conditions['area_ids'])
            || !empty($conditions['job_type_ids'])
            || !empty($conditions['employment_type_ids'])
            || !empty($conditions['condition_ids']);
    }

    // STEP1: 初期表示
    public function show(Request $request, string $token)
    {
        $job        = $this->findActiveJob($token);
        $conditions = $this->parseConditions($request);
        $via        = $request->input('via') === 'line' ? 'line' : null;

        if ($this->hasConditions($conditions)) {
            if ($via === 'line') {
                return redirect()->route('line.entry', array_merge(
                    ['token' => $job->token],
                    $this->conditionsToQuery($conditions),
                ));
            }
            // 検索条件あり → 確認画面
            return view('lp.apply', [
                'job'        => $job,
                'step'       => 'confirm',
                'conditions' => $conditions,
                'conditionLabels' => $this->buildConditionLabels($conditions),
                'via'        => $via,
            ]);
        }

        // 検索条件なし → ミニフォーム（募集要項確認 + 条件選択）
        return view('lp.apply', [
            'job'             => $job,
            'step'            => 'select',
            'areas'           => MasterArea::active()->where('prefecture', '沖縄県')->orderBy('sort_order')->get(),
            'jobTypes'        => MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category'),
            'employmentTypes' => MasterEmploymentType::active()->orderBy('sort_order')->get(),
            'via'             => $via,
        ]);
    }

    // STEP2: 判定
    public function judge(Request $request, string $token, LineMatchService $matchService)
    {
        $job        = $this->findActiveJob($token);
        $conditions = $this->parseConditions($request);
        $via        = $request->input('via') === 'line' ? 'line' : null;

        $mismatches = $matchService->getMismatches($job, $conditions);

        if (empty($mismatches)) {
            if ($via === 'line') {
                return redirect()->route('line.entry', array_merge(
                    ['token' => $job->token],
                    $this->conditionsToQuery($conditions),
                ));
            }
            return view('lp.apply', [
                'job'        => $job,
                'step'       => 'form',
                'conditions' => $conditions,
                'via'        => $via,
            ]);
        }

        return view('lp.apply', [
            'job'          => $job,
            'step'         => 'mismatch',
            'mismatches'   => $mismatches,
            'conditions'   => $conditions,
            'alternatives' => $this->findAlternativeJobs($job, $conditions),
            'via'          => $via,
        ]);
    }

    private function conditionsToQuery(array $conditions): array
    {
        $query = [];
        foreach (['area_ids', 'job_type_ids', 'employment_type_ids', 'condition_ids', 'appeal_ids'] as $key) {
            if (!empty($conditions[$key])) {
                $query[$key] = $conditions[$key];
            }
        }
        return $query;
    }

    // STEP3: 応募保存
    public function store(Request $request, string $token, ApplicationValidationService $validationService, LineMatchService $matchService)
    {
        $job        = $this->findActiveJob($token);
        $conditions = $this->parseConditions($request);

        // サーバーサイドでも判定（改ざん防止）
        if (!$matchService->isMatch($job, $conditions) && $this->hasConditions($conditions)) {
            return redirect()->route('lp.apply', ['token' => $token])
                ->withErrors(['match' => '条件が一致しないため応募できませんでした。']);
        }

        $request->validate([
            'applicant_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^[0-9]{10,11}$/'],
        ], [
            'applicant_name.required' => 'お名前を入力してください。',
            'phone.required'          => '電話番号を入力してください。',
            'phone.regex'             => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
        ]);

        DB::transaction(function () use ($request, $job, $conditions, $validationService) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_FORM,
                'applicant_name'   => $request->applicant_name,
                'phone'            => $request->phone,
                'email'            => null,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            $validationService->validate($application);
            $application->save();

            FormApplicationDetail::create([
                'application_id' => $application->id,
                'desired_area_id' => $conditions['area_ids'][0] ?? null,
                'appeal_message' => null,
                'ip_address'     => $request->ip(),
                'user_agent'     => substr($request->userAgent() ?? '', 0, 500),
            ]);

            foreach ($conditions['job_type_ids'] as $id) {
                FormDesiredJobType::create(['application_id' => $application->id, 'job_type_id' => $id]);
            }
            foreach ($conditions['employment_type_ids'] as $id) {
                FormDesiredEmploymentType::create(['application_id' => $application->id, 'employment_type_id' => $id]);
            }
            foreach ($conditions['condition_ids'] as $id) {
                FormDesiredCondition::create(['application_id' => $application->id, 'condition_id' => $id]);
            }
            foreach ($conditions['appeal_ids'] as $id) {
                FormDesiredAppeal::create(['application_id' => $application->id, 'appeal_id' => $id]);
            }

            $this->notifyEmployer($job, $application, $conditions);
        });

        return redirect()->route('lp.apply.thanks', ['token' => $token]);
    }

    public function thanks(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        return view('lp.thanks', compact('job'));
    }

    // 代替求人（同エリア→同職種→その他の順で3件）
    private function findAlternativeJobs(Job $job, array $conditions): \Illuminate\Support\Collection
    {
        $base = Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where('id', '!=', $job->id)
            ->with(['jobAreas.area', 'jobJobTypes.jobType']);

        $results = collect();

        // 同エリア＋同職種
        if (!empty($conditions['area_ids']) && !empty($conditions['job_type_ids'])) {
            $results = (clone $base)
                ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $conditions['area_ids']))
                ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $conditions['job_type_ids']))
                ->limit(3)->get();
        }

        // 同エリアのみ
        if ($results->count() < 3 && !empty($conditions['area_ids'])) {
            $ids = $results->pluck('id')->push($job->id)->toArray();
            $more = (clone $base)
                ->whereNotIn('id', $ids)
                ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $conditions['area_ids']))
                ->limit(3 - $results->count())->get();
            $results = $results->merge($more);
        }

        // 不足分を同職種で補完
        if ($results->count() < 3 && !empty($conditions['job_type_ids'])) {
            $ids = $results->pluck('id')->push($job->id)->toArray();
            $more = (clone $base)
                ->whereNotIn('id', $ids)
                ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $conditions['job_type_ids']))
                ->limit(3 - $results->count())->get();
            $results = $results->merge($more);
        }

        // それでも不足なら新着で補完
        if ($results->count() < 3) {
            $ids  = $results->pluck('id')->push($job->id)->toArray();
            $more = (clone $base)->whereNotIn('id', $ids)->latest()->limit(3 - $results->count())->get();
            $results = $results->merge($more);
        }

        return $results->take(3);
    }

    private function buildConditionLabels(array $conditions): array
    {
        $labels = [];

        if (!empty($conditions['area_ids'])) {
            $labels['areas'] = MasterArea::whereIn('id', $conditions['area_ids'])->pluck('name')->toArray();
        }
        if (!empty($conditions['job_type_ids'])) {
            $labels['jobTypes'] = MasterJobType::whereIn('id', $conditions['job_type_ids'])->pluck('name')->toArray();
        }
        if (!empty($conditions['employment_type_ids'])) {
            $labels['employmentTypes'] = MasterEmploymentType::whereIn('id', $conditions['employment_type_ids'])->pluck('name')->toArray();
        }
        if (!empty($conditions['condition_ids'])) {
            $labels['conditions'] = MasterCondition::whereIn('id', $conditions['condition_ids'])->pluck('name')->toArray();
        }

        return $labels;
    }

    private function notifyEmployer(Job $job, Application $application, array $conditions): void
    {
        $recipients = $job->notificationEmails();
        if (empty($recipients)) return;

        $areaName    = !empty($conditions['area_ids'])
            ? MasterArea::whereIn('id', $conditions['area_ids'])->pluck('name')->implode('・')
            : '未選択';
        $jobTypes    = !empty($conditions['job_type_ids'])
            ? MasterJobType::whereIn('id', $conditions['job_type_ids'])->pluck('name')->map(fn($n) => "・{$n}")->implode("\n")
            : null;
        $empTypes    = !empty($conditions['employment_type_ids'])
            ? MasterEmploymentType::whereIn('id', $conditions['employment_type_ids'])->pluck('name')->map(fn($n) => "・{$n}")->implode("\n")
            : null;
        $conds       = !empty($conditions['condition_ids'])
            ? MasterCondition::whereIn('id', $conditions['condition_ids'])->pluck('name')->map(fn($n) => "・{$n}")->implode("\n")
            : null;
        $appeals     = !empty($conditions['appeal_ids'])
            ? \App\Models\MasterAppeal::whereIn('id', $conditions['appeal_ids'])->pluck('name')->implode('、')
            : null;

        $validityNote = $application->is_valid
            ? '【有効応募】'
            : "【無効応募：{$application->invalidReasonLabel()}】";

        try {
            Mail::raw(
                implode("\n", array_filter([
                    "【求人応募通知】新しいWeb応募が届きました {$validityNote}",
                    "",
                    "■ 応募者情報",
                    "氏名：{$application->applicant_name}",
                    "電話：{$application->phone}",
                    "",
                    "■ 応募求人",
                    $job->seo_title ?: $job->title,
                    "",
                    "■ 応募者の希望条件",
                    "希望エリア：{$areaName}",
                    $jobTypes    ? "希望職種：\n{$jobTypes}"       : null,
                    $empTypes    ? "希望雇用形態：\n{$empTypes}"    : null,
                    $conds       ? "希望勤務条件：\n{$conds}"       : null,
                    $appeals     ? "重視ポイント：{$appeals}"       : null,
                    "",
                    "■ 求人管理ページ",
                    url('/jobs/' . $job->token),
                ])),
                fn($message) => $message
                    ->to($recipients)
                    ->subject('【求人応募通知】新しいWeb応募が届きました')
            );
        } catch (\Exception $e) {
            Log::warning('応募通知メール送信失敗: ' . $e->getMessage());
        }
    }
}
