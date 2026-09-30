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
    // 表示順: 注目(スタンダード限定オプション) → スタンダード → ベーシック → ハローワーク
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

        $jobs = $query
            ->orderByRaw(self::RANK_ORDER_SQL)
            ->latest()
            ->paginate(20)->withQueryString();

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

        return view('seo.jobs.index', compact(
            'jobs', 'currentArea', 'currentJobType', 'areas', 'stats',
            'pageTitle', 'pageDesc', 'searchConditionIds'
        ));
    }

    public function area(Request $request, string $slug)
    {
        $currentArea = MasterArea::active()->where('slug', $slug)->firstOrFail();

        $query = $this->visibleJobs()
            ->whereHas('jobAreas', fn($q) => $q->where('area_id', $currentArea->id));

        $filterIds          = $this->applyFilters($query, $request);
        $searchConditionIds = ['area_ids' => [$currentArea->id], ...$filterIds];

        $jobs = $query
            ->orderByRaw(self::RANK_ORDER_SQL)
            ->latest()
            ->paginate(20)->withQueryString();

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $stats = $this->statsFor($currentArea, null);

        $total = $stats['total'] ?? 0;
        return view('seo.jobs.index', [
            'jobs'               => $jobs,
            'currentArea'        => $currentArea,
            'currentJobType'     => null,
            'areas'              => $areas,
            'stats'              => $stats,
            'pageTitle'          => "【沖縄県{$currentArea->name}】介護・福祉の求人 " . ($total > 0 ? "{$total}件" : '募集'),
            'pageDesc'           => "沖縄県{$currentArea->name}の介護・福祉求人" . ($total > 0 ? "を{$total}件掲載中" : "の最新募集情報") . "。介護職員・ヘルパー・看護・保育など職種から絞り込み可能。応募がなければ掲載料無料のCare Entry(ケアエントリー)。",
            'searchConditionIds' => $searchConditionIds,
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

        $jobs = $query
            ->orderByRaw(self::RANK_ORDER_SQL)
            ->latest()
            ->paginate(20)->withQueryString();

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $stats = $this->statsFor($currentArea, $currentJobType);

        $total = $stats['total'] ?? 0;
        return view('seo.jobs.index', [
            'jobs'               => $jobs,
            'currentArea'        => $currentArea,
            'currentJobType'     => $currentJobType,
            'areas'              => $areas,
            'stats'              => $stats,
            'pageTitle'          => "【沖縄県{$currentArea->name}】{$currentJobType->name}の求人 " . ($total > 0 ? "{$total}件" : '募集'),
            'pageDesc'           => "沖縄県{$currentArea->name}の{$currentJobType->name}求人" . ($total > 0 ? "を{$total}件掲載中" : "の最新募集情報") . "。給与・雇用形態(正社員・パート・契約社員)から絞り込み検索が可能。応募がなければ掲載料無料のCare Entry。",
            'searchConditionIds' => $searchConditionIds,
        ]);
    }

    /**
     * エリア(+職種)に対する独自統計を1時間キャッシュ。プリミティブのみ返す。
     */
    private function statsFor(MasterArea $area, ?MasterJobType $jobType): array
    {
        $key = "seo_jobs_stats:v1:a{$area->id}:" . ($jobType ? "j{$jobType->id}" : 'jall');
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

            // 月給統計
            $salaries = (clone $base)
                ->where('salary_type', 'monthly')
                ->whereNotNull('salary_min')
                ->where('salary_min', '>', 0)
                ->pluck('salary_min')
                ->map(fn($v) => (int) $v)
                ->sort()
                ->values();

            $salaryMin = $salaryMax = $salaryMedian = null;
            if ($salaries->count() >= 3) {
                $salaryMin    = (int) $salaries->min();
                $salaryMax    = (int) $salaries->max();
                $n = $salaries->count();
                $salaryMedian = $n % 2 === 0
                    ? (int) (($salaries[(int) ($n/2) - 1] + $salaries[(int) ($n/2)]) / 2)
                    : (int) $salaries[(int) ($n/2)];
            }

            // 雇用形態内訳(プリミティブ配列)
            $empBreakdown = [];
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

            return [
                'total'         => $total,
                'own_count'     => $ownCount,
                'hw_count'      => $hwCount,
                'salary_min'    => $salaryMin,
                'salary_max'    => $salaryMax,
                'salary_median' => $salaryMedian,
                'emp_breakdown' => $empBreakdown,
            ];
        });
    }
}
