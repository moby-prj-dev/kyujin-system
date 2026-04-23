<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class ClientController extends Controller
{
    public function index()
    {
        $monitorCutoff = Setting::monitorCutoffDate();
        return view('client.index', compact('monitorCutoff'));
    }

    public function facebook()
    {
        $monitorCutoff = Setting::monitorCutoffDate();
        return view('pages.facebook_lp', compact('monitorCutoff'));
    }
}
