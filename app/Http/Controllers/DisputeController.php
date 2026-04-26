<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDispute;
use App\Models\Job;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function create(string $token, Application $application)
    {
        $job = Job::where('manage_token', $token)->firstOrFail();

        abort_unless($application->job_id === $job->id, 403);
        abort_unless($application->is_valid, 403, 'すでに無効な応募です。');
        abort_if($application->disputes()->where('status', '!=', 'rejected')->exists(), 403, 'すでに申告済みです。');

        $monthlyCount = ApplicationDispute::monthlyCountForJob($job->contact_email);
        abort_if($monthlyCount >= ApplicationDispute::MONTHLY_LIMIT, 403, '今月の申告上限（3件）に達しています。');

        return view('disputes.create', compact('job', 'application', 'token', 'monthlyCount'));
    }

    public function store(Request $request, string $token, Application $application)
    {
        $job = Job::where('manage_token', $token)->firstOrFail();

        abort_unless($application->job_id === $job->id, 403);
        abort_unless($application->is_valid, 403);
        abort_if($application->disputes()->where('status', '!=', 'rejected')->exists(), 403);

        $monthlyCount = ApplicationDispute::monthlyCountForJob($job->contact_email);
        abort_if($monthlyCount >= ApplicationDispute::MONTHLY_LIMIT, 403);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $application->disputes()->create([
            'job_token' => $token,
            'reason'    => $request->reason,
            'status'    => ApplicationDispute::STATUS_PENDING,
        ]);

        return redirect()->route('jobs.manage', $token)
            ->with('success', '無効申告を受け付けました。内容確認後、メールにてご連絡します。');
    }
}
