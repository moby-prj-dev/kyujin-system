<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAppealSeeder extends Seeder
{
    public function run(): void
    {
        $appeals = [
            // 経験・資格系
            ['category' => '経験・資格系', 'name' => '未経験OK',              'slug' => 'no_experience',          'question_text' => '未経験でも大丈夫ですよ！介護の経験はありますか？'],
            ['category' => '経験・資格系', 'name' => '無資格OK',              'slug' => 'no_license',             'question_text' => '資格がなくても応募できます！現在お持ちの資格はありますか？'],
            ['category' => '経験・資格系', 'name' => '資格取得支援あり',      'slug' => 'license_support_appeal', 'question_text' => '働きながら資格取得を目指したいですか？'],
            ['category' => '経験・資格系', 'name' => '介護職員初任者研修取得支援', 'slug' => 'initial_training_support', 'question_text' => '介護職員初任者研修の取得サポートを希望しますか？'],
            ['category' => '経験・資格系', 'name' => '実務者研修取得支援',    'slug' => 'practical_training_support', 'question_text' => '実務者研修の取得サポートを希望しますか？'],
            ['category' => '経験・資格系', 'name' => 'ブランクOK',            'slug' => 'blank_ok',               'question_text' => 'ブランクがあっても大丈夫です！最後に介護のお仕事をされたのはいつ頃ですか？'],
            ['category' => '経験・資格系', 'name' => 'ブランク3年以上OK',     'slug' => 'long_blank_ok',          'question_text' => '3年以上のブランクがあっても安心してご応募いただけます。現状を教えてください。'],
            // 属性歓迎系
            ['category' => '属性歓迎系', 'name' => '主婦（夫）歓迎',       'slug' => 'housewife_welcome',      'question_text' => '家事・育児との両立をご希望ですか？'],
            ['category' => '属性歓迎系', 'name' => 'シニア歓迎（60代活躍中）', 'slug' => 'senior_welcome',     'question_text' => '60代以上の方も活躍中です！年齢を教えていただけますか？'],
            ['category' => '属性歓迎系', 'name' => '男性活躍中',           'slug' => 'male_active',            'question_text' => '男性スタッフが多数活躍中です！'],
            ['category' => '属性歓迎系', 'name' => '女性活躍中',           'slug' => 'female_active',          'question_text' => '女性スタッフが多数活躍中です！'],
            ['category' => '属性歓迎系', 'name' => 'Wワーク歓迎',          'slug' => 'double_work',            'question_text' => '他のお仕事との掛け持ちをご希望ですか？'],
            ['category' => '属性歓迎系', 'name' => '副業OK',               'slug' => 'side_job_ok',            'question_text' => '副業・兼業をご希望ですか？'],
            // 収入・待遇系
            ['category' => '収入・待遇系', 'name' => '高時給',             'slug' => 'high_wage',              'question_text' => '高時給でしっかり稼ぎたいですか？'],
            ['category' => '収入・待遇系', 'name' => '日払いOK',           'slug' => 'daily_pay',              'question_text' => '日払いでのお支払いを希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '週払いOK',           'slug' => 'weekly_pay',             'question_text' => '週払いでのお支払いを希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '処遇改善手当あり',   'slug' => 'treatment_improvement',  'question_text' => '処遇改善手当ありの職場を希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '夜勤手当あり',       'slug' => 'night_allowance',        'question_text' => '夜勤手当を受け取りたいですか？'],
            ['category' => '収入・待遇系', 'name' => '資格手当あり',       'slug' => 'license_allowance',      'question_text' => '資格手当ありの職場を希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '昇給あり',           'slug' => 'salary_raise',           'question_text' => '昇給制度ありの職場を希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '賞与あり',           'slug' => 'bonus',                  'question_text' => '賞与ありの職場を希望しますか？'],
            ['category' => '収入・待遇系', 'name' => '正社員登用あり',     'slug' => 'full_time_promotion',    'question_text' => '将来的に正社員を目指したいですか？'],
            // 職場環境系
            ['category' => '職場環境系', 'name' => '研修充実',            'slug' => 'good_training',          'question_text' => '充実した研修制度のある職場を希望しますか？'],
            ['category' => '職場環境系', 'name' => 'マニュアルあり',      'slug' => 'manual_provided',        'question_text' => 'マニュアルが整備された職場を希望しますか？'],
            ['category' => '職場環境系', 'name' => 'チームワーク重視',    'slug' => 'teamwork',               'question_text' => 'チームワークを大切にした職場環境を希望しますか？'],
            ['category' => '職場環境系', 'name' => 'アットホームな職場',  'slug' => 'home_like',              'question_text' => 'アットホームな雰囲気の職場を希望しますか？'],
            ['category' => '職場環境系', 'name' => 'スタッフ定着率高い',  'slug' => 'high_retention',         'question_text' => 'スタッフが長く働き続けられる職場環境を希望しますか？'],
            ['category' => '職場環境系', 'name' => '新規オープン施設',    'slug' => 'new_facility',           'question_text' => '新しい施設で一からチームを作りたいですか？'],
            ['category' => '職場環境系', 'name' => '少人数職場',          'slug' => 'small_team',             'question_text' => '少人数でアットホームな環境を希望しますか？'],
        ];

        foreach ($appeals as $i => $appeal) {
            DB::table('master_appeals')->insertOrIgnore(array_merge($appeal, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
