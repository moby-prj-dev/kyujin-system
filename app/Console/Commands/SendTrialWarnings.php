<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingWarningMail;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTrialWarnings extends Command
{
    protected $signature   = 'billing:send-trial-warnings';
    protected $description = '無料トライアル終了7日前の警告メールを送信する';

    public function handle(): void
    {
        // 企業ごとに最初に有効化された求人票の expires_at を基準にする
        $companies = Job::query()
            ->selectRaw('contact_email, MIN(email_verified_at) as first_activated_at, MIN(expires_at) as trial_ends_at')
            ->whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->where('is_monitor', false)
            ->groupBy('contact_email')
            ->havingRaw('trial_ends_at BETWEEN ? AND ?', [now(), now()->addDays(7)])
            ->get();

        foreach ($companies as $company) {
            // 同メールの最初の求人票で送付済みかチェック
            $firstJob = Job::where('contact_email', $company->contact_email)
                ->whereNotNull('email_verified_at')
                ->orderBy('email_verified_at')
                ->first();

            if (! $firstJob || $firstJob->trial_warning_sent_at) {
                continue;
            }

            $companyName = Job::where('contact_email', $company->contact_email)
                ->latest('email_verified_at')->value('company_name');

            try {
                Mail::to($company->contact_email)->send(
                    new TrialEndingWarningMail($companyName, $company->contact_email, $company->trial_ends_at)
                );
                $firstJob->update(['trial_warning_sent_at' => now()]);
                $this->info("警告メール送信: {$company->contact_email}");
                Log::info("トライアル終了警告メール送信: {$company->contact_email}");
            } catch (\Exception $e) {
                $this->error("送信失敗: {$company->contact_email} - " . $e->getMessage());
                Log::error("トライアル終了警告メール送信失敗 [{$company->contact_email}]: " . $e->getMessage());
            }
        }

        $this->info('SendTrialWarnings 完了');
    }
}
