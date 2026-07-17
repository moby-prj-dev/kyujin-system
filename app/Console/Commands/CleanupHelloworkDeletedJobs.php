<?php

namespace App\Console\Commands;

use App\Models\Job;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupHelloworkDeletedJobs extends Command
{
    protected $signature   = 'hellowork:cleanup-deleted {--limit=50 : 1回のチェック件数}';
    protected $description = 'ハローワーク原本URLを叩き、削除済み求人をDBから消す(死活監視)';

    private const DELETED_MARKER = '該当する情報は見つかりません';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        // 最終確認(=updated_at)が古い順に取得
        $jobs = Job::where('source', 'hellowork')
            ->whereNotNull('hw_job_url')
            ->orderBy('updated_at', 'asc')
            ->limit($limit)
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('チェック対象のHW求人がありません。');
            return self::SUCCESS;
        }

        $this->info("チェック開始: {$jobs->count()}件");

        $client = new Client([
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Care Entry Verification Bot; +https://care-entry.net)',
                'Accept'     => 'text/html,application/xhtml+xml',
            ],
        ]);

        $deleted = $alive = $errors = 0;

        foreach ($jobs as $job) {
            try {
                $response = $client->get($job->hw_job_url, ['http_errors' => false]);

                if ($response->getStatusCode() !== 200) {
                    // 404等は「削除された」とみなす
                    if ($response->getStatusCode() === 404) {
                        $job->delete();
                        $deleted++;
                        $this->line("削除(404): {$job->company_name} ({$job->hw_job_no})");
                    } else {
                        $errors++;
                        $this->warn("HTTP{$response->getStatusCode()}: {$job->hw_job_no}");
                    }
                    sleep(1);
                    continue;
                }

                $body = (string) $response->getBody();

                if (str_contains($body, self::DELETED_MARKER)) {
                    $job->delete();
                    $deleted++;
                    $this->line("削除: {$job->company_name} ({$job->hw_job_no})");
                } else {
                    // touch()でupdated_atを更新(=最終確認済みマーク)
                    $job->touch();
                    $alive++;
                }

                // HWサーバー負荷軽減
                sleep(1);
            } catch (\Throwable $e) {
                $errors++;
                Log::warning('HW cleanup-deleted エラー', [
                    'hw_job_no' => $job->hw_job_no,
                    'error'     => $e->getMessage(),
                ]);
                $this->warn("エラー: {$job->hw_job_no} - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("完了: 削除{$deleted}件 / 生存{$alive}件 / エラー{$errors}件");

        return self::SUCCESS;
    }
}
