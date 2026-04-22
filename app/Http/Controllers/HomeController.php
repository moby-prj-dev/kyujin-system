<?php

namespace App\Http\Controllers;

use App\Models\MasterArea;
use App\Models\MasterJobType;

class HomeController extends Controller
{
    public function index()
    {
        $areas    = MasterArea::active()->orderBy('sort_order')->get();
        $jobTypes = MasterJobType::active()->orderBy('sort_order')->get();
        return view('welcome', compact('areas', 'jobTypes'));
    }
}
