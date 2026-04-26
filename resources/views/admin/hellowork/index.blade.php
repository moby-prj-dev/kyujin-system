@extends('admin.layouts.app')
@section('title', 'ハローワーク求人管理')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-building me-2"></i>ハローワーク求人管理</h1>
    <span class="badge bg-secondary">{{ $jobs->count() }}件</span>
</div>

@if(session('success'))
    <div class="alert alert-success mb-3">
        <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        @forelse($jobs as $job)
            <div class="d-flex align-items-start justify-content-between p-3 border-bottom">
                <div class="flex-grow-1 me-3">
                    <div class="fw-bold mb-1">{{ $job->title }}</div>
                    <div class="text-muted small mb-1">{{ $job->company_name }}</div>
                    <div class="d-flex gap-3 small text-muted">
                        <span><i class="bi bi-hash"></i> {{ $job->hw_job_no }}</span>
                        @if($job->expires_at)
                            <span class="{{ $job->expires_at->isPast() ? 'text-danger' : '' }}">
                                <i class="bi bi-calendar-x"></i> 期限: {{ $job->expires_at->format('Y/m/d') }}
                            </span>
                        @endif
                        <span><i class="bi bi-calendar-check"></i> 作成: {{ $job->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('lp.show', $job->token) }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i> LP
                    </a>
                    <a href="{{ route('admin.hellowork.edit', $job) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> 編集
                    </a>
                    <form method="POST" action="{{ route('admin.hellowork.destroy', $job) }}"
                          onsubmit="return confirm('削除しますか？')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                ハローワーク求人はまだありません。<br>
                <code class="small">php artisan hellowork:generate-lps</code> を実行して生成してください。
            </div>
        @endforelse
    </div>
</div>

@endsection
