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
        // ソフト削除済み・期限切れ求人も対象に検索(410 Gone用)
        $job = Job::with([
                'jobAreas.area',
                'jobJobTypes.jobType',
                'jobEmploymentTypes.employmentType',
                'jobConditions.condition',
                'jobAppeals.appeal',
            ])
            ->withTrashed()
            ->where('token', $token)
            ->first();

        // 存在しない・管理者非表示は404
        if (!$job || $job->is_admin_hidden) {
            abort(404);
        }

        // ソフト削除済み・期限切れ → 「募集終了」ページを HTTP 410 Gone で返す
        $isExpired = $job->trashed()
            || ($job->expires_at && $job->expires_at->isPast())
            || $job->status !== 'active';

        if ($isExpired) {
            $similarJobs = $this->findSimilarActiveJobs($job);
            return response()->view('lp.expired', [
                'job' => $job,
                'similarJobs' => $similarJobs,
            ], 410);
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
     * 募集終了求人の代替として、同エリア・同職種の公開中求人を最大6件返す
     */
    private function findSimilarActiveJobs(Job $job)
    {
        $areaIds    = $job->jobAreas->pluck('area_id')->filter()->all();
        $jobTypeIds = $job->jobJobTypes->pluck('job_type_id')->filter()->all();

        $query = Job::where('status', 'active')
            ->where('is_admin_hidden', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['jobAreas.area', 'jobJobTypes.jobType']);

        if (!empty($areaIds) && !empty($jobTypeIds)) {
            // 両方一致を優先
            $both = (clone $query)
                ->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds))
                ->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds))
                ->limit(6)
                ->get();
            if ($both->count() >= 6) return $both;

            $either = (clone $query)
                ->whereNotIn('id', $both->pluck('id'))
                ->where(function ($q) use ($areaIds, $jobTypeIds) {
                    $q->whereHas('jobAreas', fn($qq) => $qq->whereIn('area_id', $areaIds))
                      ->orWhereHas('jobJobTypes', fn($qq) => $qq->whereIn('job_type_id', $jobTypeIds));
                })
                ->limit(6 - $both->count())
                ->get();
            return $both->merge($either);
        }

        if (!empty($areaIds)) {
            return $query->whereHas('jobAreas', fn($q) => $q->whereIn('area_id', $areaIds))
                ->limit(6)->get();
        }
        if (!empty($jobTypeIds)) {
            return $query->whereHas('jobJobTypes', fn($q) => $q->whereIn('job_type_id', $jobTypeIds))
                ->limit(6)->get();
        }
        return collect();
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
