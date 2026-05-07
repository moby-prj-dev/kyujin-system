<?php

namespace App\Console\Commands;

use App\Models\ContentArticle;
use App\Models\IndexingSubmission;
use App\Models\Job;
use App\Services\IndexingApiService;
use Illuminate\Console\Command;
use Throwable;

class SubmitIndexingApi extends Command
{
    protected $signature = 'indexing:submit
        {--type=all : 対象 (jobs|articles|all)}
        {--limit= : 1回の最大送信数 (省略時は services.google_indexing.daily_limit)}
        {--include-hellowork : ハローワーク転載LPも送信対象に含める (重複コンテンツ判定リスクあり)}
        {--dry-run : 送信せずに対象URLだけ表示}';

    protected $description = 'Google Indexing API に URL_UPDATED を通知してクロールを促す';

    public function handle(IndexingApiService $api): int
    {
        $type = $this->option('type');
        if (!in_array($type, ['jobs', 'articles', 'all'], true)) {
            $this->error("--type は jobs|articles|all のいずれか");
            return self::FAILURE;
        }

        $limit = (int) ($this->option('limit') ?? config('services.google_indexing.daily_limit', 100));
        $resubmitDays = (int) config('services.google_indexing.resubmit_after_days', 30);
        $cutoff = now()->subDays($resubmitDays);

        $candidates = $this->collectCandidates($type, $this->option('include-hellowork'));

        $recentlySubmitted = IndexingSubmission::where('submitted_at', '>=', $cutoff)
            ->pluck('url')
            ->unique()
            ->flip();

        $targets = $candidates
            ->reject(fn($row) => $recentlySubmitted->has($row['url']))
            ->take($limit)
            ->values();

        if ($targets->isEmpty()) {
            $this->info("送信対象なし (候補 {$candidates->count()} 件、{$resubmitDays}日以内に送信済みを除外)");
            return self::SUCCESS;
        }

        $this->info("送信対象: {$targets->count()} 件 / 候補 {$candidates->count()} 件");

        if ($this->option('dry-run')) {
            foreach ($targets as $row) {
                $this->line("[dry-run] {$row['type']}: {$row['url']}");
            }
            return self::SUCCESS;
        }

        $ok = 0;
        $ng = 0;
        foreach ($targets as $row) {
            try {
                $result = $api->notifyUpdate($row['url']);
                IndexingSubmission::create([
                    'url'           => $row['url'],
                    'type'          => $row['type'],
                    'action'        => 'URL_UPDATED',
                    'http_status'   => $result['status'],
                    'response_body' => mb_substr($result['body'] ?? '', 0, 1000),
                    'submitted_at'  => now(),
                ]);

                if ($result['ok']) {
                    $ok++;
                    $this->line("OK  {$row['url']}");
                } else {
                    $ng++;
                    $this->warn("NG  ({$result['status']}) {$row['url']}");
                    if ($result['status'] === 429 || $result['status'] === 403) {
                        $this->error('Quota/permission エラー検知。残りの送信を中止します。');
                        break;
                    }
                }
            } catch (Throwable $e) {
                $ng++;
                $this->error("ERR {$row['url']}: {$e->getMessage()}");
                IndexingSubmission::create([
                    'url'           => $row['url'],
                    'type'          => $row['type'],
                    'action'        => 'URL_UPDATED',
                    'http_status'   => 0,
                    'response_body' => mb_substr($e->getMessage(), 0, 1000),
                    'submitted_at'  => now(),
                ]);
            }

            usleep(200_000);
        }

        $this->info("完了: OK={$ok}, NG={$ng}");
        return $ng > 0 && $ok === 0 ? self::FAILURE : self::SUCCESS;
    }

    private function collectCandidates(string $type, bool $includeHellowork)
    {
        $rows = collect();

        if ($type === 'articles' || $type === 'all') {
            ContentArticle::published()
                ->orderByDesc('updated_at')
                ->get(['slug', 'updated_at'])
                ->each(function ($a) use ($rows) {
                    $rows->push([
                        'url'  => route('articles.show', $a->slug),
                        'type' => 'article',
                    ]);
                });
        }

        if ($type === 'jobs' || $type === 'all') {
            $q = Job::active()
                ->whereNotNull('email_verified_at')
                ->where('is_admin_hidden', false);

            if (!$includeHellowork) {
                $q->where('source', '!=', 'hellowork');
            }

            $q->orderByDesc('updated_at')
              ->get(['token', 'updated_at', 'source'])
              ->each(function ($j) use ($rows) {
                  $rows->push([
                      'url'  => route('lp.show', $j->token),
                      'type' => 'job_' . $j->source,
                  ]);
              });
        }

        return $rows;
    }
}
