<?php

namespace App\Services;

use App\Mail\BillingSummaryMail;
use App\Models\Application;
use App\Models\BillingSummary;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillingService
{
    /**
     * 前月の有効応募を集計し請求メールを送信する（毎月1日実行）
     */
    public function generateAndSendMonthly(?string $month = null): array
    {
        $month = $month ?? now()->subMonth()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $companies = Application::query()
            ->join('job_listings as j', 'applications.job_id', '=', 'j.id')
            ->whereYear('applications.counted_at', $year)
            ->whereMonth('applications.counted_at', $mon)
            ->where('applications.is_valid', true)
            ->whereNull('j.deleted_at')
            ->where('j.is_monitor', false)
            ->select([
                'j.contact_email',
                DB::raw('MAX(j.company_name) as company_name'),
                DB::raw('COUNT(*) as valid_count'),
                DB::raw('SUM(CASE WHEN applications.is_billable = 1 THEN 1 ELSE 0 END) as billable_count'),
            ])
            ->groupBy('j.contact_email')
            ->get();

        $results = [];

        foreach ($companies as $company) {
            $summary = BillingSummary::updateOrCreate(
                ['contact_email' => $company->contact_email, 'billing_month' => $month],
                [
                    'valid_count'    => $company->valid_count,
                    'billable_count' => $company->billable_count,
                    'unit_price'     => 3000,
                    'total_amount'   => $company->billable_count * 3000,
                ]
            );

            if ($company->billable_count > 0 && $summary->status === BillingSummary::STATUS_UNBILLED) {
                $results[] = $this->sendSummaryMail($summary, $company->company_name);
            }
        }

        return $results;
    }

    /**
     * 個別の請求サマリーのメール送信
     */
    public function sendForSummary(BillingSummary $summary): void
    {
        $companyName = Job::where('contact_email', $summary->contact_email)->value('company_name') ?? '';
        $this->sendSummaryMail($summary, $companyName);
    }

    private function sendSummaryMail(BillingSummary $summary, string $companyName): array
    {
        try {
            Mail::to($summary->contact_email)->send(new BillingSummaryMail($summary, $companyName));
            $summary->update(['status' => BillingSummary::STATUS_SENT, 'sent_at' => now()]);
            return ['email' => $summary->contact_email, 'status' => 'sent'];
        } catch (\Exception $e) {
            Log::error("請求メール送信失敗 [{$summary->contact_email}]: " . $e->getMessage());
            return ['email' => $summary->contact_email, 'status' => 'failed', 'error' => $e->getMessage()];
        }
    }
}
