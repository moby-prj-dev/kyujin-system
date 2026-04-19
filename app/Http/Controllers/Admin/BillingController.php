<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingSummary;
use App\Models\Job;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = BillingSummary::orderByDesc('billing_month')->orderBy('contact_email');

        if ($request->filled('month')) {
            $query->where('billing_month', $request->month);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $summaries = $query->paginate(50)->withQueryString();

        // 会社名を付与
        $emails = $summaries->pluck('contact_email')->unique();
        $companyNames = Job::whereIn('contact_email', $emails)
            ->select('contact_email', 'company_name')
            ->get()
            ->groupBy('contact_email')
            ->map(fn($jobs) => $jobs->first()->company_name);

        $months = BillingSummary::selectRaw('DISTINCT billing_month')
            ->orderByDesc('billing_month')->pluck('billing_month');

        return view('admin.billings.index', compact('summaries', 'companyNames', 'months'));
    }

    public function updateStatus(Request $request, BillingSummary $billing)
    {
        $request->validate(['status' => ['required', 'in:unbilled,sent,paid,on_hold']]);
        $billing->update(['status' => $request->status]);
        return back()->with('success', 'ステータスを更新しました。');
    }

    public function send(BillingSummary $billing, BillingService $billingService)
    {
        $billingService->sendForSummary($billing);
        return back()->with('success', '請求メールを送信しました。');
    }

    public function generate(Request $request, BillingService $billingService)
    {
        $month   = $request->input('month', now()->subMonth()->format('Y-m'));
        $results = $billingService->generateAndSendMonthly($month);
        $sent    = collect($results)->where('status', 'sent')->count();
        return back()->with('success', "{$month} の請求集計が完了しました（{$sent}件送信）。");
    }
}
