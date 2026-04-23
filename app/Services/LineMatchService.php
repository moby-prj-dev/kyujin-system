<?php

namespace App\Services;

use App\Models\Job;

class LineMatchService
{
    /**
     * Returns array of mismatch keys: 'job_type', 'employment_type', 'condition'
     * Empty array means all conditions match (or no conditions were selected).
     */
    public function getMismatches(Job $job, array $searchConditions): array
    {
        $job->loadMissing(['jobAreas', 'jobJobTypes', 'jobEmploymentTypes', 'jobConditions']);

        $mismatches = [];

        if (!empty($searchConditions['area_ids'])) {
            $jobAreaIds = $job->jobAreas->pluck('area_id')->map('intval')->toArray();
            $selected   = array_map('intval', $searchConditions['area_ids']);
            if (empty(array_intersect($selected, $jobAreaIds))) {
                $mismatches[] = 'area';
            }
        }

        if (!empty($searchConditions['job_type_ids'])) {
            $jobTypeIds = $job->jobJobTypes->pluck('job_type_id')->map('intval')->toArray();
            $selected   = array_map('intval', $searchConditions['job_type_ids']);
            if (empty(array_intersect($selected, $jobTypeIds))) {
                $mismatches[] = 'job_type';
            }
        }

        if (!empty($searchConditions['employment_type_ids'])) {
            $empTypeIds = $job->jobEmploymentTypes->pluck('employment_type_id')->map('intval')->toArray();
            $selected   = array_map('intval', $searchConditions['employment_type_ids']);
            if (empty(array_intersect($selected, $empTypeIds))) {
                $mismatches[] = 'employment_type';
            }
        }

        if (!empty($searchConditions['condition_ids'])) {
            $condIds  = $job->jobConditions->pluck('condition_id')->map('intval')->toArray();
            $selected = array_map('intval', $searchConditions['condition_ids']);
            if (empty(array_intersect($selected, $condIds))) {
                $mismatches[] = 'condition';
            }
        }

        return $mismatches;
    }

    public function isMatch(Job $job, array $searchConditions): bool
    {
        return empty($this->getMismatches($job, $searchConditions));
    }
}
