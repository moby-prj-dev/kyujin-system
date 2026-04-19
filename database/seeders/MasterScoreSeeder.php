<?php

namespace Database\Seeders;

use App\Models\MasterAppeal;
use App\Models\MasterCondition;
use App\Models\MasterJobType;
use Illuminate\Database\Seeder;

class MasterScoreSeeder extends Seeder
{
    public function run(): void
    {
        $jobScores = [
            '夜勤専従介護スタッフ'         => [5, true],
            '看護師（施設）'               => [4, true],
            '准看護師（施設）'             => [4, true],
            'ケアマネージャー（居宅）'      => [4, true],
            'ケアマネージャー（施設）'      => [4, true],
            '生活相談員'                   => [4, true],
            'リハビリ補助スタッフ'         => [2, false],
        ];

        foreach ($jobScores as $name => [$score, $isMain]) {
            MasterJobType::where('name', $name)->update([
                'score'            => $score,
                'is_main_candidate' => $isMain,
            ]);
        }

        $conditionScores = [
            '週1日〜OK'      => [4, true],
            '週2日〜OK'      => [4, true],
            '週3日〜OK'      => [3, false],
            '週4日〜OK'      => [3, false],
            'フルタイム勤務' => [2, false],
            '短時間勤務OK'   => [4, true],
            '扶養内勤務OK'   => [4, true],
            '1日4時間〜OK'   => [4, true],
            '日勤のみ'       => [4, true],
            '夜勤あり'       => [2, false],
            '夜勤専従'       => [5, true],
            '早朝勤務あり'   => [2, false],
            '夕方〜勤務OK'   => [3, false],
            'シフト相談OK'   => [3, false],
            '土日休み'       => [4, true],
            '土日出勤可'     => [2, false],
            '週末のみOK'     => [3, false],
            '残業ほぼなし'   => [3, false],
            '車通勤OK'       => [2, false],
            'バイク通勤OK'   => [2, false],
            '駅徒歩10分以内' => [3, false],
            'マイカー通勤OK' => [2, false],
            '寮・社宅あり'   => [4, true],
            '交通費全額支給' => [3, false],
            '社会保険完備'   => [2, false],
            '資格取得支援あり' => [4, true],
            '制服貸与あり'   => [1, false],
        ];

        foreach ($conditionScores as $name => [$score, $isMain]) {
            MasterCondition::where('name', $name)->update([
                'score'            => $score,
                'is_main_candidate' => $isMain,
            ]);
        }

        $appealScores = [
            '未経験OK'               => [5, true],
            '無資格OK'               => [5, true],
            '高時給'                 => [5, true],
            '日払いOK'               => [5, true],
            '新規オープン施設'        => [5, true],
            '資格取得支援あり'        => [4, true],
            '介護職員初任者研修取得支援' => [4, true],
            '実務者研修取得支援'      => [4, true],
            'ブランクOK'             => [4, true],
            'ブランク3年以上OK'       => [4, true],
            'Wワーク歓迎'            => [4, true],
            '副業OK'                 => [4, true],
            '週払いOK'               => [4, true],
            '処遇改善手当あり'        => [4, true],
            '昇給あり'               => [4, true],
            '賞与あり'               => [4, true],
            '正社員登用あり'          => [4, true],
            '主婦（夫）歓迎'         => [3, false],
            'シニア歓迎（60代活躍中）' => [3, false],
            '夜勤手当あり'           => [3, false],
            '資格手当あり'           => [3, false],
            '研修充実'               => [3, false],
            'スタッフ定着率高い'      => [2, false],
            '少人数職場'             => [2, false],
            'マニュアルあり'         => [2, false],
            '男性活躍中'             => [1, false],
            '女性活躍中'             => [1, false],
            'チームワーク重視'       => [1, false],
            'アットホームな職場'      => [1, false],
        ];

        foreach ($appealScores as $name => [$score, $isMain]) {
            MasterAppeal::where('name', $name)->update([
                'score'            => $score,
                'is_main_candidate' => $isMain,
            ]);
        }
    }
}
