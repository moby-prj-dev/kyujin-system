<?php

namespace App\Services;

use App\Models\ContentArticle;
use App\Models\Job;
use App\Models\MasterArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * スタンダードプラン「露出拡大」用のおすすめ求人(PR)を探す
 * - 対象: 自社(Care Entry)求人・スタンダード・公開中・メール認証済・非表示でない・掲載期限内・沖縄県エリア
 * - 同エリア×同職種 → 同エリア の順で優先(沖縄全域への補完は呼び出し側が指定した場合のみ)
 * - 候補内は日替わりの決定的シャッフルで公平にローテーション
 * - キャッシュは ID配列(プリミティブ)のみ。Eloquent/Collection は入れない
 */
class RecommendedJobFinder
{
    private const CACHE_TTL_MINUTES = 10;

    /**
     * ハローワーク求人ページ用: 同エリア必須・職種一致を優先・他エリアへの補完なし
     */
    public function forJob(Job $job, int $limit = 3): Collection
    {
        $areaIds    = $job->jobAreas->pluck('area_id')->filter()->map(fn($v) => (int) $v)->all();
        $jobTypeIds = $job->jobJobTypes->pluck('job_type_id')->filter()->map(fn($v) => (int) $v)->all();

        if (empty($areaIds)) {
            return new Collection();
        }

        return $this->find($areaIds, $jobTypeIds, $limit, $job->id);
    }

    /**
     * ハローワーク求人ページ用「この地域で直接応募できる求人」: ベーシックの自社求人(PRなし枠)
     * 同市町村×同職種 → 同市町村 → 同じ地域(那覇・南部 等)の順。PR枠(スタンダード)より下に表示する
     */
    public function forJobDirect(Job $job, int $limit = 3): Collection
    {
        $areaIds    = $job->jobAreas->pluck('area_id')->filter()->map(fn($v) => (int) $v)->all();
        $jobTypeIds = $job->jobJobTypes->pluck('job_type_id')->filter()->map(fn($v) => (int) $v)->all();

        if (empty($areaIds)) {
            return new Collection();
        }

        $regionAreaIds = MasterArea::whereIn('region', MasterArea::whereIn('id', $areaIds)->pluck('region'))
            ->where('prefecture', '沖縄県')
            ->pluck('id')->map(fn($v) => (int) $v)->all();

        return $this->find($areaIds, $jobTypeIds, $limit, $job->id, false, Job::PLAN_BASIC, $regionAreaIds);
    }

    /**
     * 記事ページ用: area_id があればエリア一致必須、職種のみなら沖縄全域で職種一致
     */
    public function forArticle(ContentArticle $article, int $limit = 3): Collection
    {
        $areaIds    = $article->area_id ? [(int) $article->area_id] : [];
        $jobTypeIds = $article->job_type_id ? [(int) $article->job_type_id] : [];

        if (empty($areaIds) && empty($jobTypeIds)) {
            return new Collection();
        }

        return $this->find($areaIds, $jobTypeIds, $limit);
    }

    /**
     * トップページ用: 沖縄全域のスタンダード求人から日替わりで
     */
    public function forTop(int $limit = 6): Collection
    {
        return $this->find([], [], $limit, null, true);
    }

    /**
     * @param int[] $areaIds     空ならエリア条件なし
     * @param int[] $jobTypeIds  空なら職種条件なし
     * @param bool  $okinawaFallback 条件一致で足りない場合に沖縄全域のスタンダード求人で補完するか
     * @param string $plan         対象プラン(スタンダード=PR枠 / ベーシック=直接応募枠)
     * @param int[] $regionAreaIds 同エリアで足りない場合に補完する周辺エリア(同じ地域)
     */
    public function find(array $areaIds, array $jobTypeIds, int $limit, ?int $excludeId = null, bool $okinawaFallback = false, string $plan = Job::PLAN_STANDARD, array $regionAreaIds = []): Collection
    {
        if ($limit <= 0) {
            return new Collection();
        }

        $ids = $this->candidateIds($areaIds, $jobTypeIds, $okinawaFallback, $plan, $regionAreaIds);

        if ($excludeId) {
            $ids = array_values(array_filter($ids, fn($id) => $id !== (int) $excludeId));
        }
        $ids = array_slice($ids, 0, $limit);

        if (empty($ids)) {
            return new Collection();
        }

        // キャッシュ後に非公開化された求人を除くため、条件を再適用して取り直す
        $jobs = $this->baseQuery($plan)
            ->whereIn('id', $ids)
            ->with(['jobAreas.area', 'jobJobTypes.jobType'])
            ->get()
            ->keyBy('id');

        // 並び順(日替わりシャッフル結果)を復元
        return new Collection(
            collect($ids)->map(fn($id) => $jobs->get($id))->filter()->values()->all()
        );
    }

    /**
     * 優先度順・日替わりシャッフル済みの候補ID配列(プリミティブのみキャッシュ)
     *
     * @return int[]
     */
    private function candidateIds(array $areaIds, array $jobTypeIds, bool $okinawaFallback, string $plan = Job::PLAN_STANDARD, array $regionAreaIds = []): array
    {
        sort($areaIds);
        sort($jobTypeIds);
        sort($regionAreaIds);
        $today = now()->toDateString();
        $key = 'recommended_jobs:v3:' . $today . ':' . md5(json_encode([$areaIds, $jobTypeIds, $okinawaFallback, $plan, $regionAreaIds]));

        return Cache::remember($key, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($areaIds, $jobTypeIds, $okinawaFallback, $today, $plan, $regionAreaIds) {
            $tiers = [];

            if (!empty($areaIds) && !empty($jobTypeIds)) {
                // 同エリア×同職種 → 同エリア
                $tiers[] = $this->baseQuery($plan)
                    ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds))
                    ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds));
                $tiers[] = $this->baseQuery($plan)
                    ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds));
            } elseif (!empty($areaIds)) {
                $tiers[] = $this->baseQuery($plan)
                    ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds));
            } elseif (!empty($jobTypeIds)) {
                $tiers[] = $this->baseQuery($plan)
                    ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds));
            }

            if (!empty($regionAreaIds)) {
                $tiers[] = $this->baseQuery($plan)
                    ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $regionAreaIds));
            }

            if ($okinawaFallback) {
                $tiers[] = $this->baseQuery($plan);
            }

            $result = [];
            foreach ($tiers as $query) {
                $ids = $query->limit(200)->pluck('id')->map(fn($v) => (int) $v)->all();
                $ids = array_values(array_diff($ids, $result));
                $result = array_merge($result, $this->dailyShuffle($ids, $today));
            }

            return $result;
        });
    }

    /**
     * 日付をシードにした決定的シャッフル(同じ日は同じ順・日替わりで入れ替わる)
     *
     * @param int[] $ids
     * @return int[]
     */
    private function dailyShuffle(array $ids, string $today): array
    {
        usort($ids, function ($a, $b) use ($today) {
            return crc32($today . ':' . $a) <=> crc32($today . ':' . $b) ?: $a <=> $b;
        });
        return $ids;
    }

    /**
     * 対象の自社求人(沖縄県のエリアに紐付くもののみ・検索ページと揃える)
     * スタンダード=PR枠(prEligible) / ベーシック=直接応募枠(公開条件は prEligible と同じでプランのみ違う)
     */
    private function baseQuery(string $plan = Job::PLAN_STANDARD): Builder
    {
        $q = $plan === Job::PLAN_STANDARD
            ? Job::prEligible()
            : Job::where('status', Job::STATUS_ACTIVE)
                ->where('source', 'care_entry')
                ->where('plan', Job::PLAN_BASIC)
                ->whereNotNull('email_verified_at')
                ->where('is_admin_hidden', false)
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));

        return $q->whereHas('jobAreas.area', fn($q) => $q->where('prefecture', '沖縄県'));
    }
}
