<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\MasterArea;
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

    public function index()
    {
        $jobs = $this->visibleJobs()
            ->whereHas('jobAreas.area', fn($q) => $q->where('prefecture', '沖縄県'))
            ->latest()
            ->paginate(20);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        return view('seo.jobs.index', [
            'jobs'        => $jobs,
            'currentArea' => null,
            'areas'       => $areas,
            'pageTitle'   => '沖縄の介護・福祉求人一覧',
            'pageDesc'    => '沖縄で介護・福祉の仕事を探している方向けの求人一覧です。エリアや職種から自分に合う求人を探せます。',
        ]);
    }

    public function area(string $slug)
    {
        $currentArea = MasterArea::active()->where('slug', $slug)->firstOrFail();

        $jobs = $this->visibleJobs()
            ->whereHas('jobAreas', fn($q) => $q->where('area_id', $currentArea->id))
            ->latest()
            ->paginate(20);

        $areas = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        return view('seo.jobs.index', [
            'jobs'        => $jobs,
            'currentArea' => $currentArea,
            'areas'       => $areas,
            'pageTitle'   => $currentArea->name . 'の介護・福祉求人一覧',
            'pageDesc'    => $currentArea->name . 'で介護・福祉の仕事を探している方向けの求人一覧です。地域の特性に合った求人を掲載しています。',
        ]);
    }
}
