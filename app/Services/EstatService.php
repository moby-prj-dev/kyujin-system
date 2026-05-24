<?php

namespace App\Services;

use App\Models\AreaStatistic;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * e-Stat API クライアント
 * 賃金構造基本統計調査(賃金センサス)から都道府県×職種別の平均賃金を取得。
 *
 * 参考: https://www.e-stat.go.jp/api/api-info/e-stat-manual
 */
class EstatService
{
    private string $appId;
    private string $baseUrl;
    private string $wageStatsDataId;

    public function __construct()
    {
        $this->appId            = (string) config('services.estat.app_id');
        $this->baseUrl          = (string) config('services.estat.base_url');
        $this->wageStatsDataId  = (string) config('services.estat.wage_stats_data_id');
    }

    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->wageStatsDataId);
    }

    /**
     * 賃金センサスから指定都道府県の全職種データを取得し、area_statistics にキャッシュ。
     * 戻り値: 保存した件数
     */
    public function fetchAndCachePrefectureWages(string $prefecture = '沖縄県'): int
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('ESTAT_APP_ID または ESTAT_WAGE_STATS_DATA_ID が未設定です。');
        }

        $client = new Client(['timeout' => 30]);
        $response = $client->get($this->baseUrl . '/getStatsData', [
            'query' => [
                'appId'       => $this->appId,
                'statsDataId' => $this->wageStatsDataId,
                'lang'        => 'J',
                // 都道府県絞り込み(コード指定が理想だが、後段でフィルタする実装にする)
            ],
        ]);

        $json = json_decode($response->getBody()->getContents(), true);
        if (!is_array($json)) {
            throw new \RuntimeException('e-Stat 応答のパースに失敗');
        }

        $result = $json['GET_STATS_DATA']['RESULT'] ?? null;
        if (!$result || (int) ($result['STATUS'] ?? 1) !== 0) {
            $err = $result['ERROR_MSG'] ?? '不明なエラー';
            throw new \RuntimeException('e-Stat API エラー: ' . $err);
        }

        $statsData = $json['GET_STATS_DATA']['STATISTICAL_DATA'] ?? [];
        $year = $this->extractYear($statsData);

        // VALUE 配列を取得し、都道府県・職種でフィルタしてキャッシュ
        $values = $statsData['DATA_INF']['VALUE'] ?? [];
        if (empty($values)) {
            Log::warning('e-Stat: VALUEデータが空');
            return 0;
        }

        // 職種名マッピング(e-Stat側の表記)
        // 実際の表記は統計表によって異なるため、運用しながら調整する
        $occupations = ['介護職員', 'ホームヘルパー', '介護支援専門員', '社会福祉士', '保育士'];

        $saved = 0;
        foreach ($occupations as $occupation) {
            // VALUE配列から該当する値を探す(都道府県と職種の組み合わせ)
            // VALUE構造: @属性で軸を識別する必要があり、データソース次第
            // ここでは raw_payload としてレスポンス全体を保存し、後で正規化できるようにする
            $wage = $this->extractMonthlyWage($values, $prefecture, $occupation);
            if ($wage === null) continue;

            AreaStatistic::updateOrCreate(
                [
                    'prefecture' => $prefecture,
                    'occupation' => $occupation,
                    'year'       => $year,
                    'source'     => AreaStatistic::SOURCE_ESTAT_WAGE_CENSUS,
                ],
                [
                    'monthly_wage' => $wage,
                    'raw_payload'  => ['occupation' => $occupation, 'note' => 'auto-fetched'],
                    'fetched_at'   => now(),
                ]
            );
            $saved++;
        }

        return $saved;
    }

    private function extractYear(array $statsData): int
    {
        // TABLE_INF.SURVEY_DATE 例: "202401" / "2023"
        $date = $statsData['TABLE_INF']['SURVEY_DATE'] ?? null;
        if (is_string($date) && strlen($date) >= 4) {
            return (int) substr($date, 0, 4);
        }
        return (int) date('Y');
    }

    /**
     * VALUE配列から指定都道府県・職種の月給を抽出
     * 構造はAPIレスポンスのCLASS定義に依存するため、暫定実装。
     * 実データを見て調整する。
     */
    private function extractMonthlyWage(array $values, string $prefecture, string $occupation): ?int
    {
        // VALUE要素は通常 @cat01, @area, @time, @unit, $ などの属性を持つ
        // 単位が 千円 の場合 *1000、円ならそのまま
        foreach ($values as $v) {
            $area = $v['@area'] ?? '';
            $cat  = $v['@cat01'] ?? '';
            $val  = $v['$'] ?? null;
            $unit = $v['@unit'] ?? '';

            if (!is_string($area) || !str_contains($area, $prefecture)) continue;
            if (!is_string($cat)  || !str_contains($cat, $occupation))  continue;
            if (!is_numeric($val)) continue;

            $intVal = (int) $val;
            if ($unit === '千円') $intVal *= 1000;

            return $intVal;
        }
        return null;
    }
}
