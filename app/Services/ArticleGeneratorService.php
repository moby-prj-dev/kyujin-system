<?php

namespace App\Services;

use App\Models\ContentArticle;
use App\Models\MasterArea;
use App\Models\MasterJobType;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ArticleGeneratorService
{
    // 生成する記事の定義
    public static function articleDefinitions(): array
    {
        return [
            [
                'slug'     => 'okinawa-kaigo-industry',
                'category' => 'industry',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['沖縄', '介護', '福祉', '求人', '業界', '高齢化', '人材不足'],
            ],
            [
                'slug'     => 'okinawa-kaigo-beginner',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['未経験', '介護', '沖縄', '転職', '資格なし', '採用'],
            ],
            [
                'slug'     => 'okinawa-kaigo-qualification',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['介護資格', '初任者研修', '実務者研修', '介護福祉士', '沖縄', '取得方法'],
            ],
            [
                'slug'     => 'naha-kaigo-jobs',
                'category' => 'area',
                'area'     => 'naha',
                'job_type' => null,
                'keywords' => ['那覇市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'okinawa-city-kaigo-jobs',
                'category' => 'area',
                'area'     => 'okinawa_city',
                'job_type' => null,
                'keywords' => ['沖縄市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'uruma-kaigo-jobs',
                'category' => 'area',
                'area'     => 'uruma',
                'job_type' => null,
                'keywords' => ['うるま市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'urasoe-kaigo-jobs',
                'category' => 'area',
                'area'     => 'urasoe',
                'job_type' => null,
                'keywords' => ['浦添市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'okinawa-care-staff-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'care_staff_facility',
                'keywords' => ['介護職員', '施設', '仕事内容', '給与', '1日の流れ', '夜勤'],
            ],
            [
                'slug'     => 'okinawa-care-manager-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'care_manager',
                'keywords' => ['ケアマネジャー', '介護支援専門員', '仕事内容', '給与', '沖縄', 'ケアプラン'],
            ],
            [
                'slug'     => 'okinawa-home-helper-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'home_helper',
                'keywords' => ['ホームヘルパー', '訪問介護', '仕事内容', '給与', '沖縄', '訪問先'],
            ],
            // 福祉系
            [
                'slug'     => 'okinawa-fukushi-beginner',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['未経験', '福祉', '障害福祉', '児童福祉', '沖縄', '転職', '資格なし'],
            ],
            [
                'slug'     => 'okinawa-fukushi-qualification',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['社会福祉士', '精神保健福祉士', 'サービス管理責任者', '資格', '沖縄', '取得方法'],
            ],
            [
                'slug'     => 'okinawa-shougai-fukushi-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'life_support_worker',
                'keywords' => ['生活支援員', '障害福祉', '仕事内容', '給与', '沖縄', 'グループホーム'],
            ],
            [
                'slug'     => 'okinawa-jidou-fukushi-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'childcare_worker',
                'keywords' => ['保育士', '児童指導員', '児童福祉', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-soudan-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'social_welfare_worker',
                'keywords' => ['社会福祉士', '相談支援専門員', '相談支援', '仕事内容', '給与', '沖縄'],
            ],
        ];
    }

    // 管理画面からの手動生成
    public function generateFromInput(string $slug, array $keywords, string $category, ?int $areaId = null, ?int $jobTypeId = null): ContentArticle
    {
        $def = [
            'slug'     => $slug,
            'category' => $category,
            'area'     => null,
            'job_type' => null,
            'keywords' => $keywords,
        ];

        $area    = $areaId    ? MasterArea::find($areaId)    : null;
        $jobType = $jobTypeId ? MasterJobType::find($jobTypeId) : null;

        ['title' => $title, 'h1' => $h1, 'meta' => $meta, 'body' => $body, 'image_url' => $imageUrl] = $this->callGemini($def, $area, $jobType);

        return ContentArticle::updateOrCreate(
            ['slug' => $slug],
            [
                'category'         => $category,
                'title'            => $title,
                'h1'               => $h1,
                'meta_description' => $meta,
                'body'             => $body,
                'image_url'        => $imageUrl,
                'area_id'          => $areaId,
                'job_type_id'      => $jobTypeId,
                'published_at'     => now(),
            ]
        );
    }

    public function generate(array $def): ContentArticle
    {
        $area    = $def['area']    ? MasterArea::where('slug', $def['area'])->first() : null;
        $jobType = $def['job_type'] ? MasterJobType::where('slug', $def['job_type'])->first() : null;

        ['title' => $title, 'h1' => $h1, 'meta' => $meta, 'body' => $body, 'image_url' => $imageUrl] = $this->callGemini($def, $area, $jobType);

        return ContentArticle::updateOrCreate(
            ['slug' => $def['slug']],
            [
                'category'         => $def['category'],
                'title'            => $title,
                'h1'               => $h1,
                'meta_description' => $meta,
                'body'             => $body,
                'image_url'        => $imageUrl,
                'area_id'          => $area?->id,
                'job_type_id'      => $jobType?->id,
                'published_at'     => now(),
            ]
        );
    }

    private function callGemini(array $def, $area, $jobType): array
    {
        $areaName    = $area    ? $area->name    : '沖縄県';
        $jobTypeName = $jobType ? $jobType->name : 'なし';
        $keywords    = implode('、', $def['keywords']);

        $prompt = <<<PROMPT
あなたは介護・福祉業界に詳しいSEOライターです。以下の情報をもとに、求職者向けの情報記事を生成してください。

【出力形式】JSON形式で以下のキーを返してください。
{
  "title": "ページタイトル（40文字以内、キーワードを含む）",
  "h1": "H1見出し（タイトルと意味は同じだが文章が違う、読者に向けた自然な日本語）",
  "meta": "メタディスクリプション（80〜120文字、ページ内容の説明）",
  "body": "記事本文（1000〜1500文字、HTMLタグなし、段落は空行で区切る）",
  "image_query": "記事に合う写真を検索する英語キーワード（2〜4単語、例: elderly care nursing home）"
}

【対象エリア】{$areaName}
【対象職種】{$jobTypeName}
【含めるキーワード】{$keywords}

【記事ルール】
- 誇張表現・ランキング・No.1などは禁止
- 事実ベースで書く（具体的な数字は「〜程度」「〜が多い」など推定表現を使う）
- 求職者が疑問に思うことを丁寧に解説する
- 最後に「Care Entry（ケアエントリー）」で求人を探せることを自然に案内する
- 本文のみ出力（タイトルや説明文は不要）
PROMPT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model  = config('services.gemini.model', 'gemini-2.5-flash');
            $url    = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $client   = new Client(['timeout' => 60]);
            $response = $client->post($url, [
                'headers' => ['content-type' => 'application/json'],
                'json'    => [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'maxOutputTokens'  => 2000,
                        'temperature'      => 0.6,
                        'responseMimeType' => 'application/json',
                        'thinkingConfig'   => ['thinkingBudget' => 0],
                    ],
                ],
            ]);

            $raw  = json_decode($response->getBody()->getContents(), true);
            $text = trim($raw['candidates'][0]['content']['parts'][0]['text'] ?? '');
            $data = json_decode($text, true);

            if ($data && isset($data['title'], $data['h1'], $data['meta'], $data['body'])) {
                $data['image_url'] = isset($data['image_query'])
                    ? $this->fetchUnsplashImage($data['image_query'])
                    : null;
                return $data;
            }
        } catch (\Exception $e) {
            Log::warning('Gemini記事生成失敗: ' . $e->getMessage());
        }

        return $this->fallback($def['keywords'], $areaName);
    }

    private function fetchUnsplashImage(string $query): ?string
    {
        try {
            $apiKey   = env('PEXELS_API_KEY');
            $encoded  = urlencode($query . ' Japan');
            $client   = new Client(['timeout' => 10]);
            $response = $client->get("https://api.pexels.com/v1/search?query={$encoded}&per_page=1", [
                'headers' => ['Authorization' => $apiKey],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['photos'][0]['src']['large'] ?? null;
        } catch (\Exception $e) {
            Log::warning('Pexels画像取得失敗: ' . $e->getMessage());
            return null;
        }
    }

    private function fallback(array $keywords, string $area): array
    {
        $topic = $keywords[0] ?? '介護・福祉の仕事';
        return [
            'title'     => "{$area}の{$topic}について",
            'h1'        => "{$area}で{$topic}に関する情報をお探しの方へ",
            'meta'      => "{$area}の介護・福祉に関する情報をまとめています。求職者向けに仕事内容や給与相場などを解説します。",
            'body'      => "{$area}の介護・福祉業界についての情報を掲載しています。\n\nCare Entry（ケアエントリー）では沖縄の介護・福祉求人を掲載しています。エリアや職種から自分に合う求人をお探しください。",
            'image_url' => null,
        ];
    }
}
