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
            'naha'            => ['那覇市',    ['介護', '福祉', '求人', '那覇']],
            'urasoe'          => ['浦添市',    ['介護', '福祉', '求人', '浦添']],
            'ginowan'         => ['宜野湾市',  ['介護', '福祉', '求人', '宜野湾']],
            'okinawa_city'    => ['沖縄市',    ['介護', '福祉', '求人', '沖縄市']],
            'uruma'           => ['うるま市',  ['介護', '福祉', '求人', 'うるま']],
            'tomigusuku'      => ['豊見城市',  ['介護', '福祉', '求人', '豊見城']],
            'nago'            => ['名護市',    ['介護', '福祉', '求人', '名護']],
            'itoman'          => ['糸満市',    ['介護', '福祉', '求人', '糸満']],
            'ishigaki'        => ['石垣市',    ['介護', '福祉', '求人', '石垣']],
            'miyakojima'      => ['宮古島市',  ['介護', '福祉', '求人', '宮古島']],
            'nanjo'           => ['南城市',    ['介護', '福祉', '求人', '南城']],
            'yonabaru'        => ['与那原町',  ['介護', '福祉', '求人', '与那原']],
            'haebaru'         => ['南風原町',  ['介護', '福祉', '求人', '南風原']],
            'nishihara'       => ['西原町',    ['介護', '福祉', '求人', '西原']],
            'yaese'           => ['八重瀬町',  ['介護', '福祉', '求人', '八重瀬']],
            'yomitan'         => ['読谷村',    ['介護', '福祉', '求人', '読谷']],
            'chatan'          => ['北谷町',    ['介護', '福祉', '求人', '北谷']],
            'kadena'          => ['嘉手納町',  ['介護', '福祉', '求人', '嘉手納']],
            'kitanakagusuku'  => ['北中城村',  ['介護', '福祉', '求人', '北中城']],
            'nakagusuku'      => ['中城村',    ['介護', '福祉', '求人', '中城']],
        ];

        // 各職種に「カテゴリ視点」を付与: qualification=国家資格系 / job_type=その他
        $jobTypes = [
            'care_staff_facility'       => ['介護職員（施設）',      'job_type',      ['介護職員', '施設', '仕事内容', '給与', '夜勤']],
            'home_helper'               => ['ホームヘルパー',        'job_type',      ['訪問介護', 'ホームヘルパー', '仕事内容', '給与']],
            'care_manager'              => ['ケアマネジャー',        'qualification', ['ケアマネ', '介護支援専門員', '仕事内容', '給与']],
            'care_welfare_worker'       => ['介護福祉士',            'qualification', ['介護福祉士', '資格', '仕事内容', '給与']],
            'service_provision_manager' => ['サービス提供責任者',    'qualification', ['サ責', 'サービス提供責任者', '仕事内容', '給与']],
            'childcare_worker'          => ['保育士',                'qualification', ['保育士', '児童福祉', '仕事内容', '給与']],
            'life_support_worker'       => ['生活支援員',            'job_type',      ['生活支援員', '障害福祉', '仕事内容', '給与']],
            'social_welfare_worker'     => ['社会福祉士',            'qualification', ['社会福祉士', '相談支援', '仕事内容', '給与']],
            'nurse'                     => ['看護師',                'qualification', ['看護師', '施設看護', '仕事内容', '給与']],
            'associate_nurse'           => ['准看護師',              'qualification', ['准看護師', '施設看護', '仕事内容', '給与']],
            'nurse_assistant'           => ['看護助手',              'job_type',      ['看護助手', '医療事務補助', '未経験', '仕事内容']],
            'physical_therapist'        => ['理学療法士',            'qualification', ['理学療法士', 'PT', 'リハビリ', '給与']],
            'occupational_therapist'    => ['作業療法士',            'qualification', ['作業療法士', 'OT', 'リハビリ', '給与']],
            'child_counselor'           => ['児童指導員',            'job_type',      ['児童指導員', '児童福祉', '放課後デイ', '仕事内容']],
            'childcare_assistant'       => ['保育補助',              'job_type',      ['保育補助', '保育園', '未経験', '仕事内容']],
        ];

        $definitions = [];
        $idx = 0;
        $jobTypeCount = count($jobTypes);
        foreach ($areas as $areaSlug => [$areaName, $areaKeywords]) {
            foreach ($jobTypes as $jobTypeSlug => [$jobTypeName, $jobTypeCategory, $jobKeywords]) {
                // 各エリアの先頭1件は 'area' カテゴリにしてエリア記事も生成
                $category = ($idx % $jobTypeCount === 0) ? 'area' : $jobTypeCategory;
                $definitions[] = [
                    'slug'     => "{$areaSlug}-{$jobTypeSlug}-jobs",
                    'category' => $category,
                    'area'     => $areaSlug,
                    'job_type' => $jobTypeSlug,
                    'keywords' => array_merge([$areaName, $jobTypeName], $areaKeywords, $jobKeywords),
                ];
                $idx++;
            }
        }

        return $definitions;
    }

    /**
     * Phase 3: 「条件 × 職種」軸の動的記事定義
     * 夜勤あり/日勤のみ/扶養内/土日休み等の働き方条件と職種の組み合わせ。
     * Phase 1/2 が枯れた後に GenerateContentArticles から呼ばれる。
     */
    public static function conditionDefinitions(): array
    {
        $conditions = [
            'night-shift'         => ['夜勤あり',           ['夜勤', '夜間勤務', '夜勤手当']],
            'day-shift-only'      => ['日勤のみ',           ['日勤', '日中勤務', '主婦', '主夫']],
            'night-shift-only'    => ['夜勤専従',           ['夜勤専従', '高給', '夜間専従']],
            'no-overtime'         => ['残業ほぼなし',       ['ワークライフバランス', '定時上がり', '残業なし']],
            'weekend-off'         => ['土日休み',           ['土日祝休み', '家庭優先', '週末休み']],
            'within-dependent'    => ['扶養内勤務OK',       ['扶養内', 'パート', '103万円']],
            'short-hours'         => ['短時間勤務OK',       ['短時間', '時短', '4時間']],
            'license-support'     => ['資格取得支援あり',   ['資格取得', 'キャリアアップ', '研修支援']],
        ];

        // [URLフレンドリーなarticle slug] => [master_job_types.slug, 表示名, キーワード]
        $jobTypes = [
            'care-staff'      => ['care_staff_facility', '介護職員',         ['介護職員', '介護スタッフ']],
            'home-helper'     => ['home_helper',         'ホームヘルパー',   ['訪問介護', 'ヘルパー']],
            'care-manager'    => ['care_manager',        'ケアマネジャー',   ['ケアマネ', '介護支援専門員']],
            'care-fukushishi' => ['care_welfare_worker', '介護福祉士',       ['介護福祉士', '国家資格']],
            'life-support'    => ['life_support_worker', '生活支援員',       ['生活支援員', '障害福祉']],
            'childcare'       => ['childcare_worker',    '保育士',           ['保育士', '児童福祉']],
        ];

        // 資格取得支援系のみ 'qualification' カテゴリ・残りは 'job_type'
        $defs = [];
        foreach ($conditions as $condSlug => [$condName, $condKw]) {
            foreach ($jobTypes as $jtArticleSlug => [$jtMasterSlug, $jtName, $jtKw]) {
                $defs[] = [
                    'slug'     => "{$condSlug}-{$jtArticleSlug}-okinawa",  // 記事URL用
                    'category' => $condSlug === 'license-support' ? 'qualification' : 'job_type',
                    'area'     => null,           // 沖縄県全体(エリア限定無し)
                    'job_type' => $jtMasterSlug,  // master_job_types.slug にマッチさせる
                    'keywords' => array_merge(
                        ['沖縄', $jtName, $condName, '求人', '仕事'],
                        $jtKw,
                        $condKw
                    ),
                ];
            }
        }
        return $defs;
    }

    /**
     * Phase 4: 資格取得系記事(qualification カテゴリ)
     * 介護福祉士・ケアマネ・保育士などの資格取得に関する体系的解説。
     */
    public static function qualificationDefinitions(): array
    {
        return [
            ['slug' => 'kaigo-fukushishi-shikaku-okinawa',  'category' => 'qualification', 'area' => null, 'job_type' => 'care_welfare_worker',     'keywords' => ['介護福祉士', '資格', '取り方', '受験資格', '実務経験', '沖縄', '国家資格']],
            ['slug' => 'kaigo-fukushishi-shiken-okinawa',   'category' => 'qualification', 'area' => null, 'job_type' => 'care_welfare_worker',     'keywords' => ['介護福祉士', '試験', '対策', '合格率', '勉強法', '沖縄', '過去問']],
            ['slug' => 'care-manager-shikaku-okinawa',      'category' => 'qualification', 'area' => null, 'job_type' => 'care_manager',            'keywords' => ['ケアマネ', '介護支援専門員', '資格', '取り方', '受験資格', '沖縄']],
            ['slug' => 'care-manager-shiken-okinawa',       'category' => 'qualification', 'area' => null, 'job_type' => 'care_manager',            'keywords' => ['ケアマネ', '試験', '対策', '合格率', '沖縄', '勉強法']],
            ['slug' => 'shakaifukushishi-shikaku-okinawa',  'category' => 'qualification', 'area' => null, 'job_type' => 'social_welfare_worker',   'keywords' => ['社会福祉士', '資格', '取り方', '受験資格', '沖縄', '国家試験']],
            ['slug' => 'hoikushi-shikaku-okinawa',          'category' => 'qualification', 'area' => null, 'job_type' => 'childcare_worker',        'keywords' => ['保育士', '資格', '取り方', '国家試験', '保育士養成校', '沖縄']],
            ['slug' => 'shoninsha-kenshu-okinawa',          'category' => 'qualification', 'area' => null, 'job_type' => 'care_staff_facility',     'keywords' => ['介護職員初任者研修', 'ヘルパー2級', '取り方', '費用', '沖縄', '入門']],
            ['slug' => 'jitsumusha-kenshu-okinawa',         'category' => 'qualification', 'area' => null, 'job_type' => 'care_staff_facility',     'keywords' => ['実務者研修', '介護福祉士', '受験資格', 'ステップアップ', '沖縄']],
            ['slug' => 'shoninsha-vs-jitsumusha-okinawa',   'category' => 'qualification', 'area' => null, 'job_type' => 'care_staff_facility',     'keywords' => ['初任者研修', '実務者研修', '違い', '比較', '介護資格', '沖縄']],
            ['slug' => 'kakutan-kyuin-okinawa',             'category' => 'qualification', 'area' => null, 'job_type' => 'care_staff_facility',     'keywords' => ['喀痰吸引', '研修', '介護職員', '医療的ケア', '沖縄', '取り方']],
            ['slug' => 'nintei-kaigo-fukushishi-okinawa',   'category' => 'qualification', 'area' => null, 'job_type' => 'care_welfare_worker',     'keywords' => ['認定介護福祉士', '上位資格', 'キャリアアップ', '介護福祉士', '沖縄']],
            ['slug' => 'service-teikyo-sekinin-okinawa',    'category' => 'qualification', 'area' => null, 'job_type' => 'service_provision_manager','keywords' => ['サービス提供責任者', 'サ責', '資格', '要件', '訪問介護', '沖縄']],
            ['slug' => 'jido-shidoin-shikaku-okinawa',      'category' => 'qualification', 'area' => null, 'job_type' => 'child_guidance_worker',   'keywords' => ['児童指導員', '任用資格', '取り方', '児童福祉', '放課後等デイ', '沖縄']],
            ['slug' => 'psw-shikaku-okinawa',               'category' => 'qualification', 'area' => null, 'job_type' => 'psychiatric_social_worker','keywords' => ['精神保健福祉士', 'PSW', '資格', '取り方', '受験資格', '沖縄']],
            ['slug' => 'sodanshien-senmonin-okinawa',       'category' => 'qualification', 'area' => null, 'job_type' => 'consultation_support_specialist', 'keywords' => ['相談支援専門員', '資格', '取り方', '障害福祉', '研修', '沖縄']],
            ['slug' => 'kaigo-shikaku-roadmap-okinawa',     'category' => 'qualification', 'area' => null, 'job_type' => null,                       'keywords' => ['介護資格', 'ロードマップ', 'キャリアパス', '初任者研修', '介護福祉士', 'ケアマネ', '沖縄']],
            ['slug' => 'shikaku-shienseido-okinawa',        'category' => 'qualification', 'area' => null, 'job_type' => null,                       'keywords' => ['資格取得支援', '介護職', '会社負担', '受講料', '沖縄', '研修制度']],
        ];
    }

    /**
     * Phase 5: 業界動向・コラム系記事(industry カテゴリ)
     * 介護報酬・トレンド・市場動向など時事性のある記事。
     */
    public static function columnDefinitions(): array
    {
        return [
            ['slug' => 'kaigo-hosyu-kaitei-2024-okinawa',   'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護報酬改定', '2024年', '介護事業所', '影響', '加算', '沖縄']],
            ['slug' => 'kaigo-jinzai-fusoku-okinawa',       'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護人材不足', '現状', '原因', '沖縄', '対策', '採用']],
            ['slug' => 'kaigo-trend-2026-okinawa',          'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護業界', '2026年', 'トレンド', '最新動向', '沖縄', '未来']],
            ['slug' => 'kaigo-shisetsu-suii-okinawa',       'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護施設数', '推移', '沖縄県', '事業所統計', '増加', '高齢化']],
            ['slug' => 'kaigo-dx-ict-okinawa',              'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護DX', 'ICT活用', '見守りセンサー', '介護記録', '沖縄', '効率化']],
            ['slug' => 'gaikokujin-kaigo-jinzai-okinawa',   'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['外国人介護人材', 'EPA', '技能実習', '特定技能', '沖縄', '受け入れ']],
            ['slug' => 'ninchisho-care-okinawa',            'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['認知症ケア', '最前線', 'ユマニチュード', '研修', '沖縄', '事例']],
            ['slug' => 'mitori-care-okinawa',               'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['看取りケア', 'ターミナルケア', '介護施設', '沖縄', '現状', '加算']],
            ['slug' => 'okinawa-koreika-koreisya',          'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['沖縄県', '高齢化率', '統計', '65歳以上人口', '介護需要', '未来予測']],
            ['slug' => 'kaigo-yarigai-okinawa',             'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', 'やりがい', '魅力', 'リアル', '本音', '沖縄']],
            ['slug' => 'kaigo-rishoku-boshi-okinawa',       'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '離職', '原因', '対策', '定着', '沖縄', '職場環境']],
            ['slug' => 'kaigo-kyuyo-suii-okinawa',          'category' => 'industry', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '給料', '上昇', '処遇改善加算', '沖縄', '相場推移']],
        ];
    }

    /**
     * Phase 6: 初心者向け記事(beginner カテゴリ)
     * 未経験から介護業界へ転職を考える人向けの入門記事。
     */
    public static function beginnerDefinitions(): array
    {
        return [
            ['slug' => 'mikei-kara-kaigo-okinawa',          'category' => 'beginner', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['未経験', '介護職', '転職', '始め方', '沖縄', '無資格', '入門']],
            ['slug' => 'kaigo-1day-schedule-okinawa',       'category' => 'beginner', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護職', '1日', 'スケジュール', '仕事内容', 'リアル', '沖縄']],
            ['slug' => 'kaigo-muiteru-hito-okinawa',        'category' => 'beginner', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護職', '向いてる人', '適性', '性格', '沖縄', 'チェック']],
            ['slug' => 'kaigo-ningenkankei-okinawa',        'category' => 'beginner', 'area' => null, 'job_type' => null,                   'keywords' => ['介護現場', '人間関係', '対処法', '職場選び', '沖縄']],
            ['slug' => 'kaigo-tenshoku-mensetsu-okinawa',   'category' => 'beginner', 'area' => null, 'job_type' => null,                   'keywords' => ['介護職', '転職', '面接', '志望動機', '質問', '沖縄', '対策']],
            ['slug' => 'kaigo-rirekisyo-okinawa',           'category' => 'beginner', 'area' => null, 'job_type' => null,                   'keywords' => ['介護職', '履歴書', '書き方', '志望動機', '自己PR', '沖縄']],
            ['slug' => 'kaigo-shisetsu-shurui-okinawa',     'category' => 'beginner', 'area' => null, 'job_type' => null,                   'keywords' => ['介護施設', '種類', '違い', '特養', '老健', 'デイサービス', '沖縄']],
            ['slug' => 'kaigo-yakin-syosinsya-okinawa',     'category' => 'beginner', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護職', '夜勤', '初心者', '不安', '対策', '沖縄']],
        ];
    }

    /**
     * Phase 7: 実務・準備・面接・履歴書などの実用トピック(practical=介護・福祉の仕事術。介護・福祉の話題に限定)
     */
    public static function practicalDefinitions(): array
    {
        return [
            ['slug' => 'kaigo-mensetsu-shitsumon-okinawa',       'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', '面接', 'よくある質問', '回答例', '沖縄', '対策']],
            ['slug' => 'kaigo-jishoku-riyu-okinawa',             'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '志望動機', '例文', '書き方', '沖縄']],
            ['slug' => 'kaigo-shokumu-keirekisho-okinawa',       'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', '職務経歴書', '書き方', 'テンプレート', '沖縄']],
            ['slug' => 'kaigo-tenshoku-jiki-okinawa',            'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '転職', '時期', 'ベストタイミング', '4月', '10月', '沖縄']],
            ['slug' => 'kaigo-tenshoku-sagashikata-okinawa',     'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', '転職サイト', '選び方', 'ハローワーク', '違い', '沖縄']],
            ['slug' => 'kaigo-syakainhoken-okinawa',             'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '社会保険', '福利厚生', 'パート', '扶養', '沖縄']],
            ['slug' => 'kaigo-shinjin-okinawa',                  'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護職', '新人', '仕事', '覚え方', '心構え', '沖縄']],
            ['slug' => 'kaigo-strees-taisaku-okinawa',           'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', 'ストレス', '対処法', 'メンタル', '長続き', '沖縄']],
            ['slug' => 'kaigo-tenshoku-ideal-jobplace-okinawa',  'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', '職場選び', 'ホワイト', '見分け方', 'ブラック', '沖縄']],
            ['slug' => 'kaigo-shinsotsu-okinawa',                'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '新卒', '就職活動', '志望動機', '沖縄']],
            ['slug' => 'kaigo-fukugyou-okinawa',                 'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '副業', 'ダブルワーク', '注意点', '沖縄']],
            ['slug' => 'kaigo-w-license-okinawa',                'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', 'ダブルライセンス', '介護福祉士', 'ケアマネ', '取得順', '沖縄']],
            ['slug' => 'kaigo-yasumi-okinawa',                   'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '休み', '有給', '年間休日', '実態', '沖縄']],
            ['slug' => 'kaigo-teacher-okinawa',                  'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護', '実務者研修', '教員', '講師', '沖縄']],
        ];
    }

    /**
     * Phase 8: 個別テーマ(2026-10 追加)。テンプレート量産を避け、介護・福祉に限定した1本ずつ異なるテーマ
     * A:施設・事業所ごとの働き方 / B:未掲載の職種 / C:介護・福祉の仕事術 / D:沖縄ならでは / E:キャリアとお金
     */
    public static function curatedDefinitions(): array
    {
        return [
            ['slug' => 'tokuyou-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['特別養護老人ホーム', '特養', '働き方', '仕事内容', '1日の流れ', '向いている人', '沖縄']],
            ['slug' => 'rouken-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護老人保健施設', '老健', '在宅復帰', 'リハビリ', '介護職', '働き方', '沖縄']],
            ['slug' => 'grouphome-ninchisho-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['グループホーム', '認知症', '少人数', 'ユニットケア', '働き方', '沖縄']],
            ['slug' => 'yuryo-roujin-home-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['有料老人ホーム', '介護付き', '住宅型', '違い', '働き方', '沖縄']],
            ['slug' => 'sakoju-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['サービス付き高齢者向け住宅', 'サ高住', '安否確認', '生活支援', '働き方', '沖縄']],
            ['slug' => 'shoukibo-takinou-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['小規模多機能型居宅介護', '通い', '泊まり', '訪問', '働き方', '沖縄']],
            ['slug' => 'kantaki-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'nurse_welfare_facility', 'keywords' => ['看護小規模多機能型居宅介護', '看多機', '医療ニーズ', '看護師', '介護職', '沖縄']],
            ['slug' => 'day-service-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['デイサービス', '通所介護', '日勤のみ', '送迎', 'レクリエーション', '沖縄']],
            ['slug' => 'day-care-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'physical_therapist', 'keywords' => ['デイケア', '通所リハビリテーション', 'デイサービスとの違い', 'リハビリ職', '介護職', '沖縄']],
            ['slug' => 'houmon-nyuyoku-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['訪問入浴介護', '3人1組', '看護師', 'オペレーター', '仕事内容', '沖縄']],
            ['slug' => 'shogai-grouphome-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'life_support_worker', 'keywords' => ['障害者グループホーム', '共同生活援助', '世話人', '生活支援員', '夜間支援', '沖縄']],
            ['slug' => 'houkago-day-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'child_guidance_worker', 'keywords' => ['放課後等デイサービス', '放デイ', '児童指導員', '療育', '送迎', '沖縄']],
            ['slug' => 'jido-hattatsu-shien-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'childcare_worker', 'keywords' => ['児童発達支援', '未就学児', '療育', '保育士', '児童指導員', '沖縄']],
            ['slug' => 'shuro-keizoku-ab-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'employment_support_worker', 'keywords' => ['就労継続支援A型', '就労継続支援B型', '違い', '職業指導員', '就労支援員', '沖縄']],
            ['slug' => 'seikatsu-kaigo-hataraku-okinawa', 'category' => 'industry', 'area' => null, 'job_type' => 'life_support_worker', 'keywords' => ['生活介護', '障害者', '日中活動', '創作活動', '生活支援員', '沖縄']],
            ['slug' => 'school-social-worker-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'social_welfare_worker', 'keywords' => ['スクールソーシャルワーカー', 'SSW', '学校', '教育委員会', '社会福祉士', '沖縄']],
            ['slug' => 'jido-yougo-shisetsu-shokuin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'child_guidance_worker', 'keywords' => ['児童養護施設', '職員', '児童指導員', '保育士', '住み込み', '沖縄']],
            ['slug' => 'boshi-shienin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => null, 'keywords' => ['母子支援員', '母子生活支援施設', 'ひとり親', '仕事内容', '資格', '沖縄']],
            ['slug' => 'katei-shien-senmon-soudanin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'family_support_consultant', 'keywords' => ['家庭支援専門相談員', 'ファミリーソーシャルワーカー', '児童養護施設', '家族再統合', '沖縄']],
            ['slug' => 'fukushi-yougu-senmon-soudanin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => null, 'keywords' => ['福祉用具専門相談員', '福祉用具貸与', '講習', '営業', '介護保険', '沖縄']],
            ['slug' => 'kaigo-jimu-shigoto-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'care_admin', 'keywords' => ['介護事務', '介護報酬請求', 'レセプト', '受付', '未経験', '沖縄']],
            ['slug' => 'kaigo-sougei-driver-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => null, 'keywords' => ['介護施設', '送迎ドライバー', '福祉車両', '車いす', '普通免許', '沖縄']],
            ['slug' => 'shisetsu-chouriin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => null, 'keywords' => ['介護施設', '調理員', '嚥下食', '刻み食', '調理補助', '沖縄']],
            ['slug' => 'kinou-kunren-shidouin-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'physical_therapist', 'keywords' => ['機能訓練指導員', 'デイサービス', '個別機能訓練', '柔道整復師', '看護師', '沖縄']],
            ['slug' => 'shisetsu-kanri-eiyoushi-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'registered_dietitian', 'keywords' => ['管理栄養士', '介護施設', '栄養ケアマネジメント', '献立', '多職種連携', '沖縄']],
            ['slug' => 'touroku-helper-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'home_helper', 'keywords' => ['登録ヘルパー', '訪問介護', '直行直帰', '働き方', '収入', '沖縄']],
            ['slug' => 'iryouteki-care-ji-shien-okinawa', 'category' => 'job_type', 'area' => null, 'job_type' => 'child_guidance_worker', 'keywords' => ['医療的ケア児', '支援', 'たん吸引', '経管栄養', '放課後等デイサービス', '沖縄']],
            ['slug' => 'ijou-kaijo-youtsu-yobou-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['移乗介助', 'コツ', '腰痛予防', 'ボディメカニクス', '介護職', '長く働く']],
            ['slug' => 'kaigo-kiroku-kakikata-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護記録', '書き方', '例文', '客観的', '5W1H', '介護職']],
            ['slug' => 'moushiokuri-kotsu-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['申し送り', 'コツ', '引き継ぎ', '情報共有', '夜勤', '介護職']],
            ['slug' => 'hiyari-hatto-houkoku-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['ヒヤリハット', '事故報告', '書き方', '再発防止', '介護施設']],
            ['slug' => 'riyousha-kazoku-kakawari-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['利用者家族', '関わり方', '信頼関係', 'クレーム対応', '介護職']],
            ['slug' => 'yakin-sugoshikata-kamin-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['夜勤', '過ごし方', '仮眠', '体調管理', '巡視', '介護職']],
            ['slug' => 'shintai-kousoku-shinai-kaigo-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['身体拘束', '廃止', 'スピーチロック', '代替ケア', '介護施設']],
            ['slug' => 'gyakutai-boushi-kihon-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['高齢者虐待', '障害者虐待', '防止', '不適切ケア', '研修', '介護・福祉職']],
            ['slug' => 'kitaku-ganbou-taiou-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['認知症', '帰宅願望', '夕暮れ症候群', '対応', '声かけ', '介護職']],
            ['slug' => 'hattatsu-shogai-shien-kihon-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'child_guidance_worker', 'keywords' => ['発達障害', '子ども', '支援', '関わり方', '放課後等デイサービス', '児童指導員']],
            ['slug' => 'kobetsu-shien-keikaku-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'service_manager', 'keywords' => ['個別支援計画', '書き方', 'モニタリング', 'サービス管理責任者', '障害福祉']],
            ['slug' => 'kansenshou-taisaku-kaigo-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['感染症対策', '介護施設', '手洗い', 'インフルエンザ', 'ノロウイルス', '標準予防策']],
            ['slug' => 'ritou-kaigo-hataraku-okinawa', 'category' => 'area', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['離島', '宮古島', '石垣島', '久米島', '介護職', '移住', '働き方']],
            ['slug' => 'kurumashakai-tsukin-sougei-okinawa', 'category' => 'area', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['沖縄', '車社会', '車通勤', '送迎業務', '介護職', '渋滞']],
            ['slug' => 'taifuu-kaigo-shisetsu-taiou-okinawa', 'category' => 'area', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['台風', '介護施設', '出勤', '停電', 'BCP', '沖縄']],
            ['slug' => 'uchinaaguchi-koureisha-communication', 'category' => 'area', 'area' => null, 'job_type' => null, 'keywords' => ['うちなーぐち', '方言', '高齢者', 'コミュニケーション', '介護', '沖縄']],
            ['slug' => 'ui-turn-kaigo-okinawa', 'category' => 'area', 'area' => null, 'job_type' => null, 'keywords' => ['Uターン', 'Iターン', '移住', '沖縄', '介護職', '福祉職', '転職']],
            ['slug' => 'okinawa-kazokukan-zaitaku-kaigo', 'category' => 'area', 'area' => null, 'job_type' => 'home_helper', 'keywords' => ['沖縄', '家族観', '在宅介護', '訪問介護', '地域のつながり', '介護職']],
            ['slug' => 'okinawa-kaigo-shugaku-shikin-shien', 'category' => 'area', 'area' => null, 'job_type' => null, 'keywords' => ['沖縄県', '介護福祉士修学資金', '再就職準備金', '貸付', '就職支援', '福祉人材センター']],
            ['slug' => 'okinawa-chouju-kaigo-yobou', 'category' => 'area', 'area' => null, 'job_type' => null, 'keywords' => ['沖縄', '長寿', '介護予防', '高齢者', '地域', '介護・福祉職の役割']],
            ['slug' => 'kaigo-kara-seikatsu-soudanin-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'life_consultant', 'keywords' => ['介護職', '生活相談員', 'キャリアアップ', '要件', '仕事内容', '沖縄']],
            ['slug' => 'shisetsuchou-kanrisha-naru-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['施設長', '管理者', '介護施設', 'なるには', 'キャリア', '沖縄']],
            ['slug' => 'kaigo-kara-kangoshi-mezasu-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'nurse_welfare_facility', 'keywords' => ['介護職', '看護師', '准看護師', '働きながら', '学校', '沖縄']],
            ['slug' => 'houmon-kaigo-kaigyou-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'service_provision_manager', 'keywords' => ['訪問介護', '開業', '独立', '指定申請', '人員基準', '沖縄']],
            ['slug' => 'yakin-teate-souba-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['夜勤手当', '相場', '介護職', '施設形態別', '月収', '沖縄']],
            ['slug' => 'kaigo-taishokukin-fukushi-kyousai-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護職', '退職金', '社会福祉施設職員等退職手当共済', '中退共', '確認方法']],
            ['slug' => 'kaigo-rousai-youtsu-hoshou-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => 'care_staff_facility', 'keywords' => ['介護職', '労災', '腰痛', '申請', '補償', '職場の対応']],
            ['slug' => 'fukushi-tenshoku-kaisuu-hyouka-okinawa', 'category' => 'practical', 'area' => null, 'job_type' => null, 'keywords' => ['介護・福祉職', '転職回数', '面接', '評価', '伝え方', '沖縄']],
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

        ['title' => $title, 'h1' => $h1, 'meta' => $meta, 'body' => $body, 'image_url' => $imageUrl] = $this->generateDistinct($def, $area, $jobType);

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

        ['title' => $title, 'h1' => $h1, 'meta' => $meta, 'body' => $body, 'image_url' => $imageUrl] = $this->generateDistinct($def, $area, $jobType);

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

    // 既存記事タイトルとの類似度(文字bigramのJaccard)がこれ以上なら重複とみなし保存しない
    private const DUPLICATE_THRESHOLD = 0.6;

    /**
     * 既存記事と重ならない記事を生成する。
     * 似た既存タイトルをプロンプトに渡し、それでも重複タイトルになったら1回だけ再生成、だめなら例外(保存しない)。
     */
    private function generateDistinct(array $def, $area, $jobType): array
    {
        $existing = ContentArticle::where('slug', '!=', $def['slug'])->pluck('title')->all();
        $topic    = implode(' ', array_filter([$area?->name, $jobType?->name, implode(' ', $def['keywords'])]));
        $avoid    = $this->similarTitles($topic, $existing, 6, 0.15);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $data = $this->callGemini($def, $area, $jobType, $avoid);
            [$score, $closest] = $this->mostSimilar($data['title'], $existing);
            if ($score < self::DUPLICATE_THRESHOLD) {
                return $data;
            }
            // 重複したタイトルを明示して避けさせる
            $avoid = array_values(array_unique(array_merge([$closest], $avoid)));
        }

        throw new \RuntimeException("既存記事と重複するため保存しません(類似度{$score}: {$closest})");
    }

    /** @return string[] 類似度の高い順に既存タイトルを返す */
    private function similarTitles(string $text, array $titles, int $limit, float $min): array
    {
        $scored = [];
        foreach ($titles as $t) {
            $sc = $this->titleSimilarity($text, $t);
            if ($sc >= $min) {
                $scored[$t] = $sc;
            }
        }
        arsort($scored);
        return array_slice(array_keys($scored), 0, $limit);
    }

    /** @return array{0: float, 1: string} [最大類似度, そのタイトル] */
    private function mostSimilar(string $title, array $titles): array
    {
        $best = [0.0, ''];
        foreach ($titles as $t) {
            $sc = $this->titleSimilarity($title, $t);
            if ($sc > $best[0]) {
                $best = [round($sc, 2), $t];
            }
        }
        return $best;
    }

    private function titleSimilarity(string $a, string $b): float
    {
        $x = $this->bigrams($a);
        $y = $this->bigrams($b);
        if (!$x || !$y) {
            return 0.0;
        }
        $inter = count(array_intersect_key($x, $y));
        return $inter / (count($x) + count($y) - $inter);
    }

    private function bigrams(string $text): array
    {
        $text = preg_replace('/[｜|！!？?：:、。・（）()「」【】\s]/u', '', $text);
        // どの記事にも出る語は除外して比較(沖縄・求人など)
        $text  = str_replace(['沖縄県', '沖縄', 'を解説', '解説', '求人', '探す', '介護・福祉', 'とは', 'について'], '', $text);
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $grams = [];
        for ($i = 0; $i < count($chars) - 1; $i++) {
            $grams[$chars[$i] . $chars[$i + 1]] = true;
        }
        return $grams;
    }

    private function callGemini(array $def, $area, $jobType, array $avoidTitles = []): array
    {
        $areaName    = $area    ? $area->name    : '沖縄県';
        $jobTypeName = $jobType ? $jobType->name : 'なし';
        $keywords    = implode('、', $def['keywords']);
        $avoidBlock  = $avoidTitles
            ? "\n【既存記事(これらと切り口・内容・タイトルを重ねないこと)】\n- " . implode("\n- ", $avoidTitles) . "\n"
            : '';

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
{$avoidBlock}
【記事ルール】
- 誇張表現・ランキング・No.1などは禁止
- 事実ベースで書く（具体的な数字は「〜程度」「〜が多い」など推定表現を使う）
- 求職者が疑問に思うことを丁寧に解説する
- 介護・福祉の仕事・職場に関する内容に限定し、他業界にも当てはまる一般的な就職・転職論に広げない
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
            throw new \RuntimeException('Gemini記事生成失敗: ' . $e->getMessage(), 0, $e);
        }

        // 中身の薄い仮記事は公開しない(翌日以降に再生成される)
        throw new \RuntimeException('Gemini記事生成失敗: 応答のJSONが不正です');
    }

    private function fetchUnsplashImage(string $query): ?string
    {
        try {
            $apiKey   = config('services.pexels.api_key');
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
}
