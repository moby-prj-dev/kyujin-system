<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 送客枠(おすすめ求人PR / 直接応募できる求人)の表示回数を記録する
 * - ボット・クローラーのアクセスは記録しない(テーブル肥大化と数字の水増しを防ぐ)
 * - 記録の失敗でページ表示を妨げない
 */
class RecommendationImpressionLogger
{
    public const BOT_UA = '/bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless|lighthouse|monitor/i';

    public function log(Collection $jobs, string $slot, string $page): void
    {
        if ($jobs->isEmpty()) {
            return;
        }

        $ua = request()->userAgent() ?? '';
        if ($ua === '' || preg_match(self::BOT_UA, $ua)) {
            return;
        }

        $now = now();
        try {
            DB::table('lp_impressions')->insert(
                $jobs->map(fn($job) => [
                    'slot'          => $slot,
                    'target_job_id' => $job->id,
                    'page'          => $page,
                    'shown_at'      => $now,
                ])->values()->all()
            );
        } catch (\Throwable $e) {
            Log::warning('lp_impression記録失敗: ' . $e->getMessage());
        }
    }
}
