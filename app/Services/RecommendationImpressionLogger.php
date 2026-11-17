<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 送客枠(おすすめ求人PR / 直接応募できる求人)の表示回数を記録する(カードが画面に半分以上映ったら1回)
 * - ボット・クローラーのアクセスは記録しない(テーブル肥大化と数字の水増しを防ぐ)
 * - 記録の失敗でページ表示を妨げない
 */
class RecommendationImpressionLogger
{
    public const BOT_UA = '/bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless|lighthouse|monitor/i';

    /**
     * カードが画面に映った送客枠を記録する(ブラウザの /t/impression から呼ばれる)。
     * 2026-11-17 までは、ページを開いた時点でサーバー側から記録していた(画面外のカードも数えていた)
     *
     * @param array<int, array{slot: string, job_id: int}> $items
     */
    public function record(array $items, string $page): void
    {
        if (empty($items) || \App\Support\InternalVisitor::check()) {
            return;
        }

        $ua = request()->userAgent() ?? '';
        if ($ua === '' || preg_match(self::BOT_UA, $ua)) {
            return;
        }

        $now = now();
        try {
            DB::table('lp_impressions')->insert(array_map(fn($i) => [
                'slot'          => $i['slot'],
                'target_job_id' => $i['job_id'],
                'page'          => $page,
                'shown_at'      => $now,
            ], $items));
        } catch (\Throwable $e) {
            Log::warning('lp_impression記録失敗: ' . $e->getMessage());
        }
    }
}
