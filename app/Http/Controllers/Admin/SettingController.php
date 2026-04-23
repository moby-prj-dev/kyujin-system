<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $startDate = Setting::monitorStartDate();
        $months    = Setting::monitorMonths();
        $cutoff    = Setting::monitorCutoffDate();

        return view('admin.settings.index', compact('startDate', 'months', 'cutoff'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'monitor_start_date' => ['required', 'date'],
            'monitor_months'     => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        Setting::set('monitor_start_date', $request->monitor_start_date);
        Setting::set('monitor_months', $request->monitor_months);

        return back()->with('success', 'モニター設定を更新しました。');
    }
}
