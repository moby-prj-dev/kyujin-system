<?php

namespace App\Console\Commands;

use App\Services\EstatService;
use Illuminate\Console\Command;

class FetchEstatWages extends Command
{
    protected $signature   = 'stats:fetch-estat {--prefecture=沖縄県}';
    protected $description = 'e-Stat 賃金センサスから都道府県別の職種別賃金データを取得しキャッシュ';

    public function handle(EstatService $service): int
    {
        $prefecture = (string) $this->option('prefecture');

        if (!$service->isConfigured()) {
            $this->error('ESTAT_APP_ID または ESTAT_WAGE_STATS_DATA_ID が未設定です。.env を確認してください。');
            return self::FAILURE;
        }

        $this->info("e-Stat 取得開始: prefecture={$prefecture}");

        try {
            $saved = $service->fetchAndCachePrefectureWages($prefecture);
            $this->info("完了: {$saved} 件保存しました。");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('失敗: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
