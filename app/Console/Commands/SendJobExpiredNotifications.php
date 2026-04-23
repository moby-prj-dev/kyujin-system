<?php

namespace App\Console\Commands;

use App\Mail\JobExpiredMail;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendJobExpiredNotifications extends Command
{
    protected $signature   = 'billing:send-job-expired-notifications';
    protected $description = '掲載期限切れ求人の通知メールを送信する';

    public function handle(): void
    {
        $jobs = Job::query()
            ->whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->whereNull('expired_notified_at')
            ->where('status', Job::STATUS_ACTIVE)
            ->where('is_monitor', false)
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($jobs as $job) {
            try {
                Mail::to($job->contact_email)->send(
                    new JobExpiredMail($job->company_name, $job->contact_email, $job->title)
                );
                $job->update(['expired_notified_at' => now(), 'status' => Job::STATUS_EXPIRED]);
                $this->info("期限切れ通知送信: {$job->contact_email} [{$job->title}]");
                Log::info("掲載期限切れ通知メール送信: {$job->contact_email}", ['job_id' => $job->id]);
            } catch (\Exception $e) {
                $this->error("送信失敗: {$job->contact_email} - " . $e->getMessage());
                Log::error("掲載期限切れ通知送信失敗 [{$job->contact_email}]: " . $e->getMessage());
            }
        }

        $this->info('SendJobExpiredNotifications 完了');
    }
}
