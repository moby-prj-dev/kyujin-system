@extends('admin.layouts.app')
@section('title', '請求管理')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-receipt me-2"></i>請求管理</h1>

    {{-- 月次集計生成 --}}
    <form method="POST" action="{{ route('admin.billings.generate') }}" class="d-flex gap-2 align-items-center">
        @csrf
        <input type="month" name="month" class="form-control form-control-sm"
               value="{{ now()->subMonth()->format('Y-m') }}" style="width:160px;">
        <button type="submit" class="btn btn-primary btn-sm"
                onclick="return confirm('選択した月の請求集計を実行してメール送信しますか？')">
            <i class="bi bi-calculator me-1"></i>集計・送信
        </button>
    </form>
</div>

{{-- フィルター --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">請求月</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">すべて</option>
                    @foreach($months as $m)
                    <option value="{{ $m }}" {{ request('month') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">ステータス</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">すべて</option>
                    <option value="unbilled" {{ request('status') === 'unbilled' ? 'selected' : '' }}>未請求</option>
                    <option value="sent"     {{ request('status') === 'sent'     ? 'selected' : '' }}>送付済</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>入金済</option>
                    <option value="on_hold"  {{ request('status') === 'on_hold'  ? 'selected' : '' }}>保留</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">絞り込む</button>
                <a href="{{ route('admin.billings.index') }}" class="btn btn-outline-secondary btn-sm ms-1">リセット</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header px-4 py-3">
        請求一覧
        <span class="text-muted fw-normal ms-2" style="font-size:0.82rem;">{{ $summaries->total() }}件</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>請求月</th>
                    <th>会社名</th>
                    <th>メール</th>
                    <th class="text-center">有効応募</th>
                    <th class="text-center">請求対象</th>
                    <th class="text-end">単価</th>
                    <th class="text-end">合計金額</th>
                    <th class="text-center">ステータス</th>
                    <th>送信日時</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaries as $s)
                <tr>
                    <td class="fw-bold text-nowrap">{{ $s->billing_month }}</td>
                    <td style="font-size:0.88rem;">{{ $companyNames[$s->contact_email] ?? '—' }}</td>
                    <td style="font-size:0.8rem; color:#888; max-width:160px;">
                        <span class="d-block text-truncate">{{ $s->contact_email }}</span>
                    </td>
                    <td class="text-center">{{ $s->valid_count }}</td>
                    <td class="text-center fw-bold {{ $s->billable_count > 0 ? 'text-danger' : '' }}">
                        {{ $s->billable_count }}
                    </td>
                    <td class="text-end">¥{{ number_format($s->unit_price) }}</td>
                    <td class="text-end fw-bold {{ $s->total_amount > 0 ? 'text-danger' : '' }}">
                        ¥{{ number_format($s->total_amount) }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $s->statusBadge() }}">{{ $s->statusLabel() }}</span>
                    </td>
                    <td style="font-size:0.8rem; color:#888;">
                        {{ $s->sent_at?->format('Y/m/d H:i') ?? '—' }}
                    </td>
                    <td class="text-center" style="white-space:nowrap;">
                        {{-- ステータス変更 --}}
                        <form method="POST" action="{{ route('admin.billings.status', $s) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <select name="status" class="form-select form-select-sm d-inline-block"
                                    style="width:90px; font-size:0.78rem;"
                                    onchange="this.form.submit()">
                                @foreach(\App\Models\BillingSummary::STATUS_LABELS as $val => $label)
                                <option value="{{ $val }}" {{ $s->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                        {{-- 再送 --}}
                        @if(in_array($s->status, ['unbilled','on_hold']))
                        <form method="POST" action="{{ route('admin.billings.send', $s) }}" class="d-inline ms-1">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-primary"
                                    onclick="return confirm('請求メールを送信しますか？')">
                                <i class="bi bi-envelope"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">請求データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($summaries->hasPages())
    <div class="card-footer d-flex justify-content-center py-3">
        {{ $summaries->links() }}
    </div>
    @endif
</div>

@endsection
