<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDispute;
use App\Services\ApplicationValidationService;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index()
    {
        $disputes = ApplicationDispute::with(['application.job'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.disputes.index', compact('disputes'));
    }

    public function approve(Request $request, ApplicationDispute $dispute)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $dispute->update([
            'status'      => ApplicationDispute::STATUS_APPROVED,
            'admin_note'  => $request->admin_note,
            'resolved_at' => now(),
        ]);

        $app = $dispute->application;
        $app->is_valid       = false;
        $app->invalid_reason = Application::INVALID_EMPLOYER;
        $app->is_billable    = false;
        $app->save();

        return back()->with('success', '申告を承認し、応募を無効化しました。');
    }

    public function reject(Request $request, ApplicationDispute $dispute)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $dispute->update([
            'status'      => ApplicationDispute::STATUS_REJECTED,
            'admin_note'  => $request->admin_note,
            'resolved_at' => now(),
        ]);

        return back()->with('success', '申告を却下しました。');
    }
}
