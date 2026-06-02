<?php

namespace App\Http\Controllers;

use App\Models\AreaStatistic;
use App\Models\ContentArticle;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $areaStats   = $this->fetchAreaStats($article);
        $publicStat  = $this->fetchPublicStat($article);

        return view('articles.show', compact('article', 'related', 'relatedJobs', 'areaStats', 'publicStat'));
    }

    /**
     * e-Stat 賃金センサスのキャッシュから記事の職種に最も合うレコードを返す
     */
    private function fetchPublicStat(ContentArticle $article): ?AreaStatistic
    {
        return AreaStatistic::findForJobTypeName('沖縄県', $article->jobType?->name);
    }

    /**
     * 記事のエリア(または職種)に紐づく自社DBの統計情報を集計
     * Care Entry独自の「ライブデータ」として記事ページに表示し、
     * SEO上の独自性とユーザーへの訴求力を高める。
     */
    private function fetchAreaStats(ContentArticle $article): ?array
    {
        $cacheKey = "article_stats:v1:{$article->id}";
        return Cache::remember($cacheKey, now()->addHour(), function () use ($article) {
            return $this->computeAreaStats($article);
        });
    }

    private function computeAreaStats(ContentArticle $article): ?array
    {
        $hasArea    = !empty($article->area_id);
        $hasJobType = !empty($article->job_type_id);

        $baseQuery = fn() => Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        $jobsQuery = $baseQuery();

        // エリア/職種で絞り込み(片方以上ある場合)
        if ($hasArea || $hasJobType) {
            $jobsQuery->where(function ($q) use ($article, $hasArea, $hasJobType) {
                if ($hasArea) {
                    $q->orWhereHas('jobAreas', fn($q2) => $q2->where('area_id', $article->area_id));
                }
                if ($hasJobType) {
                    $q->orWhereHas('jobJobTypes', fn($q2) => $q2->where('job_type_id', $article->job_type_id));
                }
            });

            // 絞り込みで0件ならフォールバック: 沖縄全体で集計
            if ($jobsQuery->count() === 0) {
                $jobsQuery = $baseQuery();
                $hasArea = false;
                $hasJobType = false;
            }
        }

        $jobs = $jobsQuery->get(['id', 'salary_min', 'salary_max', 'salary_type', 'source']);
        $total = $jobs->count();

        if ($total === 0) {
            return null;
        }

        // 自社/ハローワーク内訳(取得済みコレクションから集計)
        $ownCount = $jobs->filter(fn($j) => $j->source !== 'hellowork')->count();
        $hwCount  = $total - $ownCount;

        // 職種別件数 トップ3(キャッシュデシリアライズ事故を避けるため Collection ではなく配列で保存)
        $jobIds = $jobs->pluck('id');
        $byJobType = DB::table('job_job_types')
            ->join('master_job_types', 'job_job_types.job_type_id', '=', 'master_job_types.id')
            ->whereIn('job_job_types.job_id', $jobIds)
            ->groupBy('master_job_types.id', 'master_job_types.name')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->limit(3)
            ->select('master_job_types.name', DB::raw('COUNT(*) as count'))
            ->get()
            ->map(fn($row) => ['name' => $row->name, 'count' => (int) $row->count])
            ->values()
            ->all();

        // 給与レンジ(月給のみ・有効値のみ)
        $monthlySalaries = $jobs
            ->where('salary_type', 'monthly')
            ->pluck('salary_min')
            ->filter(fn($s) => $s > 0)
            ->values();

        $salaryStats = null;
        if ($monthlySalaries->count() >= 3) {
            $salaryStats = [
                'min'    => (int) $monthlySalaries->min(),
                'max'    => (int) $monthlySalaries->max(),
                'median' => (int) $monthlySalaries->median(),
                'count'  => $monthlySalaries->count(),
            ];
        }

        return [
            'total'        => $total,
            'own_count'    => $ownCount,
            'hw_count'     => $hwCount,
            'by_job_type'  => $byJobType,
            'salary'       => $salaryStats,
            'area_name'    => $article->area?->name,
            'job_type_name'=> $article->jobType?->name,
        ];
    }

    /**
     * 記事のエリア/職種と一致する公開中の求人を探す
     * - area_id と job_type_id 両方ある → 両方一致を優先
     * - どちらか一方なら一方一致
     * - 不足する場合は他方の一致のみでも補完
     * - 自社(Care Entry)登録求人 を ハローワーク求人 より上に表示
     */
    private function findRelatedJobs(ContentArticle $article)
    {
        // 自社求人(source != 'hellowork')→ HW求人(source = 'hellowork') の順
        $priorityOrder = "CASE WHEN source = 'hellowork' THEN 1 ELSE 0 END ASC";

        $jobsBase = Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['jobAreas.area', 'jobJobTypes.jobType', 'jobEmploymentTypes.employmentType']);

        $hasArea    = !empty($article->area_id);
        $hasJobType = !empty($article->job_type_id);

        $fallbackLatest = fn() => (clone $jobsBase)
            ->orderByRaw($priorityOrder)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // エリア・職種が紐付いていない汎用記事(業界情報など)は沖縄全体のおすすめ求人
        if (!$hasArea && !$hasJobType) {
            return $fallbackLatest();
        }

        // 両方一致を優先
        if ($hasArea && $hasJobType) {
            $both = (clone $jobsBase)
                ->whereHas('jobAreas', fn($q) => $q->where('area_id', $article->area_id))
                ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $article->job_type_id))
                ->orderByRaw($priorityOrder)
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
                ->orderByRaw($priorityOrder)
                ->orderByDesc('created_at')
                ->limit(6 - $both->count())
                ->get();
            $merged = $both->merge($either);
            return $merged->isNotEmpty() ? $merged : $fallbackLatest();
        }

        if ($hasArea) {
            $result = (clone $jobsBase)
                ->whereHas('jobAreas', fn($q) => $q->where('area_id', $article->area_id))
                ->orderByRaw($priorityOrder)
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
            return $result->isNotEmpty() ? $result : $fallbackLatest();
        }

        if ($hasJobType) {
            $result = (clone $jobsBase)
                ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $article->job_type_id))
                ->orderByRaw($priorityOrder)
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
            return $result->isNotEmpty() ? $result : $fallbackLatest();
        }

        return $fallbackLatest();
    }
}
