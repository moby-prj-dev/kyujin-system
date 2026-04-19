<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationValidationService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['job:id,company_name,contact_email,token'])
            ->orderByDesc('applied_at');

        if ($request->filled('email')) {
            $query->where('contact_email', 'like', '%' . $request->email . '%')
                  ->orWhereHas('job', fn($q) => $q->where('contact_email', 'like', '%' . $request->email . '%'));
        }

        if ($request->filled('validity')) {
            $query->where('is_valid', $request->validity === 'valid');
        }

        if ($request->filled('billable')) {
            $query->where('is_billable', $request->billable === '1');
        }

        $applications = $query->paginate(50)->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function update(Request $request, Application $application, ApplicationValidationService $service)
    {
        $action = $request->input('action');

        if ($action === 'validate') {
            if (empty($application->normalized_email)) {
                $application->normalized_email = $service->normalizeEmail($application->email);
            }
            if (empty($application->normalized_phone)) {
                $application->normalized_phone = $service->normalizePhone($application->phone);
            }
            $application->is_valid       = true;
            $application->invalid_reason = null;
            $application->counted_at     = $application->counted_at ?? now();
            $application->is_billable    = $service->isBillable($application);
        } elseif ($action === 'invalidate') {
            $application->is_valid       = false;
            $application->invalid_reason = Application::INVALID_MANUAL;
            $application->is_billable    = false;
        }

        $application->save();

        return back()->with('success', '応募情報を更新しました。');
    }
}
