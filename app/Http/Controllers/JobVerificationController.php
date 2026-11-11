<?php

namespace App\Http\Controllers;

use App\Mail\AdminJobNotificationMail;
use App\Mail\JobManageLinkMail;
use App\Models\AuditLog;
use App\Models\Job;
use App\Models\Setting;
use App\Services\SeoGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobVerificationController extends Controller
{
    public function verify(string $verificationToken, SeoGeneratorService $seoGenerator)
    {
        $job = Job::where('email_verification_token', $verificationToken)
            ->where('status', Job::STATUS_PENDING)
            ->first();

        // 使用済み(2回目のクリック)・無効なリンクは 404 ではなく案内ページへ(編集用リンクの再送導線)
        if (! $job) {
            return response()->view('jobs.verify_invalid', [], 404);
        }

        DB::transaction(function () use ($job, $seoGenerator) {
            $job->update([
                'email_verified_at'       => now(),
                'email_verification_token' => null,
            ]);

            $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            $seoGenerator->generate($job);

            $isMonitor     = $job->is_monitor || now()->lte(Setting::monitorCutoffDate());
            $monitorEndsAt = $isMonitor ? now()->addMonths(Setting::monitorMonths()) : null;

            $job->update([
                'status'          => Job::STATUS_ACTIVE,
                'is_monitor'      => $isMonitor,
                'monitor_ends_at' => $monitorEndsAt,
                // 自社求人は掲載期限なし(掲載者が停止するまで掲載)。募集継続は3か月ごとの確認メールで確認する
            ]);

            AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
                'contact_email' => $job->contact_email,
                'status'        => $job->status,
            ]);
        });

        // 編集用リンクを掲載者にメールで送る(認証直後の画面を閉じると管理URLが分からなくなるため)
        try {
            Mail::to($job->contact_email)->send(new JobManageLinkMail($job));
        } catch (\Throwable $e) {
            Log::error('編集用リンクメール送信失敗(認証後): ' . $e->getMessage(), ['job_id' => $job->id]);
        }

        // 管理者通知の失敗で掲載者に500を見せない(公開処理はコミット済み)
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AdminJobNotificationMail($job));
            } catch (\Throwable $e) {
                Log::error('管理者通知メール送信失敗(認証後): ' . $e->getMessage(), ['job_id' => $job->id]);
            }
        }

        return redirect()->route('jobs.manage', ['token' => $job->manage_token])
            ->with('verified', true);
    }
}
