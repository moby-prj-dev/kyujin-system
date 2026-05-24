<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\Job;

class ContentArticleController extends Controller
{
    public function index()
    {
        $articles = ContentArticle::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article  = ContentArticle::published()->where('slug', $slug)->firstOrFail();
        $related  = ContentArticle::published()
            ->where('slug', '!=', $slug)
            ->where(function ($q) use ($article) {
                $q->where('category', $article->category)
                  ->orWhere('area_id', $article->area_id)
                  ->orWhere('job_type_id', $article->job_type_id);
            })
            ->limit(4)
            ->get();

        $relatedJobs = $this->findRelatedJobs($article);

        return view('articles.show', compact('article', 'related', 'relatedJobs'));
    }

    /**
     * 記事のエリア/職種と一致する公開中の求人を探す
     * - area_id と job_type_id 両方ある → 両方一致を優先
     * - どちらか一方なら一方一致
     * - 不足する場合は他方の一致のみでも補完
     */
    private function findRelatedJobs(ContentArticle $article)
    {
        $jobsBase = Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType']);

        $hasArea    = !empty($article->area_id);
        $hasJobType = !empty($article->job_type_id);

        // 両方一致を優先
        if ($hasArea && $hasJobType) {
            $both = (clone $jobsBase)
                ->whereHas('jobAreas', fn($q) => $q->where('area_id', $article->area_id))
                ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $article->job_type_id))
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
            if ($both->count() >= 6) return $both;

            // 不足分を片方一致で補完
            $excludeIds = $both->pluck('id')->all();
            $either = (clone $jobsBase)
                ->whereNotIn('id', $excludeIds)
                ->where(function ($q) use ($article) {
                    $q->whereHas('jobAreas', fn($q2) => $q2->where('area_id', $article->area_id))
                      ->orWhereHas('jobJobTypes', fn($q2) => $q2->where('job_type_id', $article->job_type_id));
                })
                ->orderByDesc('created_at')
                ->limit(6 - $both->count())
                ->get();
            return $both->merge($either);
        }

        if ($hasArea) {
            return (clone $jobsBase)
                ->whereHas('jobAreas', fn($q) => $q->where('area_id', $article->area_id))
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }

        if ($hasJobType) {
            return (clone $jobsBase)
                ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $article->job_type_id))
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }

        return collect();
    }
}
