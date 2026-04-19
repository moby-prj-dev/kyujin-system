<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\LpView;
use Illuminate\Http\Request;

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
        ])->active()->byToken($token)->firstOrFail();

        if ($job->expires_at && $job->expires_at->isPast()) {
            abort(404);
        }

        LpView::create([
            'job_id'     => $job->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'viewed_at'  => now(),
        ]);

        return view('lp.show', compact('job'));
    }
}
