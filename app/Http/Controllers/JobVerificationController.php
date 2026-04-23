<?php

namespace App\Http\Controllers;

use App\Mail\AdminJobNotificationMail;
use App\Models\AuditLog;
use App\Models\Job;
use App\Services\SeoGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobVerificationController extends Controller
{
    public function verify(string $verificationToken, SeoGeneratorService $seoGenerator)
    {
        $job = Job::where('email_verification_token', $verificationToken)
            ->where('status', Job::STATUS_PENDING)
            ->firstOrFail();

        DB::transaction(function () use ($job, $seoGenerator) {
            $job->update([
                'email_verified_at'       => now(),
                'email_verification_token' => null,
            ]);

            $job->load(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            $seoGenerator->generate($job);

            $job->update([
                'status'     => Job::STATUS_ACTIVE,
                'expires_at' => $job->is_monitor ? null : now()->addMonths(3),
            ]);

            AuditLog::record(AuditLog::ENTITY_JOB, $job->id, AuditLog::ACTION_JOB_CREATED, AuditLog::ACTOR_SYSTEM, [
                'contact_email' => $job->contact_email,
                'status'        => $job->status,
            ]);
        });

        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new AdminJobNotificationMail($job));
        }

        return redirect()->route('jobs.manage', ['token' => $job->token])
            ->with('verified', true);
    }
}
