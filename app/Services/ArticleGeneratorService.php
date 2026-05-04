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

            // ── 業界情報（追加） ────────────────────────────────────
            [
                'slug'     => 'okinawa-kaigo-salary',
                'category' => 'industry',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['沖縄', '介護', '給与', '賃金', '月収', '手当', '相場'],
            ],
            [
                'slug'     => 'okinawa-kaigo-working-style',
                'category' => 'industry',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['沖縄', '介護', '働き方', '夜勤', 'シフト', '休日', 'ワークライフバランス'],
            ],
            [
                'slug'     => 'okinawa-kaigo-future',
                'category' => 'industry',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['沖縄', '介護', '将来性', 'キャリアパス', 'スキルアップ', '管理職', '独立'],
            ],
            [
                'slug'     => 'okinawa-facility-types',
                'category' => 'industry',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['特別養護老人ホーム', '老健', 'グループホーム', 'デイサービス', '施設の違い', '沖縄'],
            ],

            // ── 職種別（追加） ──────────────────────────────────────
            [
                'slug'     => 'okinawa-care-welfare-worker-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'care_welfare_worker',
                'keywords' => ['介護福祉士', '資格', '仕事内容', '給与', '沖縄', 'キャリアアップ'],
            ],
            [
                'slug'     => 'okinawa-service-provision-manager-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'service_provision_manager',
                'keywords' => ['サービス提供責任者', 'サ責', '訪問介護', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-life-consultant-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'life_consultant',
                'keywords' => ['生活相談員', '施設', '相談業務', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-employment-support-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'employment_support_worker',
                'keywords' => ['就労支援員', '障害者就労', '就労継続支援', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-service-manager-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'service_manager',
                'keywords' => ['サービス管理責任者', 'サビ管', '障害福祉', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-psychiatric-social-worker-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'psychiatric_social_worker',
                'keywords' => ['精神保健福祉士', 'PSW', '相談支援', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-nurse-welfare-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'nurse_welfare_facility',
                'keywords' => ['看護師', '福祉施設', '介護施設', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-pt-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'physical_therapist',
                'keywords' => ['理学療法士', 'PT', 'リハビリ', '仕事内容', '給与', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-ot-jobs',
                'category' => 'job_type',
                'area'     => null,
                'job_type' => 'occupational_therapist',
                'keywords' => ['作業療法士', 'OT', 'リハビリ', '仕事内容', '給与', '沖縄'],
            ],

            // ── エリア（追加・那覇南部） ────────────────────────────
            [
                'slug'     => 'tomigusuku-kaigo-jobs',
                'category' => 'area',
                'area'     => 'tomigusuku',
                'job_type' => null,
                'keywords' => ['豊見城市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'itoman-kaigo-jobs',
                'category' => 'area',
                'area'     => 'itoman',
                'job_type' => null,
                'keywords' => ['糸満市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'nanjo-kaigo-jobs',
                'category' => 'area',
                'area'     => 'nanjo',
                'job_type' => null,
                'keywords' => ['南城市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'haebaru-kaigo-jobs',
                'category' => 'area',
                'area'     => 'haebaru',
                'job_type' => null,
                'keywords' => ['南風原町', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'yonabaru-kaigo-jobs',
                'category' => 'area',
                'area'     => 'yonabaru',
                'job_type' => null,
                'keywords' => ['与那原町', '介護', '福祉', '求人', '給与', '職場'],
            ],

            // ── エリア（追加・中部） ────────────────────────────────
            [
                'slug'     => 'ginowan-kaigo-jobs',
                'category' => 'area',
                'area'     => 'ginowan',
                'job_type' => null,
                'keywords' => ['宜野湾市', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'chatan-kaigo-jobs',
                'category' => 'area',
                'area'     => 'chatan',
                'job_type' => null,
                'keywords' => ['北谷町', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'yomitan-kaigo-jobs',
                'category' => 'area',
                'area'     => 'yomitan',
                'job_type' => null,
                'keywords' => ['読谷村', '介護', '福祉', '求人', '給与', '職場'],
            ],
            [
                'slug'     => 'nishihara-kaigo-jobs',
                'category' => 'area',
                'area'     => 'nishihara',
                'job_type' => null,
                'keywords' => ['西原町', '介護', '福祉', '求人', '給与', '職場'],
            ],

            // ── エリア（追加・北部） ────────────────────────────────
            [
                'slug'     => 'nago-kaigo-jobs',
                'category' => 'area',
                'area'     => 'nago',
                'job_type' => null,
                'keywords' => ['名護市', '介護', '福祉', '求人', '給与', '北部'],
            ],
            [
                'slug'     => 'onna-kaigo-jobs',
                'category' => 'area',
                'area'     => 'onna',
                'job_type' => null,
                'keywords' => ['恩納村', '介護', '福祉', '求人', '給与', '北部'],
            ],
            [
                'slug'     => 'motobu-kaigo-jobs',
                'category' => 'area',
                'area'     => 'motobu',
                'job_type' => null,
                'keywords' => ['本部町', '介護', '福祉', '求人', '給与', '北部'],
            ],

            // ── エリア（追加・離島） ────────────────────────────────
            [
                'slug'     => 'ishigaki-kaigo-jobs',
                'category' => 'area',
                'area'     => 'ishigaki',
                'job_type' => null,
                'keywords' => ['石垣市', '介護', '福祉', '求人', '給与', '離島', '八重山'],
            ],
            [
                'slug'     => 'miyakojima-kaigo-jobs',
                'category' => 'area',
                'area'     => 'miyakojima',
                'job_type' => null,
                'keywords' => ['宮古島市', '介護', '福祉', '求人', '給与', '離島'],
            ],
            [
                'slug'     => 'kumejima-kaigo-jobs',
                'category' => 'area',
                'area'     => 'kumejima',
                'job_type' => null,
                'keywords' => ['久米島町', '介護', '福祉', '求人', '給与', '離島'],
            ],

            // ── 資格と研修（追加） ──────────────────────────────────
            [
                'slug'     => 'okinawa-shonin-kenshu',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['介護職員初任者研修', '取得方法', '費用', '期間', 'ヘルパー2級', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-jitsumu-kenshu',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['実務者研修', '取得方法', '費用', 'たんの吸引', '医療的ケア', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-kaigo-fukushi-exam',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['介護福祉士', '国家試験', '合格率', '勉強方法', '受験資格', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-care-manager-exam',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['ケアマネジャー', '試験', '受験資格', '合格率', '勉強法', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-service-manager-qualification',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['サービス管理責任者', 'サビ管', '要件', '研修', '実務経験', '沖縄'],
            ],
            [
                'slug'     => 'okinawa-childcare-qualification',
                'category' => 'qualification',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['保育士資格', '取得方法', '試験', '養成校', '沖縄', '児童福祉'],
            ],

            // ── 未経験と転職（追加） ────────────────────────────────
            [
                'slug'     => 'okinawa-career-change-kaigo',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['他業種', '転職', '介護', '沖縄', '未経験', '流れ', '志望動機'],
            ],
            [
                'slug'     => 'okinawa-kaigo-40s',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['40代', '転職', '介護', '沖縄', '未経験', '採用', 'キャリアチェンジ'],
            ],
            [
                'slug'     => 'okinawa-kaigo-50s',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['50代', '転職', '介護', '沖縄', '再就職', '体力', 'シニア'],
            ],
            [
                'slug'     => 'okinawa-mens-kaigo',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['男性', '介護士', '沖縄', '仕事', '給与', 'キャリア', '夜勤'],
            ],
            [
                'slug'     => 'okinawa-part-time-kaigo',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['パート', 'アルバイト', '介護', '沖縄', '時給', '主婦', '扶養'],
            ],
            [
                'slug'     => 'okinawa-kaigo-interview',
                'category' => 'beginner',
                'area'     => null,
                'job_type' => null,
                'keywords' => ['介護', '面接', '履歴書', '志望動機', '自己PR', '沖縄', '転職'],
            ],
        ];
    }

    // フェーズ2: 主要エリア×主要職種の組み合わせ記事
    public static function dynamicDefinitions(): array
    {
        $areas = [
            'naha'         => ['那覇市',   ['介護', '福祉', '求人', '那覇']],
            'urasoe'       => ['浦添市',   ['介護', '福祉', '求人', '浦添']],
            'ginowan'      => ['宜野湾市', ['介護', '福祉', '求人', '宜野湾']],
            'okinawa_city' => ['沖縄市',   ['介護', '福祉', '求人', '沖縄市']],
            'uruma'        => ['うるま市', ['介護', '福祉', '求人', 'うるま']],
            'tomigusuku'   => ['豊見城市', ['介護', '福祉', '求人', '豊見城']],
            'nago'         => ['名護市',   ['介護', '福祉', '求人', '名護']],
            'itoman'       => ['糸満市',   ['介護', '福祉', '求人', '糸満']],
            'ishigaki'     => ['石垣市',   ['介護', '福祉', '求人', '石垣']],
            'miyakojima'   => ['宮古島市', ['介護', '福祉', '求人', '宮古島']],
        ];

        $jobTypes = [
            'care_staff_facility'      => ['介護職員（施設）',     ['介護職員', '施設', '仕事内容', '給与', '夜勤']],
            'home_helper'              => ['ホームヘルパー',        ['訪問介護', 'ホームヘルパー', '仕事内容', '給与']],
            'care_manager'             => ['ケアマネジャー',        ['ケアマネ', '介護支援専門員', '仕事内容', '給与']],
            'care_welfare_worker'      => ['介護福祉士',            ['介護福祉士', '資格', '仕事内容', '給与']],
            'service_provision_manager'=> ['サービス提供責任者',    ['サ責', 'サービス提供責任者', '仕事内容', '給与']],
            'childcare_worker'         => ['保育士',                ['保育士', '児童福祉', '仕事内容', '給与']],
            'life_support_worker'      => ['生活支援員',            ['生活支援員', '障害福祉', '仕事内容', '給与']],
            'social_welfare_worker'    => ['社会福祉士',            ['社会福祉士', '相談支援', '仕事内容', '給与']],
        ];

        $definitions = [];
        foreach ($areas as $areaSlug => [$areaName, $areaKeywords]) {
            foreach ($jobTypes as $jobTypeSlug => [$jobTypeName, $jobKeywords]) {
                $definitions[] = [
                    'slug'     => "{$areaSlug}-{$jobTypeSlug}-jobs",
                    'category' => 'area',
                    'area'     => $areaSlug,
                    'job_type' => $jobTypeSlug,
                    'keywords' => array_merge([$areaName, $jobTypeName], $areaKeywords, $jobKeywords),
                ];
            }
        }

        return $definitions;
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
