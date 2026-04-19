<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'areas'               => ['required', 'array', 'min:1'],
            'areas.*'             => ['integer', 'exists:master_areas,id'],
            'job_types'           => ['required', 'array', 'min:1'],
            'job_types.*'         => ['integer', 'exists:master_job_types,id'],
            'free_text'           => ['nullable', 'string', 'max:2000'],
            'photo'               => ['nullable', 'image', 'max:5120'],
            'employment_types'    => ['required', 'array', 'min:1'],
            'employment_types.*'  => ['integer', 'exists:master_employment_types,id'],
            'conditions'          => ['required', 'array', 'min:1'],
            'conditions.*'        => ['integer', 'exists:master_conditions,id'],
            'appeals'             => ['required', 'array', 'min:1'],
            'appeals.*'           => ['integer', 'exists:master_appeals,id'],
            'contact_email'       => ['required', 'email', 'max:255'],
            'contact_phone'       => ['required', 'regex:/^[0-9]{10,11}$/'],
            'agreement_flag'      => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'areas.required'              => 'エリアを1つ以上選択してください。',
            'areas.min'                   => 'エリアを1つ以上選択してください。',
            'job_types.required'          => '職種を1つ以上選択してください。',
            'job_types.min'               => '職種を1つ以上選択してください。',
            'employment_types.required'   => '雇用形態を1つ以上選択してください。',
            'employment_types.min'        => '雇用形態を1つ以上選択してください。',
            'conditions.required'         => '勤務条件を1つ以上選択してください。',
            'conditions.min'              => '勤務条件を1つ以上選択してください。',
            'appeals.required'            => 'アピールポイントを1つ以上選択してください。',
            'appeals.min'                 => 'アピールポイントを1つ以上選択してください。',
            'contact_email.required'      => '連絡先メールアドレスを入力してください。',
            'contact_email.email'         => '正しいメールアドレス形式で入力してください。',
            'contact_phone.required'      => '電話番号を入力してください。',
            'contact_phone.regex'         => '電話番号はハイフンなしの数字10〜11桁で入力してください。',
            'agreement_flag.required'     => '応募時課金への同意が必要です。',
            'agreement_flag.accepted'     => '応募時課金への同意にチェックを入れてください。',
        ];
    }
}
