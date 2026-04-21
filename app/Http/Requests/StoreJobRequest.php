<?php

namespace App\Http\Requests;

use GuzzleHttp\Client;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mutations = [];

        if ($this->has('contact_email')) {
            $mutations['contact_email'] = strtolower(trim($this->contact_email));
        }

        if ($this->has('contact_phone')) {
            $phone = $this->contact_phone;
            $phone = mb_convert_kana($phone, 'n');          // 全角数字→半角
            $phone = preg_replace('/[^0-9]/', '', $phone);  // 数字以外除去
            $mutations['contact_phone'] = $phone;
        }

        if ($mutations) {
            $this->merge($mutations);
        }
    }

    public function withValidator($validator): void
    {
        $siteKey = config('services.recaptcha.site_key');
        if (!$siteKey) return;

        $validator->after(function ($validator) {
            $token     = $this->input('recaptcha_token');
            $secretKey = config('services.recaptcha.secret_key');

            if (!$token) {
                $validator->errors()->add('recaptcha_token', 'reCAPTCHA認証に失敗しました。再度お試しください。');
                return;
            }

            try {
                $client   = new Client(['timeout' => 5]);
                $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => ['secret' => $secretKey, 'response' => $token],
                ]);
                $result = json_decode($response->getBody()->getContents(), true);

                if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
                    $validator->errors()->add('recaptcha_token', 'ボットの可能性が検出されました。再度お試しください。');
                }
            } catch (\Exception $e) {
                // reCAPTCHA検証失敗時は通過させる（UX優先）
            }
        });
    }

    public function rules(): array
    {
        return [
            'recaptcha_token'     => ['nullable', 'string'],
            'company_name'        => ['required', 'string', 'max:100'],
            'title'               => ['nullable', 'string', 'max:60'],
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
            'salary_type'         => ['required', 'in:monthly,hourly,daily,yearly,other'],
            'salary_min'          => ['required', 'integer', 'min:1'],
            'salary_max'          => ['nullable', 'integer', 'min:1', 'gte:salary_min'],
            'salary_note'         => ['nullable', 'string', 'max:500'],
            'contact_email'       => ['required', 'email', 'max:255'],
            'contact_phone'       => ['required', 'regex:/^[0-9]{10,11}$/'],
            'agreement_flag'      => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required'       => '会社名を入力してください。',
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
            'salary_type.required'        => '給与種別を選択してください。',
            'salary_type.in'              => '給与種別が正しくありません。',
            'salary_min.required'         => '最低給与額を入力してください。',
            'salary_min.integer'          => '最低給与額は正しい数値で入力してください。',
            'salary_min.min'              => '最低給与額は1以上の数値で入力してください。',
            'salary_max.integer'          => '最高給与額は正しい数値で入力してください。',
            'salary_max.gte'              => '最高給与額は最低給与額以上で入力してください。',
            'agreement_flag.required'     => '応募時課金への同意が必要です。',
            'agreement_flag.accepted'     => '応募時課金への同意にチェックを入れてください。',
        ];
    }
}
