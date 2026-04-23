<?php

namespace App\Http\Controllers;

use App\Models\MasterArea;
use App\Models\MasterJobType;

class HelloworkController extends Controller
{
    private function okinawaAreas()
    {
        return MasterArea::active()
            ->where('prefecture', '沖縄県')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');
    }

    public function index()
    {
        $areasByRegion = $this->okinawaAreas();
        $jobTypes      = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');

        return view('hellowork.index', compact('areasByRegion', 'jobTypes'));
    }

    public function area(string $slug)
    {
        $area          = MasterArea::active()->where('slug', $slug)->where('prefecture', '沖縄県')->firstOrFail();
        $areasByRegion = $this->okinawaAreas();
        $jobTypes      = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');

        return view('hellowork.area', compact('area', 'areasByRegion', 'jobTypes'));
    }
}
