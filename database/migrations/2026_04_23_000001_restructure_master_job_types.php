<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
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
            ['category' => '介護・高齢者福祉', 'name' => '介護職員（施設）',                 'slug' => 'care_staff_facility',         'sort_order' => 1],
            ['category' => '介護・高齢者福祉', 'name' => '介護福祉士',                       'slug' => 'care_welfare_worker',          'sort_order' => 2],
            ['category' => '介護・高齢者福祉', 'name' => 'ホームヘルパー（訪問介護員）',     'slug' => 'home_helper',                  'sort_order' => 3],
            ['category' => '介護・高齢者福祉', 'name' => 'ケアマネジャー（介護支援専門員）', 'slug' => 'care_manager',                 'sort_order' => 4],
            ['category' => '介護・高齢者福祉', 'name' => 'サービス提供責任者',               'slug' => 'service_provision_manager',    'sort_order' => 5],
            ['category' => '介護・高齢者福祉', 'name' => '生活相談員',                       'slug' => 'life_consultant',              'sort_order' => 6],
            // 障害者福祉
            ['category' => '障害者福祉', 'name' => '生活支援員',                   'slug' => 'life_support_worker',           'sort_order' => 7],
            ['category' => '障害者福祉', 'name' => '就労支援員',                   'slug' => 'employment_support_worker',     'sort_order' => 8],
            ['category' => '障害者福祉', 'name' => 'サービス管理責任者（サビ管）', 'slug' => 'service_manager',               'sort_order' => 9],
            ['category' => '障害者福祉', 'name' => '行動援護従業者',               'slug' => 'behavioral_guidance_worker',    'sort_order' => 10],
            ['category' => '障害者福祉', 'name' => '同行援護従業者',               'slug' => 'accompanying_guidance_worker',  'sort_order' => 11],
            ['category' => '障害者福祉', 'name' => '強度行動障害支援者',           'slug' => 'intensive_behavioral_support',  'sort_order' => 12],
            // 児童福祉
            ['category' => '児童福祉', 'name' => '保育士',                          'slug' => 'childcare_worker',              'sort_order' => 13],
            ['category' => '児童福祉', 'name' => '児童指導員',                      'slug' => 'child_guidance_worker',         'sort_order' => 14],
            ['category' => '児童福祉', 'name' => '児童発達支援管理責任者（児発管）', 'slug' => 'child_dev_manager',             'sort_order' => 15],
            ['category' => '児童福祉', 'name' => '放課後児童支援員',                'slug' => 'after_school_child_worker',     'sort_order' => 16],
            ['category' => '児童福祉', 'name' => '家庭支援専門相談員',              'slug' => 'family_support_consultant',     'sort_order' => 17],
            // 相談支援・ソーシャルワーク
            ['category' => '相談支援・ソーシャルワーク', 'name' => '社会福祉士',                   'slug' => 'social_welfare_worker',             'sort_order' => 18],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '精神保健福祉士（PSW）',         'slug' => 'psychiatric_social_worker',         'sort_order' => 19],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '相談支援専門員',               'slug' => 'consultation_support_specialist',   'sort_order' => 20],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '医療ソーシャルワーカー（MSW）', 'slug' => 'medical_social_worker',             'sort_order' => 21],
            ['category' => '相談支援・ソーシャルワーク', 'name' => '生活困窮者支援員',             'slug' => 'poverty_support_worker',            'sort_order' => 22],
            // 医療・リハビリ職
            ['category' => '医療・リハビリ職', 'name' => '看護師（福祉施設勤務）', 'slug' => 'nurse_welfare_facility',   'sort_order' => 23],
            ['category' => '医療・リハビリ職', 'name' => '理学療法士（PT）',       'slug' => 'physical_therapist',       'sort_order' => 24],
            ['category' => '医療・リハビリ職', 'name' => '作業療法士（OT）',       'slug' => 'occupational_therapist',   'sort_order' => 25],
            ['category' => '医療・リハビリ職', 'name' => '言語聴覚士（ST）',       'slug' => 'speech_therapist',         'sort_order' => 26],
            ['category' => '医療・リハビリ職', 'name' => '管理栄養士（施設）',     'slug' => 'registered_dietitian',     'sort_order' => 27],
            // 事務・管理
            ['category' => '事務・管理', 'name' => '介護事務', 'slug' => 'care_admin',    'sort_order' => 28],
            ['category' => '事務・管理', 'name' => '医療事務', 'slug' => 'medical_admin', 'sort_order' => 29],
        ];

        foreach ($types as $type) {
            DB::table('master_job_types')->upsert(
                [...$type, 'is_active' => true, 'created_at' => now()],
                ['slug'],
                ['category', 'name', 'sort_order']
            );
        }
    }

    public function down(): void
    {
        DB::table('master_job_types')->whereIn('slug', [
            'care_staff_facility', 'home_helper', 'care_manager',
            'employment_support_worker', 'behavioral_guidance_worker',
            'accompanying_guidance_worker', 'intensive_behavioral_support',
            'childcare_worker', 'child_guidance_worker', 'after_school_child_worker',
            'family_support_consultant', 'social_welfare_worker',
            'psychiatric_social_worker', 'medical_social_worker',
            'poverty_support_worker', 'nurse_welfare_facility',
            'physical_therapist', 'occupational_therapist',
            'speech_therapist', 'registered_dietitian',
        ])->delete();
    }
};
