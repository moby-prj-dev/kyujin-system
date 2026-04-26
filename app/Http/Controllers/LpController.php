<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\LpView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LpController extends Controller
{
    public function show(Request $request, string $token)
    {
        $job = Job::with([
                'jobAreas.area',
                'jobJobTypes.jobType',
                'jobEmploymentTypes.employmentType',
                'jobConditions.condition',
                'jobAppeals.appeal',
            ])
            ->where('token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(404);
        }

        if ($job->is_admin_hidden) {
            abort(404);
        }

        try {
            LpView::create([
                'job_id'     => $job->id,
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            ]);
        } catch (\Exception $e) {
            \Log::warning('lp_view記録失敗: ' . $e->getMessage());
        }

        return view('lp.show', compact('job'));
    }
}
