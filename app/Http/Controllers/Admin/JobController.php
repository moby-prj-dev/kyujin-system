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
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND deleted_at IS NULL) AS applications_count,
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 1 AND deleted_at IS NULL) AS valid_count_sub,
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_valid = 0 AND deleted_at IS NULL) AS invalid_count_sub,
                (SELECT COUNT(*) FROM applications WHERE job_id = job_listings.id AND is_billable = 1 AND deleted_at IS NULL) AS billable_count_sub
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
                'ended'       => $jobsQuery->where('expires_at', '<', now()),
                'ending_soon' => $jobsQuery->where('expires_at', '>=', now())
                                           ->where('expires_at', '<=', now()->addDays(7)),
                'active'      => $jobsQuery->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()->addDays(7))),
                default       => null,
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

    public function toggleMonitor(Job $job)
    {
        if (!$job->is_monitor) {
            // モニターに設定：無料期間を3ヶ月セット
            $monitorEndsAt = now()->addMonths(3);
            $job->update([
                'is_monitor'      => true,
                'monitor_ends_at' => $monitorEndsAt,
                'expires_at'      => $monitorEndsAt,
            ]);
            $msg = '無料モニターに設定しました。（' . $monitorEndsAt->format('Y/m/d') . 'まで無料）';
        } else {
            // モニター解除：monitor_ends_at はそのまま（期限まで課金なし）
            $job->update(['is_monitor' => false]);
            $msg = 'モニターを解除しました。（' . ($job->monitor_ends_at?->format('Y/m/d') . 'まで課金なし）');
        }

        return back()->with('success', $msg);
    }

    public function togglePermanentlyFree(Job $job)
    {
        $newVal = !$job->is_permanently_free;
        $job->update(['is_permanently_free' => $newVal]);
        $msg = $newVal ? '永久無料に設定しました。' : '永久無料を解除しました。';
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

    private function getCompanies(): \Illuminate\Support\Collection
    {
        $rows = DB::select("
            SELECT
                j.contact_email,
                MAX(j.company_name)       AS company_name,
                MIN(j.email_verified_at)  AS first_activated_at,
                MIN(j.monitor_ends_at)           AS trial_ends_at,
                MAX(j.is_permanently_free)       AS is_permanently_free,
                COUNT(DISTINCT j.id)             AS listing_count,
                COALESCE(SUM(CASE WHEN a.is_valid = 1 THEN 1 ELSE 0 END), 0) AS valid_count,
                COALESCE(SUM(CASE WHEN a.is_valid = 0 THEN 1 ELSE 0 END), 0) AS invalid_count,
                COALESCE(SUM(CASE WHEN a.is_billable = 1 THEN 1 ELSE 0 END), 0) AS billable_count
            FROM job_listings j
            LEFT JOIN applications a ON a.job_id = j.id AND a.deleted_at IS NULL
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

    public function destroy(Job $job): \Illuminate\Http\RedirectResponse
    {
        DB::transaction(function () use ($job) {
            $appIds = DB::table('applications')->where('job_id', $job->id)->pluck('id');
            if ($appIds->isNotEmpty()) {
                DB::table('billings')->whereIn('application_id', $appIds)->delete();
                DB::table('application_notifications')->whereIn('application_id', $appIds)->delete();
            }
            DB::table('billings')->where('job_id', $job->id)->delete();
            DB::table('applications')->where('job_id', $job->id)->delete();
            $job->forceDelete();
        });

        return redirect()->route('admin.jobs.index')->with('success', '求人を削除しました。');
    }



    private function companyTrialStatus(object $c): string
    {
        $billable = (int) $c->billable_count;
        $valid    = (int) $c->valid_count;
        $trialEnd = $c->trial_ends_at ? Carbon::parse($c->trial_ends_at) : null;

        if ((bool) $c->is_permanently_free)                       return 'permanently_free';
        if ($billable > 0)                                        return 'billing';
        if (!$trialEnd)                                           return 'active';
        if (now()->greaterThan($trialEnd) || $valid >= 3)         return 'ended';
        if ($trialEnd->diffInDays(now(), false) >= -7)            return 'ending_soon';

        return 'active';
    }
}
