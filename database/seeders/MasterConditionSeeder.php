<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            // 勤務日数・時間
            ['category' => '勤務日数・時間', 'name' => '週1日〜OK',      'slug' => 'one_day_week',     'question_text' => '週1日〜の勤務は可能ですか？'],
            ['category' => '勤務日数・時間', 'name' => '週2日〜OK',      'slug' => 'two_days_week',    'question_text' => '週2日〜の勤務は可能ですか？'],
            ['category' => '勤務日数・時間', 'name' => '週3日〜OK',      'slug' => 'three_days_week',  'question_text' => '週3日〜の勤務は可能ですか？'],
            ['category' => '勤務日数・時間', 'name' => '週4日〜OK',      'slug' => 'four_days_week',   'question_text' => '週4日〜の勤務は可能ですか？'],
            ['category' => '勤務日数・時間', 'name' => 'フルタイム勤務', 'slug' => 'full_time_work',   'question_text' => 'フルタイム（週5日）での勤務は可能ですか？'],
            ['category' => '勤務日数・時間', 'name' => '短時間勤務OK',   'slug' => 'short_hours',      'question_text' => '短時間勤務を希望しますか？'],
            ['category' => '勤務日数・時間', 'name' => '扶養内勤務OK',   'slug' => 'within_dependent', 'question_text' => '扶養内での勤務を希望しますか？'],
            ['category' => '勤務日数・時間', 'name' => '1日4時間〜OK',   'slug' => 'four_hours_day',   'question_text' => '1日4時間〜の勤務は可能ですか？'],
            // シフト・時間帯
            ['category' => 'シフト・時間帯', 'name' => '日勤のみ',      'slug' => 'day_shift_only',   'question_text' => '日勤のみの勤務でよろしいですか？'],
            ['category' => 'シフト・時間帯', 'name' => '夜勤あり',      'slug' => 'night_shift',      'question_text' => '夜勤ありの条件でよろしいですか？'],
            ['category' => 'シフト・時間帯', 'name' => '夜勤専従',      'slug' => 'night_shift_only', 'question_text' => '夜勤専従での勤務は可能ですか？'],
            ['category' => 'シフト・時間帯', 'name' => '早朝勤務あり',  'slug' => 'early_morning',    'question_text' => '早朝からの勤務は可能ですか？'],
            ['category' => 'シフト・時間帯', 'name' => '夕方〜勤務OK',  'slug' => 'evening_start',    'question_text' => '夕方からの勤務は可能ですか？'],
            ['category' => 'シフト・時間帯', 'name' => 'シフト相談OK',  'slug' => 'flexible_shift',   'question_text' => 'シフトは相談して決めたいですか？'],
            ['category' => 'シフト・時間帯', 'name' => '土日休み',      'slug' => 'weekend_off',      'question_text' => '土日休みを希望しますか？'],
            ['category' => 'シフト・時間帯', 'name' => '土日出勤可',    'slug' => 'weekend_work',     'question_text' => '土日の出勤は可能ですか？'],
            ['category' => 'シフト・時間帯', 'name' => '週末のみOK',    'slug' => 'weekend_only',     'question_text' => '週末のみの勤務でよろしいですか？'],
            // 勤務環境
            ['category' => '勤務環境', 'name' => '残業ほぼなし',    'slug' => 'no_overtime',      'question_text' => '残業はほぼない職場ですが、よろしいですか？'],
            ['category' => '勤務環境', 'name' => '車通勤OK',        'slug' => 'car_commute',      'question_text' => '車での通勤は可能ですか？'],
            ['category' => '勤務環境', 'name' => 'バイク通勤OK',    'slug' => 'bike_commute',     'question_text' => 'バイクでの通勤は可能ですか？'],
            ['category' => '勤務環境', 'name' => '駅徒歩10分以内',  'slug' => 'near_station',     'question_text' => '最寄り駅から徒歩10分以内の職場をご希望ですか？'],
            ['category' => '勤務環境', 'name' => '寮・社宅あり',    'slug' => 'dormitory',        'question_text' => '寮・社宅の利用を希望しますか？'],
            // 待遇
            ['category' => '待遇', 'name' => '交通費全額支給', 'slug' => 'full_commute_fee',   'question_text' => '交通費全額支給の条件でよろしいですか？'],
            ['category' => '待遇', 'name' => '社会保険完備',   'slug' => 'social_insurance',   'question_text' => '社会保険完備の職場を希望しますか？'],
            ['category' => '待遇', 'name' => '資格取得支援あり', 'slug' => 'license_support',  'question_text' => '働きながら資格取得の支援を受けたいですか？'],
            ['category' => '待遇', 'name' => '制服貸与あり',   'slug' => 'uniform_provided',   'question_text' => '制服貸与ありの職場でよろしいですか？'],
        ];

        foreach ($conditions as $i => $condition) {
            DB::table('master_conditions')->insertOrIgnore(array_merge($condition, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
