<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FormApplicationDetail;
use App\Models\FormDesiredCondition;
use App\Models\FormDesiredJobType;
use App\Models\Job;
use App\Services\ApplicationValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplyController extends Controller
{
    private function findActiveJob(string $token): Job
    {
        $job = Job::with([
            'jobConditions.condition',
            'jobJobTypes.jobType',
        ])->where('token', $token)->where('status', 'active')->firstOrFail();

        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(404);
        }
        return $job;
    }

    public function show(string $token)
    {
        $job = $this->findActiveJob($token);
        return view('lp.apply', compact('job'));
    }

    public function store(Request $request, string $token, ApplicationValidationService $validationService)
    {
        $job = $this->findActiveJob($token);

        $jobConditionIds = $job->jobConditions->pluck('condition_id')->toArray();
        $jobTypeIds      = $job->jobJobTypes->pluck('job_type_id')->toArray();

        $request->validate([
            'applicant_name'       => ['required', 'string', 'max:100'],
            'phone'                => ['required', 'regex:/^[0-9]{10,11}$/'],
            'email'                => ['required', 'email', 'max:255'],
            'appeal_message'       => ['nullable', 'string', 'max:1000'],
            'desired_conditions'   => ['required', 'array', 'min:1'],
            'desired_conditions.*' => ['integer', 'in:' . implode(',', $jobConditionIds ?: [0])],
            'desired_job_types'    => ['required', 'array', 'min:1'],
            'desired_job_types.*'  => ['integer', 'in:' . implode(',', $jobTypeIds ?: [0])],
        ], [
            'applicant_name.required'     => 'お名前を入力してください。',
            'phone.required'              => '電話番号を入力してください。',
            'phone.regex'                 => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
            'email.email'                 => '正しいメールアドレス形式で入力してください。',
            'desired_job_types.required'  => '希望職種を1つ以上選択してください。',
            'desired_job_types.min'       => '希望職種を1つ以上選択してください。',
            'desired_conditions.required' => '希望勤務条件を1つ以上選択してください。',
            'desired_conditions.min'      => '希望勤務条件を1つ以上選択してください。',
        ]);

        DB::transaction(function () use ($request, $job, $validationService) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_FORM,
                'applicant_name'   => $request->applicant_name,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            // 有効応募判定
            $validationService->validate($application);
            $application->save();

            FormApplicationDetail::create([
                'application_id' => $application->id,
                'appeal_message' => $request->appeal_message ?? '',
                'ip_address'     => $request->ip(),
                'user_agent'     => substr($request->userAgent() ?? '', 0, 500),
            ]);

            foreach ($request->input('desired_conditions', []) as $conditionId) {
                FormDesiredCondition::create([
                    'application_id' => $application->id,
                    'condition_id'   => $conditionId,
                ]);
            }

            foreach ($request->input('desired_job_types', []) as $jobTypeId) {
                FormDesiredJobType::create([
                    'application_id' => $application->id,
                    'job_type_id'    => $jobTypeId,
                ]);
            }

            $this->notifyEmployer($job, $application, $request);
        });

        return redirect()->route('lp.apply.thanks', ['token' => $token]);
    }

    public function thanks(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        return view('lp.thanks', compact('job'));
    }

    private function notifyEmployer(Job $job, Application $application, Request $request): void
    {
        if (empty($job->contact_email)) {
            return;
        }

        $conditions = $job->jobConditions
            ->whereIn('condition_id', $request->input('desired_conditions', []))
            ->map(fn($jc) => '・' . $jc->condition->name)
            ->implode("\n");

        $jobTypes = $job->jobJobTypes
            ->whereIn('job_type_id', $request->input('desired_job_types', []))
            ->map(fn($jt) => '・' . $jt->jobType->name)
            ->implode("\n");

        $validityNote = $application->is_valid
            ? '【有効応募】'
            : "【無効応募：{$application->invalidReasonLabel()}】";

        try {
            Mail::raw(
                implode("\n", array_filter([
                    "【求人応募通知】新しい応募が届きました {$validityNote}",
                    "",
                    "■ 応募者情報",
                    "氏名：{$application->applicant_name}",
                    "電話：{$application->phone}",
                    "メール：" . ($application->email ?: '未記入'),
                    $jobTypes   ? "\n■ 希望職種\n{$jobTypes}"      : null,
                    $conditions ? "\n■ 希望勤務条件\n{$conditions}" : null,
                    $application->formDetail?->appeal_message
                        ? "\n■ メッセージ\n{$application->formDetail->appeal_message}" : null,
                    "",
                    "■ 求人管理ページ",
                    url('/jobs/' . $job->token),
                ])),
                fn($message) => $message
                    ->to($job->contact_email)
                    ->subject('【求人応募通知】新しい応募が届きました')
            );
        } catch (\Exception $e) {
            Log::warning('応募通知メール送信失敗: ' . $e->getMessage());
        }
    }
}
