<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use App\Services\RecommendedJobFinder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $areasByRegion = MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        $jobTypesByCategory = MasterJobType::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $employmentTypes = MasterEmploymentType::active()
            ->orderBy('sort_order')
            ->get();

        $conditionsByCategory = MasterCondition::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $appealsByCategory = MasterAppeal::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $articles = ContentArticle::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // おすすめ求人(PR): スタンダードプランの露出拡大枠・沖縄全域から日替わり最大6件
        $featuredJobs = app(RecommendedJobFinder::class)->forTop(6);

        [$areaJobCounts, $regionJobCounts] = $this->areaJobCounts();

        return view('welcome', compact(
            'areasByRegion',
            'areaJobCounts',
            'regionJobCounts',
            'jobTypesByCategory',
            'employmentTypes',
            'conditionsByCategory',
            'appealsByCategory',
            'articles',
            'featuredJobs'
        ));
    }

    /**
     * エリア検索用の求人数(市町村別・地域別)。検索結果と同じ公開条件で数える。
     * 1求人が複数市町村に紐づくため、地域別は重複を除いて数える。
     * キャッシュはプリミティブ配列のみ。
     *
     * @return array{0: array<int,int>, 1: array<string,int>}
     */
    private function areaJobCounts(): array
    {
        return Cache::remember('home_area_job_counts:v1', now()->addMinutes(10), function () {
            $base = fn() => DB::table('job_areas as ja')
                ->join('job_listings as j', 'j.id', '=', 'ja.job_id')
                ->join('master_areas as a', 'a.id', '=', 'ja.area_id')
                ->where('j.status', 'active')
                ->whereNotNull('j.email_verified_at')
                ->where('j.is_admin_hidden', false)
                ->whereNull('j.deleted_at')
                ->where('a.prefecture', '沖縄県');

            $byArea = $base()
                ->groupBy('ja.area_id')
                ->selectRaw('ja.area_id as k, COUNT(DISTINCT j.id) as c')
                ->pluck('c', 'k')
                ->mapWithKeys(fn($c, $k) => [(int) $k => (int) $c])
                ->all();

            $byRegion = $base()
                ->groupBy('a.region')
                ->selectRaw('a.region as k, COUNT(DISTINCT j.id) as c')
                ->pluck('c', 'k')
                ->map(fn($c) => (int) $c)
                ->all();

            return [$byArea, $byRegion];
        });
    }
}
