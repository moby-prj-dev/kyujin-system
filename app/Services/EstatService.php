<?php

namespace App\Services;

use App\Models\AreaStatistic;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * e-Stat API クライアント
 * 賃金構造基本統計調査(賃金センサス)から都道府県×職種別の平均賃金を取得。
 *
 * @cat02 (職種小分類2020〜) の介護関連コード:
 *   1168 : 介護支援専門員(ケアマネージャー)
 *   1361 : 介護職員(医療・福祉施設等)
 *   1362 : 訪問介護従事者
 *   1163 : 保育士
 * @tab (表章項目):
 *   10 : 所定内給与額(月給)
 *   12 : 年間賞与その他特別給与額
 * @cat01 (性別):
 *   01 : 男女計
 * @area (地域):
 *   47000 : 沖縄県
 */
class EstatService
{
    private string $appId;
    private string $baseUrl;
    private string $wageStatsDataId;

    /** 職種名 => e-Stat @cat02 コード */
    private const OCCUPATION_CAT02 = [
        '介護職員'             => '1361',
        '訪問介護員(ヘルパー)' => '1362',
        '介護支援専門員'       => '1168',
        '保育士'               => '1163',
    ];

    private const CODE_AREA_OKINAWA = '47000';
    private const CODE_CAT01_TOTAL  = '01';  // 男女計
    private const CODE_TAB_MONTHLY  = '10';  // 所定内給与額
    private const CODE_TAB_BONUS    = '12';  // 年間賞与

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
     * 指定都道府県(現状は沖縄県のみ対応)の各職種について月給・年間賞与を取得し area_statistics に保存。
     */
    public function fetchAndCachePrefectureWages(string $prefecture = '沖縄県'): int
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('ESTAT_APP_ID または ESTAT_WAGE_STATS_DATA_ID が未設定です。');
        }
        if ($prefecture !== '沖縄県') {
            throw new \RuntimeException("対応外の都道府県: {$prefecture}(現状は沖縄県のみ)");
        }

        $saved = 0;
        foreach (self::OCCUPATION_CAT02 as $occupationName => $cat02Code) {
            $monthly = $this->fetchMetric($cat02Code, self::CODE_TAB_MONTHLY);
            $bonus   = $this->fetchMetric($cat02Code, self::CODE_TAB_BONUS);

            if ($monthly === null && $bonus === null) {
                Log::info("e-Stat: {$occupationName} はデータ無し");
                continue;
            }

            $year = $monthly['year'] ?? $bonus['year'] ?? (int) date('Y');

            AreaStatistic::updateOrCreate(
                [
                    'prefecture' => $prefecture,
                    'occupation' => $occupationName,
                    'year'       => $year,
                    'source'     => AreaStatistic::SOURCE_ESTAT_WAGE_CENSUS,
                ],
                [
                    'monthly_wage'        => $monthly['value'] ?? null,
                    'annual_special_wage' => $bonus['value']   ?? null,
                    'raw_payload'         => [
                        'cat02_code'   => $cat02Code,
                        'monthly_unit' => $monthly['unit'] ?? null,
                        'bonus_unit'   => $bonus['unit']   ?? null,
                    ],
                    'fetched_at'          => now(),
                ]
            );
            $saved++;
        }
        return $saved;
    }

    /**
     * 1組み合わせ(職種×表章項目)のデータをAPIから取得し、最新年の値を返す。
     * @return array{value:int, year:int, unit:string}|null
     */
    private function fetchMetric(string $cat02, string $tab): ?array
    {
        $client = new Client(['timeout' => 30]);

        // 直近5年分のうち最新を狙う(該当年がなければAPI側で空応答)
        $cdTimeFrom = (string) (((int) date('Y')) - 5) . '000000';

        try {
            $response = $client->get($this->baseUrl . '/getStatsData', [
                'query' => [
                    'appId'       => $this->appId,
                    'statsDataId' => $this->wageStatsDataId,
                    'cdTab'       => $tab,
                    'cdCat01'     => self::CODE_CAT01_TOTAL,
                    'cdCat02'     => $cat02,
                    'cdArea'      => self::CODE_AREA_OKINAWA,
                    'cdTimeFrom'  => $cdTimeFrom,
                    'lang'        => 'J',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning("e-Stat fetchMetric 失敗 (cat02={$cat02}, tab={$tab}): " . $e->getMessage());
            return null;
        }

        $json = json_decode($response->getBody()->getContents(), true);
        if (!is_array($json)) return null;

        $result = $json['GET_STATS_DATA']['RESULT'] ?? null;
        if (!$result || (int) ($result['STATUS'] ?? 1) !== 0) {
            Log::warning('e-Stat API エラー: ' . ($result['ERROR_MSG'] ?? '不明'));
            return null;
        }

        $values = $json['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE'] ?? [];
        // 単一要素は配列にラップされない場合がある
        if (isset($values['@time'])) $values = [$values];
        if (empty($values)) return null;

        // 最新年を選択(@time が大きい順)
        usort($values, fn($a, $b) => strcmp((string)($b['@time'] ?? ''), (string)($a['@time'] ?? '')));
        $latest = $values[0];

        $raw = $latest['$'] ?? null;
        if (!is_numeric($raw)) return null;

        $unit = (string) ($latest['@unit'] ?? '');
        $year = (int) substr((string)($latest['@time'] ?? ''), 0, 4) ?: (int) date('Y');

        $value = (int) round((float) $raw);
        // 単位変換(千円 → 円)
        if ($unit === '千円') {
            $value *= 1000;
        }

        return ['value' => $value, 'year' => $year, 'unit' => $unit];
    }
}
