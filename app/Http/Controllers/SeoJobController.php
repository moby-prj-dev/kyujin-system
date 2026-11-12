<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\MasterArea;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoJobController extends Controller
{
    // 表示順: (旧・注目フラグ) → スタンダード(露出拡大・PR表記) → ベーシック → ハローワーク
    private const RANK_ORDER_SQL = "CASE
        WHEN is_featured = 1 AND plan = 'standard' AND source = 'care_entry' THEN 0
        WHEN plan = 'standard' AND source = 'care_entry' THEN 1
        WHEN source = 'care_entry' THEN 2
        ELSE 3 END";

    private function visibleJobs()
    {
        return Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            // 掲載期限切れ(毎朝の期限切れ処理より前の時間帯)は出さない
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->with(['jobAreas.area', 'jobJobTypes.jobType']);
    }

    private function applyFilters($query, Request $request): array
    {
        $ids = [];

        // 職種（複数スラッグ → 複数ID）
        $jobTypeSlugs = array_filter((array) $request->input('job_types', []));
        if (!empty($jobTypeSlugs)) {
            $jobTypeIds = MasterJobType::active()->whereIn('slug', $jobTypeSlugs)->pluck('id')->toArray();
            if (!empty($jobTypeIds)) {
                $query->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds));
                $ids['job_type_ids'] = $jobTypeIds;
            }
        }

        // 雇用形態（複数スラッグ → 複数ID）
        $empTypeSlugs = array_filter((array) $request->input('employment_types', []));
        if (!empty($empTypeSlugs)) {
            $empTypeIds = MasterEmploymentType::active()->whereIn('slug', $empTypeSlugs)->pluck('id')->toArray();
            if (!empty($empTypeIds)) {
                $query->whereHas('jobEmploymentTypes', fn($q) => $q->whereIn('employment_type_id', $empTypeIds));
                $ids['employment_type_ids'] = $empTypeIds;
            }
        }

        // 勤務条件（複数ID）
        $condIds = array_values(array_filter(array_map('intval', (array) $request->input('condition_ids', []))));
        if (!empty($condIds)) {
            $query->whereHas('jobConditions', fn($q) => $q->whereIn('condition_id', $condIds));
            $ids['condition_ids'] = $condIds;
        }

        // 重視ポイント（フィルタなし、LINE引き継ぎ用のみ）
        $appealIds = array_values(array_filter(array_map('intval', (array) $request->input('appeal_ids', []))));
        if (!empty($appealIds)) {
            $ids['appeal_ids'] = $appealIds;
        }

        return $ids;
    }

    public function index(Request $request)
    {
        // エリアは複数選択(areas[])。旧形式の ?area=slug も受け付ける
        $areaSlugs = array_values(array_unique(array_filter(array_merge(
            (array) $request->input('areas', []),
            (array) $request->input('area', [])
        ), 'is_string')));

        $selectedAreas = $areaSlugs
            ? MasterArea::active()->where('prefecture', '沖縄県')->whereIn('slug', $areaSlugs)->orderBy('sort_order')->get()
            : collect();

        // 1エリアだけならインデックス対象のエリアページへ集約(SEO)
        if ($selectedAreas->count() === 1) {
            return redirect()->route('seo.jobs.area', [
                'slug' => $selectedAreas->first()->slug,
                ...$request->except(['area', 'areas', 'page']),
            ], 301);
        }

        $currentArea        = null;
        $searchConditionIds = [];

        $query = $this->visibleJobs();

        if ($selectedAreas->isNotEmpty()) {
            $areaIds = $selectedAreas->pluck('id')->all();
            $query->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds));
            $searchConditionIds['area_ids'] = $areaIds;
        } else {
            $query->whereHas('jobAreas.area', fn($q) => $q->where('prefecture', '沖縄県'));
        }

        $filterIds          = $this->applyFilters($query, $request);
        $searchConditionIds = [...$searchConditionIds, ...$filterIds];

        $jobs = $this->orderForListing($query)
            ->paginate(20)->withQueryString();

        $this->logPrImpressions($jobs);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $areaLabel = $selectedAreas->isNotEmpty()
            ? $selectedAreas->take(3)->pluck('name')->implode('・') . ($selectedAreas->count() > 3 ? "など{$selectedAreas->count()}エリア" : '')
            : null;

        $pageTitle = $areaLabel
            ? "{$areaLabel}の介護・福祉求人一覧"
            : '沖縄の介護・福祉求人一覧';

        $pageDesc = $areaLabel
            ? "{$areaLabel}で介護・福祉の仕事を探している方向けの求人一覧です。地域の特性に合った求人を掲載しています。"
            : '沖縄で介護・福祉の仕事を探している方向けの求人一覧です。エリアや職種から自分に合う求人を探せます。';

        $currentJobType = null;
        $stats          = null;

        $nearbyJobs = $this->nearbyJobs($searchConditionIds);

        return view('seo.jobs.index', compact(
            'jobs', 'currentArea', 'currentJobType', 'areas', 'stats',
            'pageTitle', 'pageDesc', 'searchConditionIds', 'nearbyJobs'
        ));
    }

    public function area(Request $request, string $slug)
    {
        $currentArea = MasterArea::active()->where('slug', $slug)->firstOrFail();

        $query = $this->visibleJobs()
            ->whereHas('jobAreas', fn($q) => $q->where('area_id', $currentArea->id));

        $filterIds          = $this->applyFilters($query, $request);
        $searchConditionIds = ['area_ids' => [$currentArea->id], ...$filterIds];

        $jobs = $this->orderForListing($query)
            ->paginate(20)->withQueryString();

        $this->logPrImpressions($jobs);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $stats = $this->statsFor($currentArea, null);

        $total = $stats['total'] ?? 0;
        $nearbyJobs = $this->nearbyJobs($searchConditionIds);

        // 求人が少ない市町村は、同じ地域の周辺市町村の求人も並べる(絞り込みなしのときだけ)
        $surrounding = empty($filterIds)
            ? $this->surroundingJobs($currentArea, $total, $nearbyJobs->pluck('id')->all())
            : null;

        return view('seo.jobs.index', [
            'jobs'               => $jobs,
            'currentArea'        => $currentArea,
            'currentJobType'     => null,
            'areas'              => $areas,
            'stats'              => $stats,
            'pageTitle'          => "【沖縄県{$currentArea->name}】介護・福祉の求人・転職情報 " . ($total > 0 ? "{$total}件" : '募集'),
            'pageDesc'           => "沖縄県{$currentArea->name}の介護・福祉の求人・転職情報" . ($total > 0 ? "を{$total}件掲載中。職種や雇用形態の内訳も掲載" : "") . "。介護職員・ヘルパー・看護・保育など職種から絞り込めます。応募がなければ掲載料無料のCare Entry(ケアエントリー)。",
            'searchConditionIds' => $searchConditionIds,
            'nearbyJobs'         => $nearbyJobs,
            'surrounding'        => $surrounding,
        ]);
    }

    public function areaJobType(Request $request, string $areaSlug, string $jobTypeSlug)
    {
        $currentArea    = MasterArea::active()->where('slug', $areaSlug)->firstOrFail();
        $currentJobType = MasterJobType::active()->where('slug', $jobTypeSlug)->firstOrFail();

        $query = $this->visibleJobs()
            ->whereHas('jobAreas',    fn($q) => $q->where('area_id', $currentArea->id))
            ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $currentJobType->id));

        $filterIds = $this->applyFilters($query, $request);
        $searchConditionIds = [
            'area_ids'     => [$currentArea->id],
            'job_type_ids' => [$currentJobType->id],
            ...$filterIds,
        ];

        $jobs = $this->orderForListing($query)
            ->paginate(20)->withQueryString();

        $this->logPrImpressions($jobs);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $stats = $this->statsFor($currentArea, $currentJobType);

        $total = $stats['total'] ?? 0;
        $nearbyJobs = $this->nearbyJobs($searchConditionIds);

        return view('seo.jobs.index', [
            'jobs'               => $jobs,
            'currentArea'        => $currentArea,
            'currentJobType'     => $currentJobType,
            'areas'              => $areas,
            'stats'              => $stats,
            'pageTitle'          => "【沖縄県{$currentArea->name}】{$currentJobType->name}の求人・転職情報 " . ($total > 0 ? "{$total}件" : '募集'),
            'pageDesc'           => "沖縄県{$currentArea->name}の{$currentJobType->name}求人" . ($total > 0 ? "を{$total}件掲載中" : "の最新募集情報") . "。給与・雇用形態(正社員・パート・契約社員)から絞り込み検索が可能。応募がなければ掲載料無料のCare Entry。",
            'searchConditionIds' => $searchConditionIds,
            'nearbyJobs'         => $nearbyJobs,
        ]);
    }

    /**
     * 一覧の並び順: プラン(スタンダード→ベーシック→ハローワーク) → 登録日が新しい順 → 同じ日の中は日替わりで入れ替え。
     * 登録時刻の順だと、最後に取り込んだ事業所の求人が先頭に固まる(例: 同じ施設の3件が並ぶ)ため、
     * 同じ日の中は「求人ID+今日の日付」のハッシュ順にして事業所をばらけさせる。1日の間は順番が固定なのでページ送りも崩れない。
     */
    private function orderForListing($query)
    {
        return $query
            ->orderByRaw(self::RANK_ORDER_SQL)
            ->orderByRaw('DATE(job_listings.created_at) DESC')
            ->orderByRaw('CRC32(CONCAT(job_listings.id, ?))', [now()->toDateString()])
            ->orderByDesc('job_listings.id');
    }

    /**
     * エリア(+職種)に対する独自統計を1時間キャッシュ。プリミティブのみ返す。
     */
    private function statsFor(MasterArea $area, ?MasterJobType $jobType): array
    {
        $key = "seo_jobs_stats:v3:a{$area->id}:" . ($jobType ? "j{$jobType->id}" : 'jall');
        return Cache::remember($key, now()->addHour(), function () use ($area, $jobType) {
            $base = Job::active()
                ->whereNotNull('email_verified_at')
                ->where('is_admin_hidden', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->whereHas('jobAreas', fn($q) => $q->where('area_id', $area->id));

            if ($jobType) {
                $base->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $jobType->id));
            }

            $total    = (clone $base)->count();
            $hwCount  = (clone $base)->where('source', 'hellowork')->count();
            $ownCount = $total - $hwCount;

            // 雇用形態内訳(プリミティブ配列)
            $empBreakdown = [];
            $jobIds = null;
            if ($total > 0) {
                $jobIds = (clone $base)->pluck('job_listings.id');
                $rows = \DB::table('job_employment_types')
                    ->join('master_employment_types', 'job_employment_types.employment_type_id', '=', 'master_employment_types.id')
                    ->whereIn('job_employment_types.job_id', $jobIds)
                    ->groupBy('master_employment_types.id', 'master_employment_types.name')
                    ->orderByDesc(\DB::raw('COUNT(*)'))
                    ->select('master_employment_types.name', \DB::raw('COUNT(*) as count'))
                    ->get();
                foreach ($rows as $r) {
                    $empBreakdown[] = ['name' => $r->name, 'count' => (int) $r->count];
                }
            }

            // 職種の内訳(エリアページのみ・上位6職種)
            $typeBreakdown = [];
            if ($total > 0 && ! $jobType) {
                $jobIds = $jobIds ?? (clone $base)->pluck('job_listings.id');
                $rows = \DB::table('job_job_types')
                    ->join('master_job_types', 'job_job_types.job_type_id', '=', 'master_job_types.id')
                    ->whereIn('job_job_types.job_id', $jobIds)
                    ->where('master_job_types.is_active', true)
                    ->groupBy('master_job_types.id', 'master_job_types.name', 'master_job_types.slug')
                    ->orderByDesc(\DB::raw('COUNT(*)'))
                    ->limit(6)
                    ->select('master_job_types.name', 'master_job_types.slug', \DB::raw('COUNT(*) as count'))
                    ->get();
                foreach ($rows as $r) {
                    $typeBreakdown[] = ['name' => $r->name, 'slug' => $r->slug, 'count' => (int) $r->count];
                }
            }

            return [
                'total'         => $total,
                'own_count'     => $ownCount,
                'hw_count'      => $hwCount,
                'emp_breakdown' => $empBreakdown,
                'type_breakdown' => $typeBreakdown,
            ];
        });
    }

    /** 検索結果に表示されたスタンダード(おすすめ求人・PR)カードの表示回数を記録 */
    private function logPrImpressions($jobs): void
    {
        $prJobs = collect($jobs->items())->filter(fn($j) => $j->source !== 'hellowork' && $j->isPrEligible());
        app(\App\Services\RecommendationImpressionLogger::class)->log($prJobs, 'pr_job', 'search');
    }

    /**
     * 「近くのエリアのおすすめ求人(PR)」: 選択エリアと同じ地域の別市町村のスタンダード求人(最大2件)
     * エリア未選択(沖縄全域)の検索では出さない(全件が一覧に含まれるため)
     */
    private function nearbyJobs(array $searchConditionIds): \Illuminate\Support\Collection
    {
        $areaIds = $searchConditionIds['area_ids'] ?? [];
        if (empty($areaIds)) {
            return collect();
        }

        $jobs = app(\App\Services\RecommendedJobFinder::class)
            ->forNearbyAreas($areaIds, $searchConditionIds['job_type_ids'] ?? [], 2);
        app(\App\Services\RecommendationImpressionLogger::class)->log($jobs, 'pr_job', 'search');

        return $jobs;
    }

    /** 周辺エリアの求人を並べる対象: 掲載がこの件数未満の市町村 */
    private const SURROUNDING_THRESHOLD = 10;

    /**
     * 「〇〇周辺(北部エリア)の求人」: 掲載が少ない市町村ページで、同じ地域の別市町村の求人を最大10件。
     * 求人が数件しかないページは検索エンジンに薄いページと見なされやすく、検索した人にとっても選択肢が少ないため。
     * 離島(石垣・宮古・久米島)は互いに通勤圏ではないので出さない。
     *
     * @param int[] $excludeIds すでに「近くのエリアのおすすめ求人」に出している求人
     * @return array{jobs: \Illuminate\Support\Collection, total: int, region: string, areas: array}|null
     */
    private function surroundingJobs(MasterArea $area, int $total, array $excludeIds): ?array
    {
        if ($total >= self::SURROUNDING_THRESHOLD || $area->region === '離島' || ! $area->region) {
            return null;
        }

        $neighbors = MasterArea::active()
            ->where('prefecture', $area->prefecture)
            ->where('region', $area->region)
            ->where('id', '!=', $area->id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);
        if ($neighbors->isEmpty()) {
            return null;
        }
        $neighborIds = $neighbors->pluck('id')->all();

        $base = $this->visibleJobs()
            ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $neighborIds))
            // この市町村の求人(一覧に出ている)は除く
            ->whereDoesntHave('jobAreas', fn($q) => $q->where('area_id', $area->id));

        $count = (clone $base)->count();
        if ($count === 0) {
            return null;
        }

        $jobs = $this->orderForListing($base->whereNotIn('job_listings.id', $excludeIds))
            ->limit(10)
            ->get();

        // スタンダード(PR表記あり)のカードは表示回数を記録
        $prJobs = $jobs->filter(fn($j) => $j->source !== 'hellowork' && $j->isPrEligible());
        app(\App\Services\RecommendationImpressionLogger::class)->log($prJobs, 'pr_job', 'search');

        return [
            'jobs'   => $jobs,
            'total'  => $count,
            'region' => $area->region,
            'areas'  => $neighbors->map(fn($a) => ['name' => $a->name, 'slug' => $a->slug])->all(),
        ];
    }
}
