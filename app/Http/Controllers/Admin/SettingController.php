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
        $disabled  = Setting::isMonitorDisabled();

        return view('admin.settings.index', compact('startDate', 'months', 'cutoff', 'disabled'));
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

    public function toggleMonitor(Request $request)
    {
        $disable = $request->boolean('disable');
        Setting::set('monitor_disabled', $disable ? '1' : '0');

        return back()->with(
            'success',
            $disable ? 'モニターを解除しました(全モニターUIが非表示になります)。' : 'モニターを再開しました。'
        );
    }
}
