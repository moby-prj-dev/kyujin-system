<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ListEstatTables extends Command
{
    protected $signature   = 'stats:list-estat {--stats-code=00450091 : 統計コード(デフォルト: 賃金構造基本統計調査)} {--keyword= : タイトルで絞り込みするキーワード}';
    protected $description = 'e-Stat の統計表一覧を取得して statsDataId を探す';

    public function handle(): int
    {
        $appId = (string) config('services.estat.app_id');
        if (empty($appId)) {
            $this->error('ESTAT_APP_ID が未設定です。');
            return self::FAILURE;
        }

        $statsCode = (string) $this->option('stats-code');
        $keyword   = (string) $this->option('keyword');
        $baseUrl   = (string) config('services.estat.base_url');

        $this->info("統計表一覧を取得: statsCode={$statsCode}");

        $client = new Client(['timeout' => 30]);
        $response = $client->get($baseUrl . '/getStatsList', [
            'query' => array_filter([
                'appId'      => $appId,
                'statsCode'  => $statsCode,
                'searchWord' => $keyword ?: null,
                'lang'       => 'J',
                'limit'      => 50,
            ]),
        ]);

        $json = json_decode($response->getBody()->getContents(), true);

        $result = $json['GET_STATS_LIST']['RESULT'] ?? null;
        if (!$result || (int) ($result['STATUS'] ?? 1) !== 0) {
            $this->error('API エラー: ' . ($result['ERROR_MSG'] ?? '不明'));
            return self::FAILURE;
        }

        $tables = $json['GET_STATS_LIST']['DATALIST_INF']['TABLE_INF'] ?? [];
        // 単一の場合は配列でラップしない場合あり
        if (isset($tables['@id'])) $tables = [$tables];

        if (empty($tables)) {
            $this->warn('該当する統計表が見つかりませんでした。');
            return self::SUCCESS;
        }

        $this->line("見つかった統計表: " . count($tables) . "件");
        $this->newLine();

        $rows = [];
        foreach ($tables as $t) {
            $id    = $t['@id'] ?? '';
            $title = $t['TITLE']['$'] ?? ($t['TITLE'] ?? '');
            $surveyDate = $t['SURVEY_DATE'] ?? '';
            $updatedDate = $t['UPDATED_DATE'] ?? '';
            $rows[] = [$id, mb_strimwidth((string) $title, 0, 60, '…'), $surveyDate, $updatedDate];
        }

        $this->table(['statsDataId', 'タイトル(60文字まで)', '調査年月', '更新日'], $rows);

        $this->newLine();
        $this->info('使い方:');
        $this->line('  1. 上記から「都道府県別・職種別賃金」っぽいタイトルを探す');
        $this->line('  2. その statsDataId を .env に追加: ESTAT_WAGE_STATS_DATA_ID=xxxxx');
        $this->line('  3. php artisan config:clear && php artisan config:cache');
        $this->line('  4. php artisan stats:fetch-estat で取得');

        return self::SUCCESS;
    }
}
