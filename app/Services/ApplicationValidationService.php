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
        return false;
    }

    private function isTestSubmission(Application $application): bool
    {
        $name = mb_strtolower($application->applicant_name ?? '');
        $patterns = ['test', 'テスト', 'てすと', 'あああ', 'aaaa', '0000', 'dummy', 'sample', 'サンプル', 'xxxxxx'];
        foreach ($patterns as $p) {
            if (mb_strpos($name, $p) !== false) return true;
        }
        return false;
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
        return false;
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

        return false;
    }

    public function isBillable(Application $application): bool
    {
        $companyEmail = $application->job->contact_email ?? null;
        if (! $companyEmail) return false;

        // Check if trial period (expires_at of first activated listing) has ended
        $trialEnd = Job::where('contact_email', $companyEmail)
            ->whereNotNull('email_verified_at')
            ->orderBy('email_verified_at')
            ->value('expires_at');

        if ($trialEnd && $application->applied_at && $application->applied_at->greaterThan($trialEnd)) {
            return true;
        }

        // Check if free quota (3 valid apps company-wide) is exhausted
        $priorValidCount = Application::whereHas('job', fn($q) => $q->where('contact_email', $companyEmail))
            ->where('is_valid', true)
            ->where('id', '!=', $application->id)
            ->count();

        return $priorValidCount >= 3;
    }

    private function markInvalid(Application $application, string $reason): void
    {
        $application->is_valid      = false;
        $application->invalid_reason = $reason;
        $application->is_billable   = false;
        $application->counted_at    = null;
    }
}
