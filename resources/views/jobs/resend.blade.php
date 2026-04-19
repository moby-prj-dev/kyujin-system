@extends('layouts.app')
@section('title', 'リンクの再送')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-arrow-repeat me-2"></i>リンクの再送</h1>
        <p>登録時のメールアドレスを入力すると、確認メールまたは編集用リンクを再送します。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-5 py-2">

    @if(session('resent'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-1"></i>メールを送信しました（登録がある場合）。受信トレイをご確認ください。
    </div>
    @endif

    <div class="form-section">
        <h5>メールアドレスを入力</h5>
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

    <div class="text-center mt-2">
        <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>トップページへ戻る</a>
    </div>

</div>
</div>
</div>
@endsection
