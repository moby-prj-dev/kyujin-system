@extends('admin.layouts.app')
@section('title', '企業・求人一覧')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-building me-2"></i>企業・求人一覧</h1>
</div>

<div class="card">
    <div class="card-header px-4 py-3">
        企業一覧（メールアドレス単位）
        <span class="text-muted fw-normal ms-2" style="font-size:0.82rem;">{{ count($companies) }}社</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>会社名</th>
                    <th>メール</th>
                    <th>掲載開始</th>
                    <th>トライアル状態</th>
                    <th>トライアル終了</th>
                    <th class="text-center">有効応募</th>
                    <th class="text-center">無効応募</th>
                    <th class="text-center">請求対象</th>
                    <th class="text-end">請求額</th>
                    <th class="text-center">求人数</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $c)
                @php
                    $statusLabels = [
                        'active'       => ['label' => '無料期間内', 'class' => 'trial-active'],
                        'ending_soon'  => ['label' => '終了まで7日以内', 'class' => 'trial-ending-soon'],
                        'ended'        => ['label' => '無料期間終了', 'class' => 'trial-ended'],
                        'billing'      => ['label' => '請求対象あり', 'class' => 'trial-billing'],
                    ];
                    $st = $statusLabels[$c->trial_status] ?? $statusLabels['active'];
                    $billingAmount = ($c->billable_count ?? 0) * 3000;
                @endphp
                <tr>
                    <td class="fw-bold">{{ $c->company_name }}</td>
                    <td class="text-muted" style="font-size:0.82rem;">{{ $c->contact_email }}</td>
                    <td class="text-nowrap" style="font-size:0.82rem;">
                        {{ $c->first_activated_at ? \Carbon\Carbon::parse($c->first_activated_at)->format('Y/m/d') : '—' }}
                    </td>
                    <td>
                        <span class="badge {{ $st['class'] }}" style="font-size:0.78rem; padding:4px 8px;">
                            {{ $st['label'] }}
                        </span>
                    </td>
                    <td class="text-nowrap" style="font-size:0.82rem;">
                        {{ $c->trial_ends_at ? \Carbon\Carbon::parse($c->trial_ends_at)->format('Y/m/d') : '—' }}
                    </td>
                    <td class="text-center fw-bold text-success">{{ $c->valid_count }}</td>
                    <td class="text-center text-danger">{{ $c->invalid_count }}</td>
                    <td class="text-center fw-bold {{ $c->billable_count > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $c->billable_count }}
                    </td>
                    <td class="text-end fw-bold {{ $billingAmount > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $billingAmount > 0 ? '¥' . number_format($billingAmount) : '—' }}
                    </td>
                    <td class="text-center text-muted">{{ $c->listing_count }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
