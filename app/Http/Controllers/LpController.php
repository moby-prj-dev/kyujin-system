<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
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

        $relatedArticles = $this->findRelatedArticles($job);

        return view('lp.show', compact('job', 'relatedArticles'));
    }

    /**
     * 求人のエリア/職種と一致する公開中の記事を探す
     * - 両方一致を優先・足りない場合は片方一致で補完
     * - 最大3件
     */
    private function findRelatedArticles(Job $job)
    {
        $areaIds    = $job->jobAreas->pluck('area_id')->filter()->all();
        $jobTypeIds = $job->jobJobTypes->pluck('job_type_id')->filter()->all();

        if (empty($areaIds) && empty($jobTypeIds)) {
            return collect();
        }

        $base = ContentArticle::published()
            ->with(['area:id,name', 'jobType:id,name']);

        // 両方一致を優先
        if (!empty($areaIds) && !empty($jobTypeIds)) {
            $both = (clone $base)
                ->whereIn('area_id', $areaIds)
                ->whereIn('job_type_id', $jobTypeIds)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get();
            if ($both->count() >= 3) return $both;

            $excludeIds = $both->pluck('id')->all();
            $either = (clone $base)
                ->whereNotIn('id', $excludeIds)
                ->where(function ($q) use ($areaIds, $jobTypeIds) {
                    $q->whereIn('area_id', $areaIds)->orWhereIn('job_type_id', $jobTypeIds);
                })
                ->orderByDesc('published_at')
                ->limit(3 - $both->count())
                ->get();
            return $both->merge($either);
        }

        $query = clone $base;
        if (!empty($areaIds)) {
            $query->whereIn('area_id', $areaIds);
        } else {
            $query->whereIn('job_type_id', $jobTypeIds);
        }
        return $query->orderByDesc('published_at')->limit(3)->get();
    }
}
