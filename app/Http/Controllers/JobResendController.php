<?php

namespace App\Http\Controllers;

use App\Mail\JobManageLinkMail;
use App\Mail\JobVerificationMail;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class JobResendController extends Controller
{
    public function show()
    {
        return view('jobs.resend');
    }

    public function resend(Request $request)
    {
        $request->validate(['contact_email' => ['required', 'email']]);

        $email = strtolower(trim($request->contact_email));
        $type  = $request->input('type', 'verification'); // 'verification' or 'manage'

        $job = Job::where('contact_email', $email)
            ->whereIn('status', [Job::STATUS_PENDING, Job::STATUS_DRAFT, Job::STATUS_ACTIVE, Job::STATUS_PAUSED])
            ->latest()
            ->first();

        if ($job) {
            if ($type === 'manage' && in_array($job->status, [Job::STATUS_ACTIVE, Job::STATUS_PAUSED])) {
                Mail::to($email)->send(new JobManageLinkMail($job));
            } elseif ($type === 'verification' && $job->status === Job::STATUS_PENDING) {
                // トークンが消えていれば再生成
                if (!$job->email_verification_token) {
                    $job->update(['email_verification_token' => Str::random(64)]);
                }
                Mail::to($email)->send(new JobVerificationMail($job));
            } else {
                // ステータスに応じて適切な方を送る
                if ($job->status === Job::STATUS_PENDING) {
                    if (!$job->email_verification_token) {
                        $job->update(['email_verification_token' => Str::random(64)]);
                    }
                    Mail::to($email)->send(new JobVerificationMail($job));
                } else {
                    Mail::to($email)->send(new JobManageLinkMail($job));
                }
            }
        }

        // メールが存在しない場合も同じレスポンスを返す（情報漏洩防止）
        return back()->with('resent', true);
    }
}
