@extends('admin.layouts.app')
@section('title', '応募一覧')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-people me-2"></i>応募一覧</h1>
</div>

{{-- 求人絞り込み中バナー --}}
@if($filterJob)
<div class="alert alert-info d-flex align-items-center justify-content-between py-2 mb-3">
    <span>
        <i class="bi bi-funnel-fill me-1"></i>
        <strong>{{ $filterJob->company_name }}</strong> ／ {{ $filterJob->title ?: '（タイトル未設定）' }} の応募のみ表示中
    </span>
    <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-secondary ms-3">すべて表示</a>
</div>
@endif

{{-- フィルター --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            @if($filterJob)
                <input type="hidden" name="job_id" value="{{ $filterJob->id }}">
            @endif
            <div class="col-md-3">
                <label class="form-label small fw-bold mb-1">メールアドレス</label>
                <input type="text" name="email" class="form-control form-control-sm"
                       value="{{ request('email') }}" placeholder="example@company.com">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">有効/無効</label>
                <select name="validity" class="form-select form-select-sm">
                    <option value="">すべて</option>
                    <option value="valid"   {{ request('validity') === 'valid'   ? 'selected' : '' }}>有効のみ</option>
                    <option value="invalid" {{ request('validity') === 'invalid' ? 'selected' : '' }}>無効のみ</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">請求対象</label>
                <select name="billable" class="form-select form-select-sm">
                    <option value="">すべて</option>
                    <option value="1" {{ request('billable') === '1' ? 'selected' : '' }}>請求対象のみ</option>
                    <option value="0" {{ request('billable') === '0' ? 'selected' : '' }}>非対象のみ</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">絞り込む</button>
                <a href="{{ route('admin.applications.index', $filterJob ? ['job_id' => $filterJob->id] : []) }}"
                   class="btn btn-outline-secondary btn-sm ms-1">リセット</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header px-4 py-3 d-flex flex-wrap align-items-center gap-3">
        <span>応募一覧</span>
        <span class="text-muted fw-normal" style="font-size:0.82rem;">計 {{ (int)$summaryCounts->total }}件</span>
        <span class="ms-auto d-flex flex-wrap gap-3" style="font-size:0.82rem;">
            <span>
                <span class="badge" style="background:#e6f4ea; color:#137333;">有効</span>
                <span class="fw-bold ms-1">{{ (int)$summaryCounts->valid_count }}</span>件
            </span>
            <span>
                <span class="badge" style="background:#fce8e6; color:#c62828;">無効</span>
                <span class="fw-bold ms-1">{{ (int)$summaryCounts->invalid_count }}</span>件
            </span>
            <span>
                <span class="badge bg-danger">課金対象</span>
                <span class="fw-bold ms-1 {{ (int)$summaryCounts->billable_count > 0 ? 'text-danger' : '' }}">{{ (int)$summaryCounts->billable_count }}</span>件
            </span>
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>応募日時</th>
                    <th>求人名</th>
                    <th>応募者名</th>
                    <th>メール</th>
                    <th>電話番号</th>
                    <th class="text-center">有効/無効</th>
                    <th>無効理由</th>
                    <th class="text-center">請求対象</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td class="text-nowrap" style="font-size:0.82rem;">
                        {{ $app->applied_at?->format('Y/m/d H:i') ?? '—' }}
                    </td>
                    <td style="font-size:0.82rem; max-width:180px;">
                        @if($app->job)
                        <a href="{{ route('admin.applications.index', ['job_id' => $app->job->id]) }}"
                           class="text-decoration-none text-reset d-block text-truncate"
                           title="{{ $app->job->company_name }}">
                            {{ $app->job->company_name }}
                        </a>
                        <span class="text-muted d-block text-truncate" style="font-size:0.78rem;"
                              title="{{ $app->job->title }}">{{ $app->job->title ?: '—' }}</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $app->applicant_name }}</td>
                    <td style="font-size:0.82rem; max-width:160px;">
                        <span class="d-block text-truncate">{{ $app->email ?: '—' }}</span>
                    </td>
                    <td style="font-size:0.82rem;">{{ $app->phone ?: '—' }}</td>
                    <td class="text-center">
                        @if($app->is_valid)
                            <span class="badge" style="background:#e6f4ea; color:#137333;">有効</span>
                        @else
                            <span class="badge" style="background:#fce8e6; color:#c62828;">無効</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem; color:#888;">
                        {{ $app->is_valid ? '—' : $app->invalidReasonLabel() }}
                    </td>
                    <td class="text-center">
                        @if($app->is_billable)
                            <span class="badge bg-danger">対象</span>
                        @else
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.applications.update', $app) }}" class="d-inline">
                            @csrf @method('PATCH')
                            @if($app->is_valid)
                                <input type="hidden" name="action" value="invalidate">
                                <button type="submit" class="btn btn-xs btn-outline-danger"
                                        onclick="return confirm('この応募を無効にしますか？')">無効化</button>
                            @else
                                <input type="hidden" name="action" value="validate">
                                <button type="submit" class="btn btn-xs btn-outline-success"
                                        onclick="return confirm('この応募を有効にしますか？')">有効化</button>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">応募データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="card-footer d-flex justify-content-center py-3">
        {{ $applications->links() }}
    </div>
    @endif
</div>

@endsection
