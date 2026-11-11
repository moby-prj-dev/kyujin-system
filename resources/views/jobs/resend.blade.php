@extends('layouts.app')
@section('title', '掲載企業ログイン')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-box-arrow-in-right me-2"></i>掲載企業ログイン</h1>
        <p>求人登録時のメールアドレスを入力すると、管理ページへのログインリンクをメールでお送りします。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-5 py-2">

    @if(session('resent'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-1"></i>ご登録のメールアドレスであれば、ログインリンクをお送りしました。受信トレイ(届かない場合は迷惑メールフォルダ)をご確認ください。
    </div>
    @endif

    <div class="form-section">
        <h5>メールアドレスを入力</h5>
        <p class="text-muted small mb-3">パスワードは不要です。メールに届くリンクから管理ページに入れます。複数の求人を掲載している場合は、すべての求人のリンクを1通でお送りします。</p>
        <form method="POST" action="{{ route('jobs.resend') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">登録時のメールアドレス</label>
                <input type="email" name="contact_email" class="form-control" required placeholder="example@company.com">
            </div>
            <input type="hidden" name="type" value="auto">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope me-1"></i>ログインリンクを送信する
            </button>
        </form>
    </div>

    <p class="text-muted small mt-3 mb-2">登録したメールアドレスが使えなくなった場合は、<a href="{{ route('contact.show') }}">お問い合わせ</a>からご連絡ください。ご本人確認のうえ、連絡先を変更します。</p>

    <div class="text-center mt-2">
        <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>トップページへ戻る</a>
    </div>

</div>
</div>
</div>
@endsection
