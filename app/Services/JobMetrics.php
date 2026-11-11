<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * 求人ごとの露出・反応の数字(ボット除外)。管理画面の求人一覧と、掲載者の管理ページで共用する
 * 運営者・掲載者自身のアクセスは記録時点で除外済み(InternalVisitor)
 */
class JobMetrics
{
    private const BOT = 'bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless';

    /**
     * - views30 / viewsTotal: 求人ページの閲覧数
     * - impressions30: 送客枠(おすすめ求人PR/直接応募)に表示された回数
     * - clicks30: 送客枠からのクリック数
     *
     * @param int[] $ids
     * @return array<int, array{views30:int, viewsTotal:int, impressions30:int, clicks30:int}>
     */
    public static function forJobs(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $human = fn($q, $col) => $q->where(fn($w) => $w->whereNull($col)->orWhere($col, 'not regexp', self::BOT));
        $since = now()->subDays(30);

        $viewsTotal = $human(DB::table('lp_views')->whereIn('job_id', $ids), 'user_agent')
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $views30 = $human(DB::table('lp_views')->whereIn('job_id', $ids)->where('viewed_at', '>=', $since), 'user_agent')
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $impressions30 = DB::table('lp_impressions')->whereIn('target_job_id', $ids)->where('shown_at', '>=', $since)
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');
        $clicks30 = $human(DB::table('lp_clicks')->whereIn('target_job_id', $ids)->whereIn('type', ['pr_job', 'direct_job'])->where('clicked_at', '>=', $since), 'user_agent')
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');

        $out = [];
        foreach ($ids as $id) {
            $out[$id] = [
                'views30'       => (int) ($views30[$id] ?? 0),
                'viewsTotal'    => (int) ($viewsTotal[$id] ?? 0),
                'impressions30' => (int) ($impressions30[$id] ?? 0),
                'clicks30'      => (int) ($clicks30[$id] ?? 0),
            ];
        }
        return $out;
    }
}
