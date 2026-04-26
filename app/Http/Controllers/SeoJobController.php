<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\MasterArea;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use Illuminate\Http\Request;

class SeoJobController extends Controller
{
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
        $currentArea       = null;
        $searchConditionIds = [];

        if ($request->filled('area')) {
            $currentArea = MasterArea::active()->where('slug', $request->area)->first();
        }

        $query = $this->visibleJobs();

        if ($currentArea) {
            $query->whereHas('jobAreas', fn($q) => $q->where('area_id', $currentArea->id));
            $searchConditionIds['area_ids'] = [$currentArea->id];
        } else {
            $query->whereHas('jobAreas.area', fn($q) => $q->where('prefecture', '沖縄県'));
        }

        $filterIds          = $this->applyFilters($query, $request);
        $searchConditionIds = [...$searchConditionIds, ...$filterIds];

        $jobs = $query
            ->orderByRaw("CASE WHEN source = 'care_entry' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20)->withQueryString();

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $pageTitle = $currentArea
            ? "{$currentArea->name}の介護・福祉求人一覧"
            : '沖縄の介護・福祉求人一覧';

        $pageDesc = $currentArea
            ? "{$currentArea->name}で介護・福祉の仕事を探している方向けの求人一覧です。地域の特性に合った求人を掲載しています。"
            : '沖縄で介護・福祉の仕事を探している方向けの求人一覧です。エリアや職種から自分に合う求人を探せます。';

        return view('seo.jobs.index', compact(
            'jobs', 'currentArea', 'areas', 'pageTitle', 'pageDesc', 'searchConditionIds'
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
            ->orderByRaw("CASE WHEN source = 'care_entry' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20)->withQueryString();

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        return view('seo.jobs.index', [
            'jobs'               => $jobs,
            'currentArea'        => $currentArea,
            'areas'              => $areas,
            'pageTitle'          => "{$currentArea->name}の介護・福祉求人一覧",
            'pageDesc'           => "{$currentArea->name}で介護・福祉の仕事を探している方向けの求人一覧です。地域の特性に合った求人を掲載しています。",
            'searchConditionIds' => $searchConditionIds,
        ]);
    }
}
