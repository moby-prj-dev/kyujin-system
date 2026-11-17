<?php

namespace App\Http\Controllers;

use App\Models\ContentArticle;
use App\Models\Job;
use App\Models\LpView;
use App\Services\RecommendedJobFinder;
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

        // 運営者・掲載者自身の閲覧は数えない
        if (! \App\Support\InternalVisitor::check()) {
            try {
                LpView::create([
                    'job_id'     => $job->id,
                    'ip_address' => $request->ip(),
                    // lp_views.user_agent は255文字。長いUAで記録ごと失敗しないよう切り詰める
                    'user_agent' => mb_substr($request->userAgent() ?? '', 0, 255),
                ]);
            } catch (\Exception $e) {
                \Log::warning('lp_view記録失敗: ' . $e->getMessage());
            }
        }

        $relatedArticles = $this->findRelatedArticles($job);

        // おすすめ求人(PR): ハローワーク求人ページのみ・同エリアのスタンダード求人(他エリア補完なし)
        // ※自社求人(ベーシック/スタンダード)のページには他社広告を出さない
        $recommendedJobs = $job->source === 'hellowork'
            ? app(RecommendedJobFinder::class)->forJob($job, 3)
            : collect();

        // 直接応募できる求人(ベーシック・PRなし): HW求人ページの閲覧者を自社求人へ送客。PR枠より下に表示
        $directJobs = $job->source === 'hellowork'
            ? app(RecommendedJobFinder::class)->forJobDirect($job, 3)
            : collect();

        // 送客枠の表示回数は、カードが画面に映ったときにブラウザから記録する(partials/click_tracker)

        $browse = $this->browseLinks($job);

        return view('lp.show', compact('job', 'relatedArticles', 'recommendedJobs', 'directJobs', 'browse'));
    }

    /**
     * 「ほかの求人を探す」用: この求人の市町村・職種の一覧ページと件数。
     * Google検索から求人ページに直接来た人は「戻る」で検索結果に戻ってしまうため、サイト内の一覧への導線を置く。
     * 件数は1時間キャッシュ(プリミティブのみ)
     *
     * @return array{area: ?array{name:string, slug:string, count:int}, type: ?array{name:string, slug:string, count:int}, total: int}
     */
    private function browseLinks(Job $job): array
    {
        $area = $job->jobAreas->first()?->area;
        $type = $job->jobJobTypes->first()?->jobType;

        $visible = fn() => Job::active()
            ->whereNotNull('email_verified_at')
            ->where('is_admin_hidden', false)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));

        $key = 'lp_browse:v1:a' . ($area->id ?? 0) . ':t' . ($type->id ?? 0);
        $counts = \Illuminate\Support\Facades\Cache::remember($key, now()->addHour(), function () use ($visible, $area, $type) {
            return [
                'area'  => $area ? $visible()->whereHas('jobAreas', fn($q) => $q->where('area_id', $area->id))->count() : 0,
                'type'  => ($area && $type) ? $visible()
                    ->whereHas('jobAreas', fn($q) => $q->where('area_id', $area->id))
                    ->whereHas('jobJobTypes', fn($q) => $q->where('job_type_id', $type->id))->count() : 0,
                'total' => $visible()->whereHas('jobAreas.area', fn($q) => $q->where('prefecture', '沖縄県'))->count(),
            ];
        });

        return [
            'area'  => ($area && $area->prefecture === '沖縄県') ? ['name' => $area->name, 'slug' => $area->slug, 'count' => $counts['area']] : null,
            // 同じ市町村×職種の求人が他にもあるときだけ出す(この求人1件だけのページへの導線は意味がない)
            'type'  => ($area && $type && $area->prefecture === '沖縄県' && $counts['type'] > 1) ? ['name' => $type->name, 'slug' => $type->slug, 'count' => $counts['type']] : null,
            'total' => $counts['total'],
        ];
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
