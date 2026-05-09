<?php

namespace App\Services;

use App\Models\Job;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use Illuminate\Database\Eloquent\Collection;
use LINE\Clients\MessagingApi\Model\MessageAction;
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\QuickReplyItem;
use LINE\Clients\MessagingApi\Model\TextMessage;

class LineMessageBuilder
{
    public static function follow(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "友だち追加ありがとうございます！\n\n求人ページの「LINEで応募する」ボタンから、かんたんに応募できます。",
        ]);
    }

    public static function confirmation(Job $job, array $searchConditions = []): TextMessage
    {
        $userPrefs = self::formatUserPreferences($searchConditions);

        if ($userPrefs !== []) {
            $lines = ["ご応募ありがとうございます！\n\n以下の希望条件で応募を受け付けます。\n"];
            $lines = array_merge($lines, $userPrefs);
            $lines[] = "\nこの求人はあなたの希望条件と一致しています。\nこの内容で応募しますか？";
        } else {
            $areas      = $job->jobAreas->map(fn($ja) => $ja->area?->name)->filter()->join(' / ');
            $jobTypes   = $job->jobJobTypes->map(fn($jt) => $jt->jobType?->name)->filter()->join('、');
            $empTypes   = $job->jobEmploymentTypes->map(fn($je) => $je->employmentType?->name)->filter()->join('、');
            $conditions = $job->jobConditions->map(fn($jc) => $jc->condition?->name)->filter()->join(' / ');

            $lines = ["ご応募ありがとうございます！\n\n以下の求人への応募を受け付けます。\n"];
            if ($areas)      $lines[] = "・勤務地：{$areas}";
            if ($jobTypes)   $lines[] = "・職種：{$jobTypes}";
            if ($empTypes)   $lines[] = "・雇用形態：{$empTypes}";
            if ($conditions) $lines[] = "・勤務条件：{$conditions}";
            $lines[] = "\nこの内容で応募しますか？";
        }

        return new TextMessage([
            'type'       => 'text',
            'text'       => implode("\n", $lines),
            'quickReply' => new QuickReply([
                'items' => [
                    new QuickReplyItem([
                        'type'   => 'action',
                        'action' => new MessageAction(['type' => 'message', 'label' => 'はい', 'text' => 'はい']),
                    ]),
                    new QuickReplyItem([
                        'type'   => 'action',
                        'action' => new MessageAction(['type' => 'message', 'label' => '内容を確認したい', 'text' => '内容を確認したい']),
                    ]),
                ],
            ]),
        ]);
    }

    private static function formatUserPreferences(array $conditions): array
    {
        $lines = [];

        if (!empty($conditions['area_ids'])) {
            $names = MasterArea::whereIn('id', $conditions['area_ids'])->pluck('name')->join(' / ');
            if ($names !== '') $lines[] = "・希望勤務地：{$names}";
        }
        if (!empty($conditions['job_type_ids'])) {
            $names = MasterJobType::whereIn('id', $conditions['job_type_ids'])->pluck('name')->join('、');
            if ($names !== '') $lines[] = "・希望職種：{$names}";
        }
        if (!empty($conditions['employment_type_ids'])) {
            $names = MasterEmploymentType::whereIn('id', $conditions['employment_type_ids'])->pluck('name')->join('、');
            if ($names !== '') $lines[] = "・希望雇用形態：{$names}";
        }
        if (!empty($conditions['condition_ids'])) {
            $names = MasterCondition::whereIn('id', $conditions['condition_ids'])->pluck('name')->join(' / ');
            if ($names !== '') $lines[] = "・希望勤務条件：{$names}";
        }
        if (!empty($conditions['appeal_ids'])) {
            $names = MasterAppeal::whereIn('id', $conditions['appeal_ids'])->pluck('name')->join(' / ');
            if ($names !== '') $lines[] = "・重視ポイント：{$names}";
        }

        return $lines;
    }

    public static function askName(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "お名前を教えてください。\n（例：山田 太郎）",
        ]);
    }

    public static function askPhone(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "電話番号を教えてください。\nハイフンなし・数字のみで入力してください。\n（例：09012345678）",
        ]);
    }

    public static function phoneInvalid(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "電話番号の形式が正しくありません。\nハイフンなし・数字10〜11桁で入力してください。\n（例：09012345678）",
        ]);
    }

    public static function completed(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "応募を受け付けました！ありがとうございます。\n\n担当者よりご連絡いたします。\nしばらくお待ちください。",
        ]);
    }

    public static function mismatch(Job $job, array $mismatches, Collection $alternatives): TextMessage
    {
        $jobTypes = $job->jobJobTypes->map(fn($jt) => $jt->jobType?->name)->filter()->join('、');

        $labels = [];
        if (in_array('job_type', $mismatches))        $labels[] = '職種';
        if (in_array('employment_type', $mismatches)) $labels[] = '雇用形態';
        if (in_array('condition', $mismatches))       $labels[] = '勤務条件';

        $text  = "この求人は「{$jobTypes}」の募集となっています。\n\n";
        $text .= 'ご希望の' . implode('・', $labels) . "と異なる可能性があるため、\nこの求人からの応募はご案内が難しいです。\n\n";
        $text .= "ご希望に近い求人をご案内します👇\n";

        $nums = ['①', '②', '③', '④', '⑤'];
        foreach ($alternatives as $i => $alt) {
            $altArea = $alt->jobAreas->first()?->area?->name ?? '沖縄県';
            $altType = $alt->jobJobTypes->first()?->jobType?->name ?? '';
            $text .= "\n{$nums[$i]} {$altArea}｜{$altType}\n" . route('lp.show', $alt->token);
        }

        $text .= "\n\n▼他の求人を見る\n" . url('/');

        return new TextMessage(['type' => 'text', 'text' => $text]);
    }

    public static function expiredToken(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "このリンクは有効期限切れです。\n求人ページから再度「LINEで応募する」をタップしてください。",
        ]);
    }

    public static function noSession(): TextMessage
    {
        return new TextMessage([
            'type' => 'text',
            'text' => "応募は求人ページの「LINEで応募する」ボタンからお手続きください。",
        ]);
    }
}
