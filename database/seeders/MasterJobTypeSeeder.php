<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJobTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // 介護系
            ['category' => '介護系', 'name' => '介護スタッフ（デイサービス）',        'slug' => 'care_day_service'],
            ['category' => '介護系', 'name' => '介護スタッフ（特別養護老人ホーム）',  'slug' => 'care_special_nursing'],
            ['category' => '介護系', 'name' => '介護スタッフ（グループホーム）',      'slug' => 'care_group_home'],
            ['category' => '介護系', 'name' => '介護スタッフ（有料老人ホーム）',      'slug' => 'care_paid_home'],
            ['category' => '介護系', 'name' => '介護スタッフ（訪問介護）',            'slug' => 'care_home_visit'],
            ['category' => '介護系', 'name' => '介護スタッフ（居宅介護）',            'slug' => 'care_home_based'],
            ['category' => '介護系', 'name' => '夜勤専従介護スタッフ',               'slug' => 'care_night_only'],
            ['category' => '介護系', 'name' => '介護職員初任者研修保有者歓迎',        'slug' => 'care_initial_training'],
            ['category' => '介護系', 'name' => '介護福祉士歓迎',                     'slug' => 'care_welfare_worker'],
            // ヘルパー系
            ['category' => 'ヘルパー系', 'name' => '訪問ヘルパー',     'slug' => 'helper_visit'],
            ['category' => 'ヘルパー系', 'name' => '生活援助ヘルパー', 'slug' => 'helper_daily_living'],
            ['category' => 'ヘルパー系', 'name' => '身体介護ヘルパー', 'slug' => 'helper_physical_care'],
            ['category' => 'ヘルパー系', 'name' => 'ホームヘルパー',   'slug' => 'helper_home'],
            // 看護・医療系
            ['category' => '看護・医療系', 'name' => '看護助手',         'slug' => 'nursing_assistant'],
            ['category' => '看護・医療系', 'name' => '看護師（施設）',   'slug' => 'nurse_facility'],
            ['category' => '看護・医療系', 'name' => '准看護師（施設）', 'slug' => 'assistant_nurse_facility'],
            // リハビリ系
            ['category' => 'リハビリ系', 'name' => '機能訓練指導員',     'slug' => 'rehab_trainer'],
            ['category' => 'リハビリ系', 'name' => 'リハビリ補助スタッフ', 'slug' => 'rehab_assistant'],
            // 保育・福祉系
            ['category' => '保育・福祉系', 'name' => '保育補助スタッフ',              'slug' => 'childcare_assistant'],
            ['category' => '保育・福祉系', 'name' => '学童保育スタッフ',              'slug' => 'after_school_care'],
            ['category' => '保育・福祉系', 'name' => '放課後等デイサービススタッフ',  'slug' => 'afterschool_day_service'],
            ['category' => '保育・福祉系', 'name' => '障害者支援スタッフ',            'slug' => 'disability_support'],
            ['category' => '保育・福祉系', 'name' => '生活支援員',                   'slug' => 'life_support_worker'],
            // 相談・事務系
            ['category' => '相談・事務系', 'name' => 'ケアマネージャー（居宅）', 'slug' => 'care_manager_home'],
            ['category' => '相談・事務系', 'name' => 'ケアマネージャー（施設）', 'slug' => 'care_manager_facility'],
            ['category' => '相談・事務系', 'name' => '生活相談員',               'slug' => 'life_consultant'],
            ['category' => '相談・事務系', 'name' => '介護事務',                 'slug' => 'care_admin'],
            ['category' => '相談・事務系', 'name' => '医療事務',                 'slug' => 'medical_admin'],
        ];

        foreach ($types as $i => $type) {
            DB::table('master_job_types')->insertOrIgnore(array_merge($type, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
