<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;

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
            ->limit(6)
            ->get();

        return view('welcome', compact(
            'areasByRegion',
            'jobTypesByCategory',
            'employmentTypes',
            'conditionsByCategory',
            'appealsByCategory',
            'articles'
        ));
    }
}
