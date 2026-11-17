<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /** 「当サイトを何で知りましたか」(任意)の選択肢。チラシ・フォーム営業などの効果測定用 */
    public const REFERRALS = [
        'Google・Yahoo!などの検索',
        '自社の求人がケアエントリーに載っているのを見て',
        '郵送のチラシ・資料',
        'ケアエントリーからのご案内メール',
        'SNS（Instagram・Facebookなど）',
        '知人・取引先の紹介',
        'その他',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:200'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
            'referral' => ['nullable', 'string', \Illuminate\Validation\Rule::in(self::REFERRALS)],
            // ハニーポット（ボット対策・空欄でなければ拒否）
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'お名前を入力してください。',
            'email.required'   => 'メールアドレスを入力してください。',
            'email.email'      => '正しいメールアドレスを入力してください。',
            'subject.required' => 'お問い合わせ件名を入力してください。',
            'message.required' => 'お問い合わせ内容を入力してください。',
            'message.max'      => 'お問い合わせ内容は2000文字以内で入力してください。',
        ];
    }
}
