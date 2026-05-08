<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
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
