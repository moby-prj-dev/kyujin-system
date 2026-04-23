<?php

namespace App\Services;

use App\Models\Job;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SeoGeneratorService
{
    public function generate(Job $job): void
    {
        $areas           = $job->jobAreas->map(fn($a) => $a->area->name)->toArray();
        $jobTypeItems    = $job->jobJobTypes->map(fn($j) => $j->jobType)->all();
        $employmentTypes = $job->jobEmploymentTypes->map(fn($e) => $e->employmentType->name)->all();
        $conditionItems  = $job->jobConditions->map(fn($c) => $c->condition)->all();
        $appealItems     = $job->jobAppeals->map(fn($a) => $a->appeal)->all();

        // 主訴求を決定
        $mainAppeal = $this->selectMainAppeal($jobTypeItems, $conditionItems, $appealItems);

        // 主職種（ベース）
        $mainJobType = count($jobTypeItems) > 0 ? $jobTypeItems[0]->name : '介護スタッフ';

        $mainArea = count($areas) > 0 ? $areas[0] : '';
        $areaStr  = implode('・', $areas);
        $empStr   = implode('・', $employmentTypes);

        // タイトル：手入力があればそのまま使用、なければ生成
        $userTitle = ($job->title && $job->title !== '（生成中）') ? $job->title : null;
        $mainTitle = $userTitle ?? $this->generateTitle($mainAppeal, $mainJobType, $conditionItems, $appealItems, $mainArea);

        // サブタイトル生成
        $subtitle = $this->generateSubtitle($areas, $employmentTypes, $conditionItems, $appealItems, $mainAppeal);

        // タグ生成（主訴求以外の全項目）
        $tags = $this->generateTags($jobTypeItems, $conditionItems, $appealItems, $mainAppeal);

        // メタディスクリプション
        $metaDesc = $this->generateMetaDesc($areaStr, $mainJobType, $empStr, $conditionItems, $appealItems, $jobTypeItems, $mainAppeal);

        // LP本文：free_textがあればそのまま使用、なければ生成
        $description = $job->free_text
            ?: $this->generateDescription($areaStr, $mainJobType, $empStr, $conditionItems, $appealItems, $jobTypeItems, $mainAppeal);

        $job->title                 = $mainTitle;
        $job->seo_title             = $mainTitle . " | {$areaStr}";
        $job->subtitle              = $subtitle;
        $job->lp_tags               = $tags;
        $job->meta_description      = $metaDesc;
        $job->description_generated = $description;
        $job->save();
    }

    // ----------------------------------------
    // 主訴求決定ロジック
    // ----------------------------------------

    private function selectMainAppeal(array $jobTypes, array $conditions, array $appeals): array
    {
        // STEP1: is_main_candidate=true の候補を収集
        $candidates = [];

        foreach ($appeals as $item) {
            if ($item->is_main_candidate) {
                $candidates[] = ['label' => $item->name, 'score' => $item->score, 'type' => 'appeal'];
            }
        }
        foreach ($conditions as $item) {
            if ($item->is_main_candidate) {
                $candidates[] = ['label' => $item->name, 'score' => $item->score, 'type' => 'condition'];
            }
        }
        foreach ($jobTypes as $item) {
            if ($item->is_main_candidate) {
                $candidates[] = ['label' => $item->name, 'score' => $item->score, 'type' => 'job'];
            }
        }

        // STEP4: 候補なし → 職種を主訴求に
        if (empty($candidates)) {
            $name = count($jobTypes) > 0 ? $jobTypes[0]->name : '介護スタッフ';
            return ['label' => $name, 'score' => 3, 'type' => 'job'];
        }

        // STEP2: score最大を選択
        $maxScore = max(array_column($candidates, 'score'));
        $topCandidates = array_filter($candidates, fn($c) => $c['score'] === $maxScore);

        // STEP3: 同点時 appeal > condition > job
        $priority = ['appeal' => 0, 'condition' => 1, 'job' => 2];
        usort($topCandidates, fn($a, $b) => $priority[$a['type']] <=> $priority[$b['type']]);

        return reset($topCandidates);
    }

    // ----------------------------------------
    // タイトル生成（Vertex AI Gemini or テンプレート）
    // ----------------------------------------

    private function generateTitle(array $mainAppeal, string $jobType, array $conditions, array $appeals, string $area = ''): string
    {
        $apiKey = config('services.gemini.api_key');

        if ($apiKey && $apiKey !== 'your_api_key_here') {
            return $this->generateTitleWithGemini($mainAppeal['label'], $jobType, $conditions, $appeals, $area);
        }

        return $this->generateTitleFromTemplate($mainAppeal['label'], $jobType, $area);
    }

    private function generateTitleWithGemini(string $mainAppeal, string $jobType, array $conditions, array $appeals, string $area = ''): string
    {
        $conditionStr = implode('、', array_map(fn($c) => $c->name, $conditions));
        $appealStr    = implode('、', array_map(fn($a) => $a->name, $appeals));

        $prompt = <<<PROMPT
あなたは求人広告のコピーライターです。以下の情報をもとに、求職者が思わずクリックしたくなる自然で魅力的な求人タイトルを1つだけ生成してください。

【出力ルール】
- タイトル文字列のみ出力（説明・番号・記号は不要）
- 30〜40文字程度
- 「介護」を必ず含める
- 「求人」を必ず含める
- 勤務地「{$area}」を必ず含める
- 記号（｜／【】）を使わず、自然な日本語の文章として読めるタイトルにする
- 主訴求を活かして求職者の心に刺さる表現にする

【求人情報】
主訴求：{$mainAppeal}
職種：{$jobType}
勤務地：{$area}
勤務条件：{$conditionStr}
アピールポイント：{$appealStr}
PROMPT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model  = config('services.gemini.model', 'gemini-2.5-flash');
            $url    = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

            $client   = new Client(['timeout' => 15]);
            $response = $client->post($url, [
                'headers' => ['content-type' => 'application/json'],
                'json'    => [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 100,
                        'temperature'     => 0.7,
                    ],
                ],
            ]);

            $body  = json_decode($response->getBody()->getContents(), true);
            $title = trim($body['candidates'][0]['content']['parts'][0]['text'] ?? '');

            if ($title) {
                return $title;
            }
        } catch (\Exception $e) {
            Log::warning('Gemini APIタイトル生成失敗: ' . $e->getMessage());
        }

        return $this->generateTitleFromTemplate($mainAppeal, $jobType, $area);
    }

    private function generateTitleFromTemplate(string $mainAppeal, string $jobType, string $area = ''): string
    {
        $loc = $area ? "【{$area}】" : '';

        $templates = [
            '未経験OK'        => "{$loc}未経験OK！介護求人（{$jobType}）",
            '無資格OK'        => "{$loc}無資格OKの介護求人（{$jobType}）",
            '高時給'          => "{$loc}高時給の介護求人｜{$jobType}募集",
            '日払いOK'        => "{$loc}日払いOK！介護求人（{$jobType}）",
            '日勤のみ'        => "{$loc}日勤のみの介護求人｜{$jobType}",
            '夜勤専従'        => "{$loc}夜勤専従の介護求人｜{$jobType}",
            '週1日〜OK'       => "{$loc}週1日〜OKの介護求人（{$jobType}）",
            '週2日〜OK'       => "{$loc}週2日〜OKの介護求人（{$jobType}）",
            '短時間勤務OK'    => "{$loc}短時間OKの介護求人｜{$jobType}",
            '扶養内勤務OK'    => "{$loc}扶養内OKの介護求人（{$jobType}）",
            'ブランクOK'      => "{$loc}ブランクOKの介護求人｜{$jobType}",
            'Wワーク歓迎'     => "{$loc}WワークOKの介護求人（{$jobType}）",
            '新規オープン施設' => "{$loc}新規オープン施設の介護求人｜{$jobType}",
            '土日休み'        => "{$loc}土日休みの介護求人｜{$jobType}",
            '寮・社宅あり'    => "{$loc}寮あり介護求人（{$jobType}）",
        ];

        return $templates[$mainAppeal] ?? "{$loc}{$mainAppeal}の介護求人｜{$jobType}";
    }

    // ----------------------------------------
    // サブタイトル生成
    // ----------------------------------------

    private function generateSubtitle(array $areas, array $employmentTypes, array $conditions, array $appeals, array $mainAppeal): string
    {
        $parts = [];

        if ($areas) {
            $parts[] = $areas[0];
        }
        if ($employmentTypes) {
            $parts[] = implode('・', $employmentTypes);
        }

        // score4以上の条件（主訴求以外）
        foreach (array_merge($conditions, $appeals) as $item) {
            if ($item->score >= 4 && $item->name !== $mainAppeal['label']) {
                $parts[] = $item->name;
                if (count($parts) >= 4) break;
            }
        }

        return implode(' / ', $parts);
    }

    // ----------------------------------------
    // タグ生成
    // ----------------------------------------

    private function generateTags(array $jobTypes, array $conditions, array $appeals, array $mainAppeal): array
    {
        $tags = [];

        foreach (array_merge($jobTypes, $conditions, $appeals) as $item) {
            if ($item->name !== $mainAppeal['label']) {
                $tags[] = $item->name;
            }
        }

        return $tags;
    }

    // ----------------------------------------
    // メタディスクリプション生成
    // ----------------------------------------

    private function generateMetaDesc(string $area, string $jobType, string $employment, array $conditions, array $appeals, array $jobTypes, array $mainAppeal): string
    {
        $apiKey = config('services.gemini.api_key');

        if ($apiKey && $apiKey !== 'your_api_key_here') {
            $generated = $this->generateMetaDescWithGemini($area, $jobType, $employment, $conditions, $appeals, $jobTypes, $mainAppeal);
            if ($generated) {
                return $generated;
            }
        }

        return $this->generateMetaDescFromTemplate($area, $jobType, $employment, $mainAppeal);
    }

    private function generateMetaDescWithGemini(string $area, string $jobType, string $employment, array $conditions, array $appeals, array $jobTypes, array $mainAppeal): string
    {
        $highItems = array_filter(
            array_merge(
                array_map(fn($c) => ['name' => $c->name, 'score' => $c->score ?? 0], $conditions),
                array_map(fn($a) => ['name' => $a->name, 'score' => $a->score ?? 0], $appeals),
            ),
            fn($i) => $i['score'] >= 4 && $i['name'] !== $mainAppeal['label']
        );
        $highStr = implode('、', array_column(array_values($highItems), 'name'));
        $mainLabel = $mainAppeal['label'];

        $prompt = <<<PROMPT
あなたは求人広告のSEOライターです。以下の情報をもとに、検索結果に表示されるメタディスクリプションを1つ生成してください。

【出力ルール】
- 120〜150文字程度
- エリア・職種・主訴求を自然に盛り込む
- 高スコア項目があれば1〜2個追加する
- 「LINE応募OK」または「LINEで応募できます」を含める
- テキストのみ出力（説明・番号不要）

【求人情報】
エリア：{$area}
職種：{$jobType}
雇用形態：{$employment}
主訴求：{$mainLabel}
強調したい項目：{$highStr}
PROMPT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model  = config('services.gemini.model', 'gemini-2.5-flash');
            $url    = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

            $client   = new Client(['timeout' => 15]);
            $response = $client->post($url, [
                'headers' => ['content-type' => 'application/json'],
                'json'    => [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'maxOutputTokens' => 200,
                        'temperature'     => 0.7,
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return trim($body['candidates'][0]['content']['parts'][0]['text'] ?? '');
        } catch (\Exception $e) {
            Log::warning('Gemini APIメタディスク生成失敗: ' . $e->getMessage());
            return '';
        }
    }

    private function generateMetaDescFromTemplate(string $area, string $jobType, string $employment, array $mainAppeal): string
    {
        return "{$area}エリアで{$jobType}（{$employment}）を募集中。{$mainAppeal['label']}。LINE応募OK。";
    }

    // ----------------------------------------
    // LP本文生成
    // ----------------------------------------

    private function generateDescription(string $area, string $jobType, string $employment, array $conditions, array $appeals, array $jobTypes = [], array $mainAppeal = []): string
    {
        $apiKey = config('services.gemini.api_key');

        if ($apiKey && $apiKey !== 'your_api_key_here') {
            $generated = $this->generateDescriptionWithGemini($area, $jobType, $employment, $conditions, $appeals, $jobTypes, $mainAppeal);
            if ($generated) {
                return $generated;
            }
        }

        return $this->generateDescriptionFromTemplate($area, $jobType, $employment, $conditions, $appeals);
    }

    private function generateDescriptionWithGemini(string $area, string $jobType, string $employment, array $conditions, array $appeals, array $jobTypes = [], array $mainAppeal = []): string
    {
        // スコア順に並べて高スコア項目を強調
        $allItems = array_merge(
            array_map(fn($c) => ['name' => $c->name, 'score' => $c->score ?? 0, 'type' => '勤務条件'], $conditions),
            array_map(fn($a) => ['name' => $a->name, 'score' => $a->score ?? 0, 'type' => 'アピール'], $appeals),
            array_map(fn($j) => ['name' => $j->name, 'score' => $j->score ?? 0, 'type' => '職種'], $jobTypes),
        );
        usort($allItems, fn($a, $b) => $b['score'] <=> $a['score']);

        $highItems  = array_filter($allItems, fn($i) => $i['score'] >= 4);
        $otherItems = array_filter($allItems, fn($i) => $i['score'] < 4);

        $highStr  = implode('、', array_column(array_values($highItems), 'name'));
        $otherStr = implode('、', array_column(array_values($otherItems), 'name'));
        $mainLabel = $mainAppeal['label'] ?? '';

        $prompt = <<<PROMPT
あなたは求人広告のライターです。以下の情報をもとに、求職者に響く求人LP本文を生成してください。

【出力ルール】
- 2段落・合計150〜200文字程度
- 1段落目：最重要アピール（主訴求・高スコア項目）を中心に職場の魅力を伝える
- 2段落目：求める人物像と応募の呼びかけ
- 絵文字・箇条書きは使わない
- 「LINE応募またはフォームからお気軽にご応募ください。」で締めくくる
- 本文テキストのみ出力（タイトル・見出し・余分な説明は不要）

【求人情報】
勤務地：{$area}
職種：{$jobType}
雇用形態：{$employment}
主訴求（最も強調すべき点）：{$mainLabel}
特に強調したい項目（高スコア）：{$highStr}
その他の項目：{$otherStr}
PROMPT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model  = config('services.gemini.model', 'gemini-2.5-flash');
            $url    = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

            $client   = new Client(['timeout' => 20]);
            $response = $client->post($url, [
                'headers' => ['content-type' => 'application/json'],
                'json'    => [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 800,
                        'temperature'     => 0.8,
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return trim($body['candidates'][0]['content']['parts'][0]['text'] ?? '');
        } catch (\Exception $e) {
            Log::warning('Gemini API本文生成失敗: ' . $e->getMessage());
            return '';
        }
    }

    private function generateDescriptionFromTemplate(string $area, string $jobType, string $employment, array $conditions, array $appeals): string
    {
        $conditionList = implode("\n", array_map(fn($c) => "・{$c->name}", $conditions));
        $appealList    = implode("\n", array_map(fn($a) => "・{$a->name}", $appeals));

        return <<<TEXT
{$area}で{$jobType}のお仕事をお探しの方へ

現在、{$area}エリアで{$jobType}（{$employment}）のスタッフを募集しています。

【勤務条件】
{$conditionList}

【この求人のポイント】
{$appealList}

LINE応募またはフォームからお気軽にご応募ください。
応募後、担当者より折り返しご連絡いたします。
TEXT;
    }
}
