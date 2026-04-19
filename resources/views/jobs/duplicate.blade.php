@extends('layouts.app')
@section('title', '登録情報の確認')
@section('content')

@php
    $pattern = session('duplicate_pattern');
    $email   = session('duplicate_email');
@endphp

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-shield-exclamation me-2"></i>登録情報の確認</h1>
        <p>入力された情報が既存の登録情報と一致しました。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6 py-2">

    {{-- パターンA --}}
    @if($pattern === 'both')
    <div class="alert alert-warning d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-bold mb-1">このメールアドレスと電話番号はすでに登録済みです</div>
            <p class="mb-0 small">同一企業による重複登録を防ぐため、新規登録はできません。既存の企業情報としてお手続きください。</p>
        </div>
    </div>

    {{-- パターンB --}}
    @elseif($pattern === 'email')
    <div class="alert alert-warning d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-bold mb-1">このメールアドレスはすでに登録されています</div>
            <p class="mb-0 small">同じ企業による重複登録を防ぐため、新規登録はできません。続きから操作する場合は、確認メールまたは編集用リンクを再送してください。</p>
        </div>
    </div>

    {{-- パターンC --}}
    @elseif($pattern === 'phone')
    <div class="alert alert-danger d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-telephone-fill fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-bold mb-1">この電話番号はすでに登録済みの企業情報と一致しています</div>
            <p class="mb-0 small">重複登録防止のため、新規登録を停止しました。既存の企業情報をご利用の場合は、確認メールまたは編集用リンクの再送をご利用ください。</p>
        </div>
    </div>
    @endif

    {{-- パターンA・B：メールわかっている --}}
    @if(in_array($pattern, ['both', 'email']))

        @if(session('resent'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>メールを再送しました。受信トレイをご確認ください。</div>
        @endif

        <div class="form-section">
            <h5>続きから操作する</h5>
            <form method="POST" action="{{ route('jobs.resend') }}" class="mb-3">
                @csrf
                <input type="hidden" name="contact_email" value="{{ $email }}">
                <input type="hidden" name="type" value="verification">
                <button type="submit" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-envelope me-1"></i>確認メールを再送する
                </button>
            </form>
            <form method="POST" action="{{ route('jobs.resend') }}">
                @csrf
                <input type="hidden" name="contact_email" value="{{ $email }}">
                <input type="hidden" name="type" value="manage">
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-pencil-square me-1"></i>編集用リンクを再送する
                </button>
            </form>
        </div>

    {{-- パターンC：メール不明 --}}
    @elseif($pattern === 'phone')

        @if(session('resent'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>ご登録のメールアドレスに再送しました（登録がある場合）。</div>
        @endif

        <div class="form-section">
            <h5>登録済みメールアドレスで続きから操作する</h5>
            <form method="POST" action="{{ route('jobs.resend') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">登録時のメールアドレス</label>
                    <input type="email" name="contact_email" class="form-control" required placeholder="example@company.com">
                </div>
                <input type="hidden" name="type" value="auto">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-envelope me-1"></i>リンクを送信する
                </button>
            </form>
        </div>
    @endif

    <div class="text-center mt-3">
        <a href="{{ route('jobs.create') }}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>登録フォームに戻る
        </a>
    </div>

</div>
</div>
</div>
@endsection
