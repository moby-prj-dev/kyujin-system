@extends('layouts.app')
@section('title', 'メール確認のお願い')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-envelope me-2"></i>確認メールを送信しました</h1>
        <p>メールアドレスの確認が完了すると求人が公開されます。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-5 py-2">

    <div class="form-section text-center py-4">
        <div class="mb-3" style="font-size:3rem; line-height:1;">📧</div>
        <h4 class="fw-bold mb-3">確認メールを送信しました</h4>
        <p class="text-muted mb-4">
            <strong>{{ $email }}</strong> 宛にメールを送信しました。<br>
            メール内のリンクをクリックすると求人が公開されます。
        </p>
        <div class="alert alert-info text-start">
            <ul class="mb-0 small">
                <li>メールが届かない場合は迷惑メールフォルダをご確認ください</li>
                <li>リンクの有効期限は24時間です</li>
                @if(app()->environment('local'))
                <li>ローカル環境では <a href="http://localhost:8025" target="_blank">Mailpit</a> で確認できます</li>
                @endif
            </ul>
        </div>
        <a href="{{ route('jobs.resend.show') }}" class="btn btn-outline-primary mt-2">
            <i class="bi bi-arrow-repeat me-1"></i>確認メールを再送する
        </a>
    </div>

</div>
</div>
</div>
@endsection
