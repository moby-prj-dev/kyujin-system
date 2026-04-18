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
            'area_id'             => ['required', 'integer', 'exists:master_areas,id'],
            'job_type_id'         => ['required', 'integer', 'exists:master_job_types,id'],
            'employment_type_id'  => ['required', 'integer', 'exists:master_employment_types,id'],
            'conditions'          => ['required', 'array', 'min:1'],
            'conditions.*'        => ['integer', 'exists:master_conditions,id'],
            'appeals'             => ['required', 'array', 'min:1'],
            'appeals.*'           => ['integer', 'exists:master_appeals,id'],
            'contact_email'       => ['required', 'email', 'max:255'],
            'contact_phone'       => ['required', 'string', 'max:20'],
            'agreement_flag'      => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'area_id.required'            => 'エリアを選択してください。',
            'job_type_id.required'        => '職種を選択してください。',
            'employment_type_id.required' => '雇用形態を選択してください。',
            'conditions.required'         => '勤務条件を1つ以上選択してください。',
            'conditions.min'              => '勤務条件を1つ以上選択してください。',
            'appeals.required'            => 'アピールポイントを1つ以上選択してください。',
            'appeals.min'                 => 'アピールポイントを1つ以上選択してください。',
            'contact_email.required'      => '連絡先メールアドレスを入力してください。',
            'contact_email.email'         => '正しいメールアドレス形式で入力してください。',
            'contact_phone.required'      => '電話番号を入力してください。',
            'agreement_flag.required'     => '応募時課金への同意が必要です。',
            'agreement_flag.accepted'     => '応募時課金への同意にチェックを入れてください。',
        ];
    }
}
