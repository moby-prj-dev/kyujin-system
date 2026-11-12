@extends('layouts.app')
@section('title', 'お問い合わせ')
@section('content')

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-envelope me-2"></i>お問い合わせ</h1>
        <p>サービスに関するご質問・ご相談はこちらからどうぞ。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8 py-2">

    {{-- お問い合わせフォーム不具合のお詫び(2026-12-05まで表示) --}}
    @if(now()->lt(\Carbon\Carbon::parse('2026-12-06 00:00:00')))
    <div id="notice-contact-form" class="alert alert-warning mb-3" style="font-size:0.88rem;line-height:1.8;">
        <strong><i class="bi bi-exclamation-triangle me-1"></i>【お詫び】お問い合わせフォームの不具合について</strong><br>
        2026年5月〜10月30日の間、お問い合わせフォームから送信いただいた内容が、システムの不具合により当方に届いておりませんでした。ご迷惑をおかけし、誠に申し訳ございません。<br>
        お手数ですが、再度お問い合わせいただくか、<a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a> までご連絡ください。（現在は復旧しております）
    </div>
    @endif

    <div class="form-section">
        <p class="mb-3" style="font-size:0.93rem; line-height:1.9;">
            サービスに関するご質問・ご相談は、以下のフォームよりお気軽にお問い合わせください。<br>
            平日10:00〜18:00の間に担当者よりご連絡いたします。
        </p>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">お名前 <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required maxlength="100">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required maxlength="255">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="company" class="form-label">会社名・施設名 <small class="text-muted fw-normal">（任意）</small></label>
                <input type="text" id="company" name="company"
                       class="form-control @error('company') is-invalid @enderror"
                       value="{{ old('company') }}" maxlength="200">
                @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="subject" class="form-label">お問い合わせ件名 <span class="text-danger">*</span></label>
                <input type="text" id="subject" name="subject"
                       class="form-control @error('subject') is-invalid @enderror"
                       value="{{ old('subject') }}" required maxlength="200">
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">お問い合わせ内容 <span class="text-danger">*</span></label>
                <textarea id="message" name="message" rows="8"
                          class="form-control @error('message') is-invalid @enderror"
                          required maxlength="2000">{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ハニーポット（ボット対策・人間には見えない） --}}
            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>
            {{-- 表示した時刻(暗号化)。ページを開かずに直接送るボットや、数秒で送るボットを見分ける --}}
            <input type="hidden" name="_ts" value="{{ encrypt(now()->timestamp) }}">

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-send me-1"></i>送信する
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-3">
        <p class="text-muted small mb-2">
            ※ お電話でのお問い合わせをご希望の方は、<a href="{{ route('company') }}">運営者情報ページ</a>をご確認ください。
        </p>
        <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>トップページへ戻る</a>
    </div>

</div>
</div>
</div>
@endsection
