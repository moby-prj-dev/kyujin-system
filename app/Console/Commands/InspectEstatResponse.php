<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class InspectEstatResponse extends Command
{
    protected $signature   = 'stats:inspect-estat {--stats-data-id= : 統計表ID (省略時は config 値)} {--samples=5 : VALUE要素のサンプル件数} {--search= : CLASS_INF の名前フィルタ(例: 介護)}';
    protected $description = 'e-Stat レスポンスの CLASS_INF と VALUE サンプルを表示してデータ構造を確認する';

    public function handle(): int
    {
        $appId       = (string) config('services.estat.app_id');
        $statsDataId = (string) ($this->option('stats-data-id') ?: config('services.estat.wage_stats_data_id'));
        $baseUrl     = (string) config('services.estat.base_url');
        $samples     = (int) $this->option('samples');

        if (empty($appId) || empty($statsDataId)) {
            $this->error('appId / statsDataId が設定されていません。');
            return self::FAILURE;
        }

        $this->info("取得: statsDataId={$statsDataId}");

        $client = new Client(['timeout' => 30]);
        $response = $client->get($baseUrl . '/getStatsData', [
            'query' => [
                'appId'       => $appId,
                'statsDataId' => $statsDataId,
                'lang'        => 'J',
                'limit'       => 20,
            ],
        ]);
        $json = json_decode($response->getBody()->getContents(), true);

        $result = $json['GET_STATS_DATA']['RESULT'] ?? null;
        if (!$result || (int) ($result['STATUS'] ?? 1) !== 0) {
            $this->error('API エラー: ' . ($result['ERROR_MSG'] ?? '不明'));
            return self::FAILURE;
        }

        $statsData = $json['GET_STATS_DATA']['STATISTICAL_DATA'] ?? [];
        $classInf  = $statsData['CLASS_INF']['CLASS_OBJ'] ?? [];
        $values    = $statsData['DATA_INF']['VALUE'] ?? [];

        $this->line(str_repeat('=', 60));
        $this->info('CLASS_INF (分類軸の定義) - 各軸の意味とコード対応');
        $this->line(str_repeat('=', 60));

        $search = (string) $this->option('search');

        foreach ($classInf as $obj) {
            $id   = $obj['@id'] ?? '?';
            $name = $obj['@name'] ?? '?';
            $classes = $obj['CLASS'] ?? [];
            if (isset($classes['@code'])) $classes = [$classes];

            if ($search !== '') {
                // search指定: その軸の中で名前にキーワード含むものだけ表示(件数制限なし)
                $matched = array_filter($classes, fn($c) => str_contains((string)($c['@name'] ?? ''), $search));
                if (empty($matched)) continue;
                $this->line('');
                $this->line("● 軸 @{$id} ({$name}) - '{$search}' に一致: " . count($matched) . '件');
                foreach ($matched as $c) {
                    $code = $c['@code'] ?? '?';
                    $nm   = $c['@name'] ?? '?';
                    $this->line("   {$code} : {$nm}");
                }
            } else {
                $this->line('');
                $this->line("● 軸 @{$id} ({$name})");
                $shown = 0;
                foreach ($classes as $c) {
                    $code = $c['@code'] ?? '?';
                    $nm   = $c['@name'] ?? '?';
                    $this->line("   {$code} : {$nm}");
                    if (++$shown >= 10) {
                        $this->line('   ... (合計 ' . count($classes) . ' 項目, 全件は --search=キーワード で絞れる)');
                        break;
                    }
                }
            }
        }

        $this->line('');
        $this->line(str_repeat('=', 60));
        $this->info("VALUE サンプル ({$samples}件)");
        $this->line(str_repeat('=', 60));

        foreach (array_slice($values, 0, $samples) as $i => $v) {
            $this->line("[{$i}] " . json_encode($v, JSON_UNESCAPED_UNICODE));
        }

        $this->line('');
        $this->line('合計 VALUE 件数: ' . count($values) . ' (limit=20で取得)');
        $this->line('');
        $this->info('上記情報を元に EstatService::extractMonthlyWage を調整します。');
        $this->line('- どの @id が「都道府県」軸か?(@area, @cat02 等)');
        $this->line('- どの @id が「職種」軸か?(@cat01, @cat03 等)');
        $this->line('- 沖縄県のコード値, 介護職員のコード値');

        return self::SUCCESS;
    }
}
