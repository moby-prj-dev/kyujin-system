<?php

namespace App\Services;

use App\Support\BotTraffic;
use Illuminate\Support\Facades\DB;

/**
 * 求人ごとの露出・反応の数字(ロボット除外・基準は BotTraffic)。管理画面の求人一覧と、掲載者の管理ページで共用する
 * 運営者・掲載者自身のアクセスは記録時点で除外済み(InternalVisitor)
 */
class JobMetrics
{
    /**
     * 送客枠の表示回数を「カードが画面に映ったとき」に数え始めた時刻(UTC)。
     * それより前はページを開いた時点でサーバーが数えており、ロボットや画面外のカードも含んでいたので集計に使わない
     */
    public const IMPRESSIONS_COUNTED_FROM = '2026-11-17 08:00:00';

    /**
     * - views30 / viewsTotal: 求人ページの閲覧数
     * - impressions30: 送客枠(おすすめ求人PR/直接応募)に表示された回数(30日。ただし IMPRESSIONS_COUNTED_FROM 以降のみ)
     * - impressionsFrom: impressions30 の集計開始が30日前より後のとき、その日付(Y-m-d・表示用)。それ以外は null
     * - clicks30: 送客枠からのクリック数
     *
     * @param int[] $ids
     * @param string|null $slot 'pr_job'(おすすめ求人PR枠) / 'direct_job'(直接応募枠) で絞る。null は両方
     * @return array<int, array{views30:int, viewsTotal:int, impressions30:int, impressionsFrom:?string, clicks30:int}>
     */
    public static function forJobs(array $ids, ?string $slot = null): array
    {
        if (empty($ids)) {
            return [];
        }

        $views  = fn($q) => BotTraffic::onlyHumans($q, heavy: true);
        $human  = fn($q) => BotTraffic::onlyHumans($q);
        $since = now()->subDays(30);
        $slots = $slot ? [$slot] : ['pr_job', 'direct_job'];
        $countedFrom = \Illuminate\Support\Carbon::parse(self::IMPRESSIONS_COUNTED_FROM, 'UTC');
        $impSince = $countedFrom->gt($since) ? $countedFrom : $since;
        $impressionsFrom = $countedFrom->gt($since) ? $countedFrom->copy()->timezone('Asia/Tokyo')->toDateString() : null;

        $viewsTotal = $views(DB::table('lp_views')->whereIn('job_id', $ids))
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $views30 = $views(DB::table('lp_views')->whereIn('job_id', $ids)->where('viewed_at', '>=', $since))
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $impressions30 = DB::table('lp_impressions')->whereIn('target_job_id', $ids)->whereIn('slot', $slots)->where('shown_at', '>=', $impSince)
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');
        $clicks30 = $human(DB::table('lp_clicks')->whereIn('target_job_id', $ids)->whereIn('type', $slots)->where('clicked_at', '>=', $since))
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');

        $out = [];
        foreach ($ids as $id) {
            $out[$id] = [
                'views30'       => (int) ($views30[$id] ?? 0),
                'viewsTotal'    => (int) ($viewsTotal[$id] ?? 0),
                'impressions30' => (int) ($impressions30[$id] ?? 0),
                'impressionsFrom' => $impressionsFrom,
                'clicks30'      => (int) ($clicks30[$id] ?? 0),
            ];
        }
        return $out;
    }
}
