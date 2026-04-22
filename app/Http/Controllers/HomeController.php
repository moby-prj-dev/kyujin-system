<?php

namespace App\Http\Controllers;

use App\Models\MasterArea;
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

        return view('welcome', compact('areasByRegion', 'jobTypesByCategory'));
    }
}
