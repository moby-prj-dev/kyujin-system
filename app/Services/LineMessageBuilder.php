<?php

namespace App\Services;

use App\Models\Job;
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

    public static function confirmation(Job $job): TextMessage
    {
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
