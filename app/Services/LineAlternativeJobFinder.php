<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

class LineAlternativeJobFinder
{
    // スタンダード(露出拡大)の自社求人を各パス内で優先
    private const STANDARD_FIRST_SQL = "CASE WHEN plan = 'standard' AND source = 'care_entry' THEN 0 ELSE 1 END";

    public function find(Job $excludeJob, array $searchConditions, int $limit = 3): Collection
    {
        $jobTypeIds = array_map('intval', $searchConditions['job_type_ids'] ?? []);
        $areaIds    = array_map('intval', $searchConditions['area_ids'] ?? []);

        $base = fn() => Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where('id', '!=', $excludeJob->id)
            ->with(['jobAreas.area', 'jobJobTypes.jobType'])
            ->orderByRaw(self::STANDARD_FIRST_SQL);

        // 1st pass: same job type
        if (!empty($jobTypeIds)) {
            $results = $base()
                ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds))
                ->latest()
                ->limit($limit)
                ->get();

            if ($results->count() >= $limit) {
                return $results;
            }
        } else {
            $results = new Collection();
        }

        // 2nd pass: same area, any job type
        if ($results->count() < $limit && !empty($areaIds)) {
            $fallback = $base()
                ->whereNotIn('id', $results->pluck('id'))
                ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds))
                ->latest()
                ->limit($limit - $results->count())
                ->get();

            $results = $results->concat($fallback);
        }

        // 3rd pass: any Okinawa job
        if ($results->count() < $limit) {
            $fallback = $base()
                ->whereNotIn('id', $results->pluck('id'))
                ->latest()
                ->limit($limit - $results->count())
                ->get();

            $results = $results->concat($fallback);
        }

        return $results;
    }
}
