@extends('admin.layouts.app')
@section('title', '無効申告管理')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-flag me-2"></i>無効申告管理</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="table-light">
                <tr>
                    <th>申告日時</th>
                    <th>事業者</th>
                    <th>応募者</th>
                    <th>申告理由</th>
                    <th class="text-center">ステータス</th>
                    <th>対応</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                <tr>
                    <td class="text-nowrap">{{ $dispute->created_at->format('Y/m/d H:i') }}</td>
                    <td>
                        <div>{{ $dispute->application->job->company_name ?? '—' }}</div>
                        <div class="text-muted small">{{ $dispute->application->job->contact_email ?? '' }}</div>
                    </td>
                    <td>
                        <div>{{ $dispute->application->applicant_name }}</div>
                        <div class="text-muted small">{{ $dispute->application->phone ?? $dispute->application->email }}</div>
                        <div class="text-muted small">応募日：{{ $dispute->application->applied_at?->format('Y/m/d') }}</div>
                    </td>
                    <td style="max-width:250px;">{{ $dispute->reason }}</td>
                    <td class="text-center">
                        @if($dispute->status === 'pending')
                            <span class="badge bg-warning text-dark">審査中</span>
                        @elseif($dispute->status === 'approved')
                            <span class="badge bg-success">承認</span>
                        @else
                            <span class="badge bg-secondary">却下</span>
                        @endif
                    </td>
                    <td>
                        @if($dispute->status === 'pending')
                            <div class="d-flex gap-1 flex-column" style="min-width:180px;">
                                <form action="{{ route('admin.disputes.approve', $dispute) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="text" name="admin_note" class="form-control form-control-sm mb-1" placeholder="メモ（任意）">
                                    <button class="btn btn-success btn-sm w-100">承認（無効化）</button>
                                </form>
                                <form action="{{ route('admin.disputes.reject', $dispute) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="text" name="admin_note" class="form-control form-control-sm mb-1" placeholder="メモ（任意）">
                                    <button class="btn btn-outline-secondary btn-sm w-100">却下</button>
                                </form>
                            </div>
                        @else
                            <div class="text-muted small">
                                {{ $dispute->resolved_at?->format('Y/m/d') }}<br>
                                {{ $dispute->admin_note }}
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">申告はありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $disputes->links() }}</div>

@endsection
