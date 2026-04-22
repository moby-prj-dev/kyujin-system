<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJobTypeSeeder extends Seeder
{
    public function run(): void
    {
        // 提供リストと重複・類似する旧エントリを削除
        DB::table('master_job_types')->whereIn('slug', [
            'care_day_service',
            'care_special_nursing',
            'care_group_home',
            'care_paid_home',
            'care_home_visit',
            'care_home_based',
            'care_night_only',
            'care_initial_training',
            'helper_visit',
            'helper_daily_living',
            'helper_physical_care',
            'helper_home',
            'nursing_assistant',
            'nurse_facility',
            'assistant_nurse_facility',
            'rehab_trainer',
            'rehab_assistant',
            'childcare_assistant',
            'after_school_care',
            'afterschool_day_service',
            'disability_support',
            'care_manager_home',
            'care_manager_facility',
        ])->delete();

        $types = [
            // 介護・高齢者福祉
            ['category' => '介護・高齢者福祉', 'name' => '介護職員（施設）',                  'slug' => 'care_staff_facility'],
            ['category' => '介護・高齢者福祉', 'name' => '介護福祉士',                        'slug' => 'care_welfare_worker'],
            ['category' => '介護・高齢者福祉', 'name' => 'ホームヘルパー（訪問介護員）',      'slug' => 'home_helper'],
            ['category' => '介護・高齢者福祉', 'name' => 'ケアマネジャー（介護支援専門員）',  'slug' => 'care_manager'],
            ['category' => '介護・高齢者福祉', 'name' => 'サービス提供責任者',                'slug' => 'service_provision_manager'],
            ['category' => '介護・高齢者福祉', 'name' => '生活相談員',                        'slug' => 'life_consultant'],
            // 障害者福祉
            ['category' => '障害者福祉', 'name' => '生活支援員',                   'slug' => 'life_support_worker'],
            ['category' => '障害者福祉', 'name' => '就労支援員',                   'slug' => 'employment_support_worker'],
            ['category' => '障害者福祉', 'name' => 'サービス管理責任者（サビ管）', 'slug' => 'service_manager'],
            ['category' => '障害者福祉', 'name' => '行動援護従業者',               'slug' => 'behavioral_guidance_worker'],
            ['category' => '障害者福祉', 'name' => '同行援護従業者',               'slug' => 'accompanying_guidance_worker'],
            ['category' => '障害者福祉', 'name' => '強度行動障害支援者',           'slug' => 'intensive_behavioral_support'],
            // 児童福祉
            ['category' => '児童福祉', 'name' => '保育士',                          'slug' => 'childcare_worker'],
            ['category' => '児童福祉', 'name' => '児童指導員',                      'slug' => 'child_guidance_worker'],
            ['category' => '児童福祉', 'name' => '児童発達支援管理責任者（児発管）', 'slug' => 'child_dev_manager'],
            ['category' => '児童福祉', 'name' => '放課後児童支援員',                'slug' => 'after_school_child_worker'],
            ['category' => '児童福祉', 'name' => '家庭支援専門相談員',              'slug' => 'family_support_consultant'],
            // 相談支援・ソーシャルワーク
            ['category' => '相談支援・ソーシャルワーク', 'name' => '社会福祉士',                   'slug' => 'social_welfare_worker'],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '精神保健福祉士（PSW）',         'slug' => 'psychiatric_social_worker'],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '相談支援専門員',               'slug' => 'consultation_support_specialist'],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '医療ソーシャルワーカー（MSW）', 'slug' => 'medical_social_worker'],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '生活困窮者支援員',             'slug' => 'poverty_support_worker'],
            // 医療・リハビリ職
            ['category' => '医療・リハビリ職', 'name' => '看護師（福祉施設勤務）', 'slug' => 'nurse_welfare_facility'],
            ['category' => '医療・リハビリ職', 'name' => '理学療法士（PT）',       'slug' => 'physical_therapist'],
            ['category' => '医療・リハビリ職', 'name' => '作業療法士（OT）',       'slug' => 'occupational_therapist'],
            ['category' => '医療・リハビリ職', 'name' => '言語聴覚士（ST）',       'slug' => 'speech_therapist'],
            ['category' => '医療・リハビリ職', 'name' => '管理栄養士（施設）',     'slug' => 'registered_dietitian'],
            // 事務・管理
            ['category' => '事務・管理', 'name' => '介護事務', 'slug' => 'care_admin'],
            ['category' => '事務・管理', 'name' => '医療事務', 'slug' => 'medical_admin'],
        ];

        $rows = array_map(fn($type, $i) => [
            ...$type,
            'sort_order' => $i + 1,
            'is_active'  => true,
            'created_at' => now(),
        ], $types, array_keys($types));

        DB::table('master_job_types')->upsert(
            $rows,
            ['slug'],
            ['category', 'name', 'sort_order']
        );
    }
}
