<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
