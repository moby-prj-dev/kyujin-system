@extends('layouts.app')
@section('title', '管理ページのURLが変わりました')
@section('content')
@push('head')<meta name="robots" content="noindex, nofollow">@endpush

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-shield-lock me-2"></i>管理ページのURLが変わりました</h1>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6 py-2">
    <div class="form-section">
        <p>セキュリティ強化のため、求人の管理ページのURLを変更しました。お手数ですが、新しい管理ページのリンクをメールで受け取ってください。</p>
        <p class="text-muted small">求人登録時のメールアドレスを入力すると、すぐにリンクが届きます。パスワードは不要です。</p>
        <a href="{{ route('jobs.resend.show') }}" class="btn btn-primary w-100">
            <i class="bi bi-envelope me-1"></i>管理ページのリンクを受け取る
        </a>
    </div>
</div>
</div>
</div>
@endsection
