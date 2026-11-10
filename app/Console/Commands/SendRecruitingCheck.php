<?php

namespace App\Console\Commands;

use App\Mail\RecruitingCheckMail;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * 自社求人に3か月ごとに募集継続の確認メールを送る(自社求人は掲載期限なしのため、募集終了の求人が残り続けないように)
 * 返信がなくても掲載は継続する
 */
class SendRecruitingCheck extends Command
{
    protected $signature   = 'jobs:send-recruiting-check {--dry-run : 送信せず対象だけ表示}';
    protected $description = '掲載中の自社求人に3か月ごとの募集状況確認メールを送る';

    private const INTERVAL_MONTHS = 3;

    public function handle(): int
    {
        $threshold = now()->subMonths(self::INTERVAL_MONTHS);

        $jobs = Job::query()
            ->where('status', Job::STATUS_ACTIVE)
            ->whereNotNull('email_verified_at')
            ->whereNotNull('contact_email')
            ->where(fn($q) => $q->whereNull('source')->orWhere('source', '<>', 'hellowork'))
            ->where(fn($q) => $q
                ->where('recruiting_checked_at', '<=', $threshold)
                ->orWhere(fn($w) => $w->whereNull('recruiting_checked_at')->where('email_verified_at', '<=', $threshold)))
            ->get();

        $bot = 'bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless';
        foreach ($jobs as $job) {
            $views = DB::table('lp_views')->where('job_id', $job->id)->where('viewed_at', '>=', $threshold)
                ->where(fn($q) => $q->whereNull('user_agent')->orWhere('user_agent', 'not regexp', $bot))->count();
            $applications = $job->applications()->where('applied_at', '>=', $threshold)->count();

            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$job->id} {$job->company_name} 閲覧{$views} 応募{$applications}");
                continue;
            }

            try {
                Mail::to($job->contact_email)->send(new RecruitingCheckMail($job, $views, $applications));
                $job->update(['recruiting_checked_at' => now()]);
                $this->info("送信: {$job->id} {$job->company_name}");
            } catch (\Throwable $e) {
                Log::error("募集状況確認メール送信失敗 [{$job->id}]: " . $e->getMessage());
                $this->error("失敗: {$job->id} " . $e->getMessage());
            }
        }

        $this->info('対象 ' . $jobs->count() . '件');
        return self::SUCCESS;
    }
}
