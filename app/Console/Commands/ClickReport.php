<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * クリック計測の集計(施策の効果確認用)
 *   php artisan clicks:report            直近30日
 *   php artisan clicks:report --days=7
 */
class ClickReport extends Command
{
    protected $signature   = 'clicks:report {--days=30 : 集計する日数}';
    protected $description = '応募ボタン・送客枠のクリック数を集計する';

    private const BOT_UA = 'bot|crawl|spider|slurp|preview|facebookexternalhit|curl|python|headless';

    public function handle(): int
    {
        $days  = max(1, (int) $this->option('days'));
        $since = now()->subDays($days);
        $base  = fn() => DB::table('lp_clicks')
            ->where('clicked_at', '>=', $since)
            ->where(fn($q) => $q->whereNull('user_agent')->orWhere('user_agent', 'not regexp', self::BOT_UA));

        $labels = [
            'hw_apply'   => 'ハローワークで応募する',
            'line_apply' => 'LINEで応募する',
            'form_apply' => 'フォームで応募する',
            'pr_job'     => 'おすすめ求人(PR)枠',
            'direct_job' => '直接応募できる求人枠',
        ];
        $counts = $base()->selectRaw('type, page, COUNT(*) c')->groupBy('type', 'page')->get();
        $rows = [];
        foreach ($labels as $type => $label) {
            $byPage = $counts->where('type', $type)->mapWithKeys(fn($r) => [$r->page ?? '-' => (int) $r->c]);
            $rows[] = [$label, $byPage->sum(), $byPage->map(fn($c, $p) => "{$p}:{$c}")->implode(' ')];
        }
        $this->info("直近{$days}日のクリック数(bot除外)");
        $this->table(['項目', '合計', 'ページ別'], $rows);

        // 送客枠の表示回数とクリック率(表示はボットを記録時点で除外済み)
        $imp = DB::table('lp_impressions')->where('shown_at', '>=', $since)
            ->selectRaw('slot, COUNT(*) c')->groupBy('slot')->pluck('c', 'slot');
        $clk = $counts->whereIn('type', ['pr_job', 'direct_job'])->groupBy('type')->map(fn($g) => $g->sum('c'));
        $this->info('送客枠の表示とクリック率');
        $this->table(['枠', '表示', 'クリック', 'クリック率'], collect(['pr_job', 'direct_job'])->map(function ($slot) use ($imp, $clk, $labels) {
            $i = (int) ($imp[$slot] ?? 0);
            $c = (int) ($clk[$slot] ?? 0);
            return [$labels[$slot], $i, $c, $i > 0 ? round($c / $i * 100, 1) . '%' : '—'];
        })->all());

        $targets = $base()->whereIn('type', ['pr_job', 'direct_job'])->whereNotNull('target_job_id')
            ->join('job_listings as j', 'j.id', '=', 'lp_clicks.target_job_id')
            ->selectRaw('j.company_name, lp_clicks.type, COUNT(*) c')
            ->groupBy('j.company_name', 'lp_clicks.type')->orderByDesc('c')->limit(20)->get();
        if ($targets->isNotEmpty()) {
            $this->info('送客枠でクリックされた求人(上位20)');
            $this->table(['事業所', '枠', 'クリック'], $targets->map(fn($r) => [$r->company_name, $labels[$r->type] ?? $r->type, $r->c])->all());
        }

        return self::SUCCESS;
    }
}
