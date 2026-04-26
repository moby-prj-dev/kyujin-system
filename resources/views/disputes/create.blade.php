@extends('layouts.app')
@section('title', '無効申告')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-flag me-2"></i>無効申告</h1>
        <p>有効と判定された応募について、無効申告を行えます。</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                今月の申告残数：<strong>{{ ApplicationDispute::MONTHLY_LIMIT - $monthlyCount }}件</strong>（月{{ ApplicationDispute::MONTHLY_LIMIT }}件まで）
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <p class="text-muted small mb-1">申告対象の応募</p>
                    <p class="fw-bold mb-1">{{ $application->applicant_name }}</p>
                    <p class="text-muted small mb-0">
                        応募日時：{{ $application->applied_at?->format('Y/m/d H:i') }}<br>
                        @if($application->phone)電話：{{ $application->phone }}<br>@endif
                        @if($application->email)メール：{{ $application->email }}@endif
                    </p>
                </div>
            </div>

            <form action="{{ route('disputes.store', [$token, $application]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold">申告理由 <span class="text-danger">*</span></label>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason_preset" value="冷やかし・いたずら応募のため" id="r1">
                            <label class="form-check-label" for="r1">冷やかし・いたずら応募のため</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason_preset" value="連絡が取れない・音信不通のため" id="r2">
                            <label class="form-check-label" for="r2">連絡が取れない・音信不通のため</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason_preset" value="既に紹介済みの人物による応募のため" id="r3">
                            <label class="form-check-label" for="r3">既に紹介済みの人物による応募のため</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason_preset" value="その他" id="r4">
                            <label class="form-check-label" for="r4">その他（下記に詳細を記入）</label>
                        </div>
                    </div>
                    <textarea class="form-control @error('reason') is-invalid @enderror"
                        name="reason" id="reason" rows="4"
                        placeholder="申告理由の詳細を記入してください">{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted">申告内容は当社が確認のうえ判断します。虚偽の申告が確認された場合、以降の申告受付を停止することがあります。</div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('jobs.manage', $token) }}" class="btn btn-outline-secondary">戻る</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-flag me-1"></i>無効申告を送信する
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="reason_preset"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const textarea = document.getElementById('reason');
        if (radio.value !== 'その他') {
            textarea.value = radio.value;
        } else {
            textarea.value = '';
            textarea.focus();
        }
    });
});
</script>
@endsection
