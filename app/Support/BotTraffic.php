<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 閲覧・クリックの集計からロボット(検索エンジン・収集ツール)を除くための共通ルール
 *
 * 2026-11-20 に見直し。それまでは UA に "bot" 等を含むものだけ除いていたが、
 * Google の「GoogleOther」「Google-Read-Aloud」、Tencent クラウドの古い iPhone 偽装、
 * 1日に100件以上の求人を読む収集ツールなどが人として数えられ、閲覧数が実際の2倍以上になっていた。
 * 記録は従来どおり全件残し、集計するときにここで除く(過去分にも同じ基準が効く)
 */
class BotTraffic
{
    /** UA で見分けるロボット(MySQL の REGEXP と PHP の preg で共用・大文字小文字は区別しない) */
    public const UA_PATTERN = 'bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless|lighthouse|monitor|scrapy|wget|go-http|okhttp|java/|axios|httpclient'
        . '|GoogleOther|Google-'                 // Googlebot を名乗らない Google の読み取り(GoogleOther, Google-Read-Aloud, Google-InspectionTool など)
        . '|Nexus 5X Build/MMB29P'               // Googlebot スマホ版の端末名(2015年の端末で、人の利用はほぼない)
        . '|iPhone OS 13_2_3'                    // Tencent クラウドの収集ツールが使う古い iPhone の偽装
        . '|BusinessContactVerification'
        . '|deploy-check'                        // 運営者の動作確認
        . '|^Mozilla/5\.0$';                     // 中身のない UA

    /** IP の先頭で見分けるロボット(66.249. は Google のクローラー) */
    public const IP_PREFIXES = ['66.249.'];

    /** 1日にこれより多く求人ページを開いた IP は収集ツールとみなす(人は多くても1日10件前後) */
    public const HEAVY_VIEWS_PER_DAY = 20;

    public static function isBotUa(?string $ua): bool
    {
        $ua = (string) $ua;
        return $ua === '' || preg_match('#' . self::UA_PATTERN . '#i', $ua) === 1;
    }

    public static function isBotIp(?string $ip): bool
    {
        foreach (self::IP_PREFIXES as $prefix) {
            if (str_starts_with((string) $ip, $prefix)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 1日に HEAVY_VIEWS_PER_DAY 件を超えて求人ページを開いた IP(過去180日)
     * 全件集計は0.3秒ほどかかるので1時間キャッシュ(文字列の配列のみ)
     *
     * @return string[]
     */
    public static function heavyIps(): array
    {
        return Cache::remember('bot_traffic:heavy_ips:v1', now()->addHour(), fn() => DB::table('lp_views')
            ->where('viewed_at', '>=', now()->subDays(180))
            ->whereNotNull('ip_address')
            ->groupBy('ip_address', DB::raw('DATE(viewed_at)'))
            ->havingRaw('COUNT(*) > ?', [self::HEAVY_VIEWS_PER_DAY])
            ->pluck('ip_address')
            ->unique()->values()->map(fn($ip) => (string) $ip)->all());
    }

    /**
     * クエリからロボットを除く
     * $heavy: 大量に閲覧した IP も除く(閲覧数の集計用)
     */
    public static function onlyHumans(Builder $q, string $uaCol = 'user_agent', string $ipCol = 'ip_address', bool $heavy = false): Builder
    {
        $q->whereNotNull($uaCol)->where($uaCol, 'not regexp', self::UA_PATTERN);

        $q->where(function ($w) use ($ipCol) {
            $w->whereNull($ipCol);
            $w->orWhere(function ($x) use ($ipCol) {
                foreach (self::IP_PREFIXES as $prefix) {
                    $x->where($ipCol, 'not like', $prefix . '%');
                }
            });
        });

        if ($heavy && ($ips = self::heavyIps())) {
            $q->where(fn($w) => $w->whereNull($ipCol)->orWhereNotIn($ipCol, $ips));
        }

        return $q;
    }
}
