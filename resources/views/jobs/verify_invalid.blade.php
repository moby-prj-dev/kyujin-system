@extends('layouts.app')
@section('title', 'このリンクは使用済みです')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-link-45deg me-2"></i>このリンクは使用済み、または無効です</h1>
        <p>メールアドレスの確認がすでに完了している可能性があります。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6 py-2">

    <div class="form-section text-center py-4">
        <p class="mb-3">
            確認用リンクは<strong>1回だけ</strong>使えます。すでにクリックされている場合、求人は公開済みです。<br>
            求人の編集・停止は「編集用リンク」から行えます。
        </p>
        <div class="alert alert-info text-start small">
            <ul class="mb-0">
                <li>編集用リンクは、確認完了時に登録メールアドレスへお送りしています（迷惑メールフォルダもご確認ください）</li>
                <li>見つからない場合は、下のボタンから登録メールアドレスを入力すると再送できます</li>
            </ul>
        </div>
        <a href="{{ route('jobs.resend.show') }}" class="btn btn-primary mt-2">
            <i class="bi bi-envelope me-1"></i>編集用リンクを再送する
        </a>
    </div>

</div>
</div>
</div>
@endsection
