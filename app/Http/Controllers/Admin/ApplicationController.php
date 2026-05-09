<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['job:id,company_name,contact_email,token,title'])
            ->orderByDesc('applied_at');

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->filled('email')) {
            $query->where(fn($q) =>
                $q->where('email', 'like', '%' . $request->email . '%')
                  ->orWhereHas('job', fn($q2) => $q2->where('contact_email', 'like', '%' . $request->email . '%'))
            );
        }

        if ($request->filled('validity')) {
            $query->where('is_valid', $request->validity === 'valid');
        }

        if ($request->filled('billable')) {
            $query->where('is_billable', $request->billable === '1');
        }

        $applications = $query->paginate(50)->withQueryString();

        $filterJob = $request->filled('job_id')
            ? Job::find($request->job_id)
            : null;

        $summaryQuery = Application::query();
        if ($request->filled('job_id'))   { $summaryQuery->where('job_id', $request->job_id); }
        if ($request->filled('email'))    { $summaryQuery->where(fn($q) => $q->where('email', 'like', '%'.$request->email.'%')->orWhereHas('job', fn($q2) => $q2->where('contact_email', 'like', '%'.$request->email.'%'))); }
        if ($request->filled('validity')) { $summaryQuery->where('is_valid', $request->validity === 'valid'); }
        if ($request->filled('billable')) { $summaryQuery->where('is_billable', $request->billable === '1'); }

        $summaryCounts = $summaryQuery->selectRaw('
            COUNT(*) AS total,
            SUM(is_valid = 1) AS valid_count,
            SUM(is_valid = 0) AS invalid_count,
            SUM(is_billable = 1) AS billable_count
        ')->first();

        return view('admin.applications.index', compact('applications', 'filterJob', 'summaryCounts'));
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
            $application->is_billable    = false;
        } elseif ($action === 'invalidate') {
            $application->is_valid       = false;
            $application->invalid_reason = Application::INVALID_MANUAL;
            $application->is_billable    = false;
        }

        $application->save();

        return back()->with('success', '応募情報を更新しました。');
    }

    public function destroy(Application $application)
    {
        DB::transaction(function () use ($application) {
            DB::table('billings')->where('application_id', $application->id)->delete();
            DB::table('application_notifications')->where('application_id', $application->id)->delete();
            $application->delete();
        });

        return back()->with('success', '応募データを削除しました。');
    }
}
