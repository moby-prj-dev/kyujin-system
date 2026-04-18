#!/bin/bash
# ============================================================
# 求人LP自動生成システム - マスターデータSeeder一括配置スクリプト
# 実行場所: ~/kyujin-system（Laravelプロジェクトのルート）
# 実行方法: bash setup_seeders.sh
# ============================================================

set -e
echo "=========================================="
echo " Seederファイル配置を開始します..."
echo "=========================================="

SEEDER_DIR="database/seeders"

# ============================================================
# MasterAreaSeeder
# ============================================================
cat > $SEEDER_DIR/MasterAreaSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            // 東京都 / 都心・城南
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '新宿区',   'slug' => 'shinjuku'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '渋谷区',   'slug' => 'shibuya'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '港区',     'slug' => 'minato'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '千代田区', 'slug' => 'chiyoda'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '中央区',   'slug' => 'chuo'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '品川区',   'slug' => 'shinagawa'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '目黒区',   'slug' => 'meguro'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '大田区',   'slug' => 'ota'],
            // 東京都 / 城東
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '江東区',   'slug' => 'koto'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '墨田区',   'slug' => 'sumida'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '江戸川区', 'slug' => 'edogawa'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '葛飾区',   'slug' => 'katsushika'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '足立区',   'slug' => 'adachi'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '荒川区',   'slug' => 'arakawa'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '台東区',   'slug' => 'taito'],
            // 東京都 / 城北
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '豊島区', 'slug' => 'toshima'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '北区',   'slug' => 'kita'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '板橋区', 'slug' => 'itabashi'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '練馬区', 'slug' => 'nerima'],
            // 東京都 / 城西
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '杉並区', 'slug' => 'suginami'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '中野区', 'slug' => 'nakano'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '世田谷区', 'slug' => 'setagaya'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '文京区', 'slug' => 'bunkyo'],
            // 東京都 / 多摩
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '八王子市',  'slug' => 'hachioji'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '立川市',    'slug' => 'tachikawa'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '町田市',    'slug' => 'machida'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '府中市',    'slug' => 'fuchu'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '調布市',    'slug' => 'chofu'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '三鷹市',    'slug' => 'mitaka'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '武蔵野市',  'slug' => 'musashino'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '西東京市',  'slug' => 'nishitokyo'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '東村山市',  'slug' => 'higashimurayama'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '小平市',    'slug' => 'kodaira'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '日野市',    'slug' => 'hino'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '多摩市',    'slug' => 'tama'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '稲城市',    'slug' => 'inagi'],
            // 沖縄県 / 那覇・南部
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '那覇市',   'slug' => 'naha'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '豊見城市', 'slug' => 'tomigusuku'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '糸満市',   'slug' => 'itoman'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '南城市',   'slug' => 'nanjo'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '八重瀬町', 'slug' => 'yaese'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '南風原町', 'slug' => 'haebaru'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '与那原町', 'slug' => 'yonabaru'],
            // 沖縄県 / 中部
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '浦添市',   'slug' => 'urasoe'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '宜野湾市', 'slug' => 'ginowan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '沖縄市',   'slug' => 'okinawa_city'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => 'うるま市', 'slug' => 'uruma'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '北谷町',   'slug' => 'chatan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '嘉手納町', 'slug' => 'kadena'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '読谷村',   'slug' => 'yomitan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '北中城村', 'slug' => 'kitanakagusuku'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '中城村',   'slug' => 'nakagusuku'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '西原町',   'slug' => 'nishihara'],
            // 沖縄県 / 北部
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '名護市',   'slug' => 'nago'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '恩納村',   'slug' => 'onna'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '今帰仁村', 'slug' => 'nakijin'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '本部町',   'slug' => 'motobu'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '金武町',   'slug' => 'kin'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '宜野座村', 'slug' => 'ginoza'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '大宜味村', 'slug' => 'ogimi'],
            // 沖縄県 / 離島
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '石垣市',  'slug' => 'ishigaki'],
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '宮古島市', 'slug' => 'miyakojima'],
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '久米島町', 'slug' => 'kumejima'],
        ];

        foreach ($areas as $i => $area) {
            DB::table('master_areas')->insertOrIgnore(array_merge($area, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
EOF

# ============================================================
# MasterJobTypeSeeder
# ============================================================
cat > $SEEDER_DIR/MasterJobTypeSeeder.php << 'EOF'
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
EOF

# ============================================================
# MasterEmploymentTypeSeeder
# ============================================================
cat > $SEEDER_DIR/MasterEmploymentTypeSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterEmploymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => '正社員',   'slug' => 'full_time'],
            ['name' => '契約社員', 'slug' => 'contract'],
            ['name' => 'パート',   'slug' => 'part_time'],
            ['name' => 'アルバイト', 'slug' => 'part_time_casual'],
            ['name' => '業務委託', 'slug' => 'freelance'],
            ['name' => '派遣社員', 'slug' => 'temp_staff'],
        ];

        foreach ($types as $i => $type) {
            DB::table('master_employment_types')->insertOrIgnore(array_merge($type, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
EOF

# ============================================================
# MasterConditionSeeder
# ============================================================
cat > $SEEDER_DIR/MasterConditionSeeder.php << 'EOF'
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
            ['category' => '勤務環境', 'name' => 'マイカー通勤OK',  'slug' => 'mycar_commute',    'question_text' => 'マイカーでの通勤は可能ですか？'],
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
EOF

# ============================================================
# MasterAppealSeeder
# ============================================================
cat > $SEEDER_DIR/MasterAppealSeeder.php << 'EOF'
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
EOF

echo "  ✅ Seederファイル 5件 配置完了"

# ============================================================
# DatabaseSeeder を更新
# ============================================================
cat > $SEEDER_DIR/DatabaseSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterAreaSeeder::class,
            MasterJobTypeSeeder::class,
            MasterEmploymentTypeSeeder::class,
            MasterConditionSeeder::class,
            MasterAppealSeeder::class,
        ]);
    }
}
EOF

echo "  ✅ DatabaseSeeder 更新完了"

# ============================================================
# Seeder 実行
# ============================================================
echo ""
echo "[2/2] php artisan db:seed を実行中..."
./vendor/bin/sail artisan db:seed

echo ""
echo "=========================================="
echo " ✅ マスターデータの投入が完了しました！"
echo ""
echo "   master_areas          : エリア（東京・沖縄）"
echo "   master_job_types      : 職種（介護・医療・福祉）"
echo "   master_employment_types: 雇用形態"
echo "   master_conditions     : 勤務条件（LINE質問文付き）"
echo "   master_appeals        : アピールポイント（LINE質問文付き）"
echo "=========================================="
