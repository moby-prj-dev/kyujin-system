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

        // 掲載元: Care Entry(自社=ハローワーク以外。古い求人は source が空のことがある) / ハローワーク
        if ($request->filled('source')) {
            match ($request->source) {
                'hellowork'  => $jobsQuery->where('source', 'hellowork'),
                'care_entry' => $jobsQuery->where(fn($q) => $q->whereNull('source')->orWhere('source', '<>', 'hellowork')),
                default      => null,
            };
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

        // 企業一覧で展開表示する各企業の掲載求人(自社求人のみ・メールアドレス単位)
        $companyJobs = Job::query()
            ->whereIn('contact_email', $companies->pluck('contact_email'))
            ->where(fn($q) => $q->whereNull('source')->orWhere('source', '<>', 'hellowork'))
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->get(['id', 'contact_email', 'title', 'seo_title', 'status', 'plan', 'token', 'email_verified_at', 'expires_at', 'created_at'])
            ->groupBy('contact_email');

        $jobMetrics = $this->jobMetrics($companyJobs->flatten()->pluck('id')->all());

        return view('admin.jobs.index', compact('jobs', 'companies', 'companyJobs', 'jobMetrics'));
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
            // モニター解除：無料期間も即終了 (monitor_ends_at をクリア = 通常課金開始)
            $job->update([
                'is_monitor'      => false,
                'monitor_ends_at' => null,
            ]);
            $msg = 'モニターを解除しました。（無料期間終了・通常課金に切替)';
        }

        return back()->with('success', $msg);
    }

    public function toggleFeatured(Job $job)
    {
        // 旧・注目求人オプションは販売終了(プランBでスタンダードの「露出拡大」に統合)
        // 残っている旧フラグの解除専用。新規設定は受け付けない
        if (!$job->is_featured) {
            return back()->with('error', '注目求人オプションは販売を終了しました。スタンダードプランの露出拡大をご利用ください。');
        }

        $job->update(['is_featured' => false, 'featured_started_at' => null]);
        return back()->with('success', '旧・注目求人フラグを解除しました。(検索結果では他のスタンダード求人と同列の表示になります)');
    }

    public function togglePermanentlyFree(Job $job)
    {
        $newVal = !$job->is_permanently_free;
        $job->update(['is_permanently_free' => $newVal]);
        $msg = $newVal ? '永久無料に設定しました。' : '永久無料を解除しました。';
        return back()->with('success', $msg);
    }

    public function togglePlan(Job $job)
    {
        $newPlan = $job->isStandard() ? Job::PLAN_BASIC : Job::PLAN_STANDARD;

        // スタンダードに切替時、同じ掲載主の他求人も同期
        // (プランは契約単位=事業所単位のため)
        $companyJobs = Job::where('contact_email', $job->contact_email)->orWhere('contact_phone', $job->contact_phone);
        $companyJobs->update([
            'plan' => $newPlan,
            // plan_started_at はスタンダード時は現在時刻、ベーシック戻し時は null
            'plan_started_at' => $newPlan === Job::PLAN_STANDARD ? now() : null,
            // 旧・注目求人オプション(販売終了)のフラグ掃除: ベーシック戻し時に解除
            ...($newPlan === Job::PLAN_BASIC ? ['is_featured' => false, 'featured_started_at' => null] : []),
        ]);

        $msg = $newPlan === Job::PLAN_STANDARD
            ? 'スタンダードプランに切替しました。(同一連絡先 ' . $companyJobs->count() . '件を同期・初回月額請求日:' . now()->copy()->addMonthNoOverflow()->startOfMonth()->format('Y/n/j') . ')'
            : 'ベーシックプランに戻しました。(同一連絡先 ' . $companyJobs->count() . '件を同期・翌月分より月額料金なし)';

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
        // ハローワーク等の外部スクレイピング求人は企業一覧から除外
        $rows = DB::select("
            SELECT
                j.contact_email,
                MAX(j.company_name)                                                AS company_name,
                MIN(j.email_verified_at)                                           AS first_activated_at,
                MIN(j.monitor_ends_at)                                             AS trial_ends_at,
                MAX(j.is_permanently_free)                                         AS is_permanently_free,
                COUNT(DISTINCT CASE WHEN j.deleted_at IS NULL THEN j.id END)       AS listing_count,
                COALESCE((
                    SELECT SUM(CASE WHEN a.is_valid = 1 THEN 1 ELSE 0 END)
                    FROM applications a
                    INNER JOIN job_listings j2 ON j2.id = a.job_id
                    WHERE j2.contact_email = j.contact_email
                      AND a.deleted_at IS NULL
                      AND (j2.source IS NULL OR j2.source <> 'hellowork')
                ), 0) AS valid_count,
                COALESCE((
                    SELECT SUM(CASE WHEN a.is_valid = 0 THEN 1 ELSE 0 END)
                    FROM applications a
                    INNER JOIN job_listings j2 ON j2.id = a.job_id
                    WHERE j2.contact_email = j.contact_email
                      AND a.deleted_at IS NULL
                      AND (j2.source IS NULL OR j2.source <> 'hellowork')
                ), 0) AS invalid_count,
                COALESCE((
                    SELECT SUM(CASE WHEN a.is_billable = 1 THEN 1 ELSE 0 END)
                    FROM applications a
                    INNER JOIN job_listings j2 ON j2.id = a.job_id
                    WHERE j2.contact_email = j.contact_email
                      AND a.deleted_at IS NULL
                      AND (j2.source IS NULL OR j2.source <> 'hellowork')
                ), 0) AS billable_count
            FROM job_listings j
            WHERE j.email_verified_at IS NOT NULL
              AND (j.source IS NULL OR j.source <> 'hellowork')
            GROUP BY j.contact_email
            ORDER BY first_activated_at DESC
        ");

        return collect($rows)->map(function ($c) {
            $c->pricing = $this->companyPricing($c);
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

    public function destroyCompany(Request $request): \Illuminate\Http\RedirectResponse
    {
        $email = $request->input('email');
        if (!is_string($email) || $email === '') {
            return back()->withErrors(['email' => '対象の企業メールが不正です。']);
        }

        // アクティブ(未削除)な求人がある場合は削除を拒否
        $activeCount = DB::table('job_listings')
            ->where('contact_email', $email)
            ->whereNull('deleted_at')
            ->count();

        if ($activeCount > 0) {
            return back()->withErrors([
                'company' => "この企業にはまだアクティブな求人が {$activeCount} 件あります。先に求人を全て削除してください。",
            ]);
        }

        DB::transaction(function () use ($email) {
            // 当該企業のすべての job_listings (soft-deleted も含む)
            $jobIds = DB::table('job_listings')
                ->where('contact_email', $email)
                ->pluck('id');

            if ($jobIds->isEmpty()) {
                return;
            }

            $appIds = DB::table('applications')->whereIn('job_id', $jobIds)->pluck('id');
            if ($appIds->isNotEmpty()) {
                DB::table('billings')->whereIn('application_id', $appIds)->delete();
                DB::table('application_notifications')->whereIn('application_id', $appIds)->delete();
            }
            DB::table('billings')->whereIn('job_id', $jobIds)->delete();
            DB::table('applications')->whereIn('job_id', $jobIds)->delete();
            DB::table('job_listings')->whereIn('id', $jobIds)->delete();
        });

        return redirect()->route('admin.jobs.index')
            ->with('success', "「{$email}」の企業データを完全に削除しました。");
    }

    /**
     * 企業の料金区分(請求判定 ApplicationValidationService::isBillable と同じ考え方)
     * - permanently_free: 永久無料
     * - monitor: モニター期間中(終了日前 かつ 有効応募3件未満)= 応募は無料
     * - normal: 通常(応募があれば請求対象)
     */
    private function companyPricing(object $c): string
    {
        $monitorEnd = $c->trial_ends_at ? Carbon::parse($c->trial_ends_at) : null;

        if ((bool) $c->is_permanently_free) return 'permanently_free';
        if ($monitorEnd && now()->lessThanOrEqualTo($monitorEnd) && (int) $c->valid_count < 3) return 'monitor';

        return 'normal';
    }

    /**
     * 自社求人ごとの露出・反応の数字(ボット除外)。プリミティブ配列で返す
     * - views30 / viewsTotal: 求人ページの閲覧数
     * - impressions30: 送客枠(おすすめ求人PR/直接応募)に表示された回数
     * - clicks30: 送客枠からのクリック数
     *
     * @param int[] $ids
     * @return array<int, array{views30:int, viewsTotal:int, impressions30:int, clicks30:int}>
     */
    private function jobMetrics(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $bot   = 'bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless';
        $human = fn($q, $col) => $q->where(fn($w) => $w->whereNull($col)->orWhere($col, 'not regexp', $bot));
        $since = now()->subDays(30);

        $viewsTotal = $human(DB::table('lp_views')->whereIn('job_id', $ids), 'user_agent')
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $views30 = $human(DB::table('lp_views')->whereIn('job_id', $ids)->where('viewed_at', '>=', $since), 'user_agent')
            ->selectRaw('job_id, COUNT(*) c')->groupBy('job_id')->pluck('c', 'job_id');
        $impressions30 = DB::table('lp_impressions')->whereIn('target_job_id', $ids)->where('shown_at', '>=', $since)
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');
        $clicks30 = $human(DB::table('lp_clicks')->whereIn('target_job_id', $ids)->whereIn('type', ['pr_job', 'direct_job'])->where('clicked_at', '>=', $since), 'user_agent')
            ->selectRaw('target_job_id, COUNT(*) c')->groupBy('target_job_id')->pluck('c', 'target_job_id');

        $out = [];
        foreach ($ids as $id) {
            $out[$id] = [
                'views30'       => (int) ($views30[$id] ?? 0),
                'viewsTotal'    => (int) ($viewsTotal[$id] ?? 0),
                'impressions30' => (int) ($impressions30[$id] ?? 0),
                'clicks30'      => (int) ($clicks30[$id] ?? 0),
            ];
        }
        return $out;
    }
}
