<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyBillings extends Command
{
    protected $signature   = 'billing:generate-monthly {--month= : 対象月 YYYY-MM（省略時は前月）}';
    protected $description = '月次請求サマリーを生成し請求メールを送信する';

    public function handle(BillingService $billingService): void
    {
        $month = $this->option('month') ?? now()->subMonth()->format('Y-m');
        $this->info("請求集計対象月: {$month}");

        $results = $billingService->generateAndSendMonthly($month);

        foreach ($results as $r) {
            $status = $r['status'] === 'sent' ? '✓ 送信' : '✗ 失敗';
            $this->line("{$status}: {$r['email']}");
        }

        $sent   = collect($results)->where('status', 'sent')->count();
        $failed = collect($results)->where('status', 'failed')->count();
        $this->info("完了: 送信 {$sent}件 / 失敗 {$failed}件");
        Log::info("月次請求集計完了 [{$month}]: 送信{$sent}件 失敗{$failed}件");
    }
}
