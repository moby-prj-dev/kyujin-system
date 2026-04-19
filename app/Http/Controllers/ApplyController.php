<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FormApplicationDetail;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApplyController extends Controller
{
    private function findActiveJob(string $token): Job
    {
        $job = Job::where('token', $token)->where('status', 'active')->firstOrFail();
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

    public function store(Request $request, string $token)
    {
        $job = $this->findActiveJob($token);

        $request->validate([
            'applicant_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^[0-9]{10,11}$/'],
            'email'          => ['nullable', 'email', 'max:255'],
            'appeal_message' => ['nullable', 'string', 'max:1000'],
        ], [
            'applicant_name.required' => 'お名前を入力してください。',
            'phone.required'          => '電話番号を入力してください。',
            'phone.regex'             => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
            'email.email'             => '正しいメールアドレス形式で入力してください。',
        ]);

        DB::transaction(function () use ($request, $job) {
            $application = Application::create([
                'job_id'           => $job->id,
                'application_type' => Application::TYPE_FORM,
                'applicant_name'   => $request->applicant_name,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'status'           => Application::STATUS_RECEIVED,
                'applied_at'       => now(),
            ]);

            FormApplicationDetail::create([
                'application_id' => $application->id,
                'appeal_message' => $request->appeal_message ?? '',
                'ip_address'     => $request->ip(),
                'user_agent'     => substr($request->userAgent() ?? '', 0, 500),
            ]);

            $this->notifyEmployer($job, $application);
        });

        return redirect()->route('lp.apply.thanks', ['token' => $token]);
    }

    public function thanks(string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();
        return view('lp.thanks', compact('job'));
    }

    private function notifyEmployer(Job $job, Application $application): void
    {
        if (empty($job->contact_email)) {
            return;
        }

        try {
            Mail::raw(
                implode("\n", [
                    "【求人応募通知】新しい応募が届きました",
                    "",
                    "■ 応募者情報",
                    "氏名：{$application->applicant_name}",
                    "電話：{$application->phone}",
                    "メール：" . ($application->email ?: '未記入'),
                    "",
                    "■ 求人管理ページ",
                    url('/jobs/' . $job->token),
                ]),
                fn($message) => $message
                    ->to($job->contact_email)
                    ->subject('【求人応募通知】新しい応募が届きました')
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('応募通知メール送信失敗: ' . $e->getMessage());
        }
    }
}
