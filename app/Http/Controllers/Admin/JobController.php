<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobArea;
use App\Models\JobAppeal;
use App\Models\JobCondition;
use App\Models\JobEmploymentType;
use App\Models\JobJobType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobsQuery = Job::query()
            ->selectRaw('
                job_listings.*,
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id) AS applications_count,
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 1) AS valid_count_sub
            ')
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $jobsQuery->where(fn($q) =>
                $q->where('company_name', 'like', $s)->orWhere('title', 'like', $s)
            );
        }

        if ($request->filled('status')) {
            $jobsQuery->where('status', $request->status);
        }

        if ($request->filled('trial_status')) {
            match ($request->trial_status) {
                'ended' => $jobsQuery->where(fn($q) =>
                    $q->where('expires_at', '<', now())
                      ->orWhereRaw('(SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 1) >= 3')
                ),
                'ending_soon' => $jobsQuery
                    ->where('expires_at', '>=', now())
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->whereRaw('(SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 1) < 3'),
                'active' => $jobsQuery
                    ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()->addDays(7)))
                    ->whereRaw('(SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 1) < 3'),
                default => null,
            };
        }

        if ($request->filled('continued')) {
            $request->continued === '1'
                ? $jobsQuery->whereNotNull('continued_at')
                : $jobsQuery->whereNull('continued_at');
        }

        $jobs = $jobsQuery->orderByDesc('created_at')->paginate(15)->withQueryString();

        $companies = $this->getCompanies();

        return view('admin.jobs.index', compact('jobs', 'companies'));
    }

    public function toggleHidden(Job $job)
    {
        $job->update(['is_admin_hidden' => !$job->is_admin_hidden]);

        $msg = $job->is_admin_hidden ? '求人を非公開にしました。' : '求人を再公開しました。';
        return back()->with('success', $msg);
    }

    public function updateMemo(Request $request, Job $job)
    {
        $request->validate(['admin_memo' => ['nullable', 'string', 'max:2000']]);

        $job->update([
            'admin_memo'            => $request->admin_memo,
            'admin_memo_updated_at' => now(),
        ]);

        return back()->with('success', 'メモを保存しました。');
    }

    public function duplicate(Job $job)
    {
        $newJob = $job->replicate([
            'token',
            'status',
            'email_verification_token',
            'email_verified_at',
            'expires_at',
            'paused_at',
            'continued_at',
            'continue_notified_at',
            'trial_warning_sent_at',
            'expired_notified_at',
            'is_admin_hidden',
            'admin_memo',
            'admin_memo_updated_at',
            'seo_title',
            'subtitle',
            'lp_tags',
            'meta_description',
            'description_generated',
        ]);

        $newJob->token  = Str::random(32);
        $newJob->status = Job::STATUS_DRAFT;
        $newJob->title  = ($job->title ? $job->title . '（コピー）' : '（コピー）');
        $newJob->save();

        foreach ($job->jobAreas as $r)           { JobArea::create(['job_id' => $newJob->id, 'area_id' => $r->area_id]); }
        foreach ($job->jobJobTypes as $r)        { JobJobType::create(['job_id' => $newJob->id, 'job_type_id' => $r->job_type_id]); }
        foreach ($job->jobEmploymentTypes as $r) { JobEmploymentType::create(['job_id' => $newJob->id, 'employment_type_id' => $r->employment_type_id]); }
        foreach ($job->jobConditions as $r)      { JobCondition::create(['job_id' => $newJob->id, 'condition_id' => $r->condition_id]); }
        foreach ($job->jobAppeals as $r)         { JobAppeal::create(['job_id' => $newJob->id, 'appeal_id' => $r->appeal_id]); }

        return back()->with('success', "求人を複製しました（ID: {$newJob->id}）。管理画面の求人一覧から確認できます。");
    }

    private function getCompanies(): \Illuminate\Support\Collection
    {
        $rows = DB::select("
            SELECT
                j.contact_email,
                MAX(j.company_name)       AS company_name,
                MIN(j.email_verified_at)  AS first_activated_at,
                MIN(j.expires_at)         AS trial_ends_at,
                COUNT(DISTINCT j.id)      AS listing_count,
                COALESCE(SUM(CASE WHEN a.is_valid = 1 THEN 1 ELSE 0 END), 0) AS valid_count,
                COALESCE(SUM(CASE WHEN a.is_valid = 0 THEN 1 ELSE 0 END), 0) AS invalid_count,
                COALESCE(SUM(CASE WHEN a.is_billable = 1 THEN 1 ELSE 0 END), 0) AS billable_count
            FROM job_listings j
            LEFT JOIN applications a ON a.job_id = j.id
            WHERE j.email_verified_at IS NOT NULL
              AND j.deleted_at IS NULL
            GROUP BY j.contact_email
            ORDER BY first_activated_at DESC
        ");

        return collect($rows)->map(function ($c) {
            $c->trial_status = $this->companyTrialStatus($c);
            return $c;
        });
    }

    private function companyTrialStatus(object $c): string
    {
        $billable = (int) $c->billable_count;
        $valid    = (int) $c->valid_count;
        $trialEnd = $c->trial_ends_at ? Carbon::parse($c->trial_ends_at) : null;

        if ($billable > 0)  return 'billing';
        if (!$trialEnd)     return 'active';
        if (now()->greaterThan($trialEnd) || $valid >= 3) return 'ended';
        if ($trialEnd->diffInDays(now(), false) >= -7)    return 'ending_soon';

        return 'active';
    }
}
