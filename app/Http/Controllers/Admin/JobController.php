<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index()
    {
        $companies = DB::select("
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

        $companies = collect($companies)->map(function ($c) {
            $c->trial_status = $this->trialStatus($c);
            return $c;
        });

        return view('admin.jobs.index', compact('companies'));
    }

    private function trialStatus(object $c): string
    {
        $billable  = (int) $c->billable_count;
        $valid     = (int) $c->valid_count;
        $trialEnd  = $c->trial_ends_at ? Carbon::parse($c->trial_ends_at) : null;

        if ($billable > 0) return 'billing';

        if (! $trialEnd) return 'active';

        if (now()->greaterThan($trialEnd) || $valid >= 3) return 'ended';

        if ($trialEnd->diffInDays(now(), false) >= -7) return 'ending_soon';

        return 'active';
    }
}
