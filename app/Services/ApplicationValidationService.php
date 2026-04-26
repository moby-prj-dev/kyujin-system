<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Job;

class ApplicationValidationService
{
    public function validate(Application $application): void
    {
        $application->normalized_email = $this->normalizeEmail($application->email);
        $application->normalized_phone = $this->normalizePhone($application->phone);

        if ($this->isMissingRequiredFields($application)) {
            $this->markInvalid($application, Application::INVALID_MISSING_FIELDS);
            return;
        }

        if ($this->isTestSubmission($application)) {
            $this->markInvalid($application, Application::INVALID_TEST);
            return;
        }

        if ($this->isSpam($application)) {
            $this->markInvalid($application, Application::INVALID_SPAM);
            return;
        }

        if ($this->isDuplicate($application)) {
            $this->markInvalid($application, Application::INVALID_DUPLICATE);
            return;
        }

        $application->is_valid       = true;
        $application->invalid_reason = null;
        $application->counted_at     = $application->counted_at ?? now();
        $billable                    = $this->isBillable($application);
        $application->is_billable    = $billable;
        $application->billable_snapshot = $billable;

        // 怪しい応募フラグ
        [$isSuspicious, $suspiciousReason] = $this->checkSuspicious($application);
        $application->is_suspicious      = $isSuspicious;
        $application->suspicious_reason  = $suspiciousReason;
    }

    public function recalculateBillable(Application $application): void
    {
        if (! $application->is_valid) {
            $application->is_billable = false;
            return;
        }
        $application->is_billable = $this->isBillable($application);
    }

    public function normalizeEmail(?string $email): ?string
    {
        if (empty($email)) return null;
        return strtolower(trim($email));
    }

    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) return null;
        $phone = mb_convert_kana($phone, 'n');
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return empty($phone) ? null : $phone;
    }

    private function isMissingRequiredFields(Application $application): bool
    {
        if (empty($application->applicant_name)) return true;
        if (empty($application->normalized_email) && empty($application->normalized_phone)) return true;

        // 電話番号の形式チェック（日本の携帯・固定電話）
        if ($application->normalized_phone && !$this->isValidJapanesePhone($application->normalized_phone)) {
            return true;
        }

        return false;
    }

    private function isValidJapanesePhone(string $phone): bool
    {
        // 10桁または11桁のみ許可
        $len = strlen($phone);
        if ($len < 10 || $len > 11) return false;

        // 0始まり必須
        if ($phone[0] !== '0') return false;

        // 明らかな連番・繰り返しを弾く（0000000000, 1234567890等）
        if (preg_match('/^(\d)\1{9,}$/', $phone)) return false;
        if ($phone === '01234567890' || $phone === '09876543210') return false;

        // 有効なプレフィックス（携帯・固定・IP電話）
        $validPrefixes = ['070', '080', '090', '050', '03', '06', '011', '022', '025', '052', '076', '078', '082', '092', '098', '0120', '0800'];
        foreach ($validPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) return true;
        }

        // その他の0X系固定電話（市外局番）も許可
        return str_starts_with($phone, '0');
    }

    private function isTestSubmission(Application $application): bool
    {
        $score = 0;
        $name  = mb_strtolower($application->applicant_name ?? '');
        $email = $application->normalized_email ?? '';
        $phone = $application->normalized_phone ?? '';

        // 名前に明らかなテスト文字列（氏名として使われない単語）
        $exactTestNames = ['test', 'テスト', 'てすと', 'dummy', 'sample', 'サンプル', 'ダミー'];
        foreach ($exactTestNames as $p) {
            if ($name === mb_strtolower($p)) {
                $score += 3; // 完全一致は即アウト
                break;
            }
        }

        // 繰り返し文字（あああ、aaaa、0000など）
        if (preg_match('/^(.)\1{3,}$/u', $name)) $score += 3;

        // 名前が連続文字列（abcde、12345など）
        if (preg_match('/^[a-z]{1,2}$/', $name)) $score += 2;

        // メールもテスト系
        if (preg_match('/test|dummy|sample|noreply/', $email)) $score += 2;

        // 電話が連番・繰り返し
        if ($phone && preg_match('/^(\d)\1{9,}$/', $phone)) $score += 2;

        // 名前にテスト語を含む（ただし苗字の可能性も考慮して加点低め）
        $softPatterns = ['てすと', 'xxxxxx', 'あああ', 'aaaa', '0000'];
        foreach ($softPatterns as $p) {
            if (mb_strpos($name, $p) !== false) {
                $score += 2;
                break;
            }
        }

        return $score >= 3;
    }

    private function isSpam(Application $application): bool
    {
        $fields = strtolower(implode(' ', [
            $application->applicant_name ?? '',
            $application->normalized_email ?? '',
        ]));
        foreach (['spam', 'bot@', 'noreply', 'robot'] as $p) {
            if (str_contains($fields, $p)) return true;
        }

        // メールのMXレコードチェック
        if ($application->normalized_email) {
            $domain = substr(strrchr($application->normalized_email, '@'), 1);
            if ($domain && !$this->hasMxRecord($domain)) {
                return true;
            }
        }

        return false;
    }

    private function hasMxRecord(string $domain): bool
    {
        try {
            return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
        } catch (\Throwable) {
            return true; // DNS解決できない場合は通過させる
        }
    }

    private function isDuplicate(Application $application): bool
    {
        $base = Application::where('job_id', $application->job_id)
            ->where('is_valid', true);

        if ($application->id) {
            $base->where('id', '!=', $application->id);
        }

        if ($application->normalized_email) {
            if ((clone $base)->where('normalized_email', $application->normalized_email)->exists()) {
                return true;
            }
        }

        if ($application->normalized_phone) {
            if ((clone $base)->where('normalized_phone', $application->normalized_phone)->exists()) {
                return true;
            }
        }

        // LINE応募：line_user_idで重複チェック
        if ($application->application_type === Application::TYPE_LINE) {
            $lineUserId = $application->lineDetail?->line_user_id
                ?? optional($application->relationLoaded('lineDetail') ? $application->lineDetail : null)?->line_user_id;

            if (!$lineUserId && $application->id) {
                $lineUserId = \App\Models\LineApplicationDetail::where('application_id', $application->id)->value('line_user_id');
            }

            if ($lineUserId) {
                $exists = (clone $base)
                    ->whereHas('lineDetail', fn($q) => $q->where('line_user_id', $lineUserId))
                    ->exists();
                if ($exists) return true;
            }
        }

        return false;
    }

    public function isBillable(Application $application): bool
    {
        $companyEmail = $application->job->contact_email ?? null;
        if (! $companyEmail) return false;

        // 永久無料フラグ（会社単位）
        $isPermanentlyFree = Job::where('contact_email', $companyEmail)
            ->whereNotNull('email_verified_at')
            ->where('is_permanently_free', true)
            ->exists();
        if ($isPermanentlyFree) return false;

        // monitor_ends_at が設定されていれば、解除後も期限まで判定
        $monitorEnd = Job::where('contact_email', $companyEmail)
            ->whereNotNull('email_verified_at')
            ->whereNotNull('monitor_ends_at')
            ->orderBy('email_verified_at')
            ->value('monitor_ends_at');

        if ($monitorEnd && $application->applied_at && $application->applied_at->lessThanOrEqualTo($monitorEnd)) {
            // モニター期間内：有効応募3件未満なら無料
            $priorValidCount = Application::whereHas('job', fn($q) => $q->where('contact_email', $companyEmail))
                ->where('is_valid', true)
                ->where('id', '!=', $application->id)
                ->count();

            if ($priorValidCount < 3) {
                return false;
            }
        }

        return true;
    }

    private function checkSuspicious(Application $application): array
    {
        $reasons = [];

        // 同一IPから短時間に複数応募（フォーム応募のみ）
        if ($application->application_type === Application::TYPE_FORM) {
            $ipAddress = $application->formDetail?->ip_address;
            if ($ipAddress) {
                $recentCount = Application::whereHas('formDetail', fn($q) => $q->where('ip_address', $ipAddress))
                    ->where('id', '!=', ($application->id ?? 0))
                    ->where('applied_at', '>=', now()->subHours(1))
                    ->count();
                if ($recentCount >= 3) {
                    $reasons[] = '同一IPから1時間以内に' . ($recentCount + 1) . '件の応募';
                }
            }

            // 明らかなボット系UA
            $ua = strtolower($application->formDetail?->user_agent ?? '');
            if ($ua && preg_match('/curl|python|scrapy|bot|crawler|spider|headless/i', $ua)) {
                $reasons[] = '疑わしいUserAgent: ' . substr($ua, 0, 80);
            }
        }

        if (empty($reasons)) {
            return [false, null];
        }

        return [true, implode(' / ', $reasons)];
    }

    private function markInvalid(Application $application, string $reason): void
    {
        $application->is_valid      = false;
        $application->invalid_reason = $reason;
        $application->is_billable   = false;
        $application->counted_at    = null;
    }
}
