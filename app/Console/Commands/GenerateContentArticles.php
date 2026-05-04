<?php

namespace App\Console\Commands;

use App\Services\ArticleGeneratorService;
use Illuminate\Console\Command;

class GenerateContentArticles extends Command
{
    protected $signature   = 'articles:generate {--slug= : 特定のslugのみ生成} {--limit= : 未生成のものをN件だけ生成}';
    protected $description = 'Geminiを使ってコンテンツSEO記事を生成・更新する';

    public function handle(ArticleGeneratorService $service): int
    {
        $definitions = ArticleGeneratorService::articleDefinitions();

        if ($slug = $this->option('slug')) {
            $definitions = array_filter($definitions, fn($d) => $d['slug'] === $slug);
            if (empty($definitions)) {
                $this->error("slug '{$slug}' が見つかりません。");
                return self::FAILURE;
            }
        } elseif ($limit = $this->option('limit')) {
            $existingSlugs = \App\Models\ContentArticle::pluck('slug')->all();

            // フェーズ1: 定義済み未生成
            $definitions = array_values(array_filter($definitions, fn($d) => !in_array($d['slug'], $existingSlugs)));

            // フェーズ1が尽きたらフェーズ2（エリア×職種）へ
            if (empty($definitions)) {
                $dynamic     = ArticleGeneratorService::dynamicDefinitions();
                $definitions = array_values(array_filter($dynamic, fn($d) => !in_array($d['slug'], $existingSlugs)));
            }

            if (empty($definitions)) {
                $this->info('新規生成する記事はありません（フェーズ1・2ともに完了）。');
                return self::SUCCESS;
            }

            $definitions = array_slice($definitions, 0, (int) $limit);
        }

        $this->info('記事生成を開始します（' . count($definitions) . '件）');
        $bar = $this->output->createProgressBar(count($definitions));
        $bar->start();

        $success = 0;
        $failed  = 0;

        foreach ($definitions as $def) {
            try {
                $article = $service->generate($def);
                $this->newLine();
                $this->line("  ✓ [{$article->slug}] {$article->title}");
                $success++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ✗ [{$def['slug']}] {$e->getMessage()}");
                $failed++;
            }
            $bar->advance();
            sleep(1); // Gemini APIレート制限対策
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("完了: 成功 {$success}件 / 失敗 {$failed}件");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
