<?php

namespace App\Http\Controllers;

use App\Mail\JobManageLinkMail;
use App\Mail\ManageLoginLinksMail;
use App\Mail\JobVerificationMail;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        // 同じアドレスへの送信は10分に1回まで(他人のアドレスを入力して事業所にメールを連打する嫌がらせ対策)
        // 登録の有無に関わらず同じ扱いにして、登録有無を推測できないようにする
        if (! Cache::add('manage_link_sent:' . sha1($email), 1, now()->addMinutes(10))) {
            return back()->with('resent', true);
        }

        // 公開中・停止中などの求人 → そのアドレスの全求人の管理ページリンクを1通で送る
        $jobs = Job::where('contact_email', $email)
            ->whereIn('status', [Job::STATUS_ACTIVE, Job::STATUS_PAUSED, Job::STATUS_CLOSED, Job::STATUS_EXPIRED])
            ->orderByDesc('created_at')
            ->get();
        if ($jobs->isNotEmpty()) {
            Mail::to($email)->send(new ManageLoginLinksMail($jobs));
        }

        // メール確認が済んでいない求人 → 確認メールを再送
        $pending = Job::where('contact_email', $email)->where('status', Job::STATUS_PENDING)->latest()->first();
        if ($pending) {
            if (! $pending->email_verification_token) {
                $pending->update(['email_verification_token' => Str::random(64)]);
            }
            Mail::to($email)->send(new JobVerificationMail($pending));
        }

        // メールが存在しない場合も同じレスポンスを返す（情報漏洩防止）
        return back()->with('resent', true);
    }
}
