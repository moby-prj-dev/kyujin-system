<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募フォーム｜{{ $job->seo_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body{background-color:#f5f7fa;color:#333;font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;}
        .apply-wrap{max-width:640px;margin:0 auto;}
        .apply-header{background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);color:#fff;padding:1.5rem 1.5rem;}
        .apply-header h1{font-size:1.2rem;font-weight:800;margin:0;}
        .apply-header .job-name{font-size:0.85rem;opacity:0.85;margin-top:4px;}
        .apply-body{padding:1.5rem 1rem 3rem;}
        .form-section{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.07);padding:1.5rem;margin-bottom:1rem;}
        .form-label{font-weight:700;font-size:0.9rem;}
        .required-badge{font-size:0.72rem;background:#dc3545;color:#fff;border-radius:3px;padding:1px 5px;margin-left:6px;vertical-align:middle;}
        .optional-badge{font-size:0.72rem;background:#aaa;color:#fff;border-radius:3px;padding:1px 5px;margin-left:6px;vertical-align:middle;}
        .form-control{border:1.5px solid #ccc;border-radius:8px;font-size:0.95rem;}
        .form-control:focus{border-color:#1a73e8;box-shadow:0 0 0 3px rgba(26,115,232,0.15);}
        .btn-submit{width:100%;background:#1a73e8;color:#fff;border:none;border-radius:30px;font-size:1.05rem;font-weight:800;padding:0.85rem;margin-top:0.5rem;}
        .btn-submit:hover{background:#1558b0;color:#fff;}
        .back-link{display:block;text-align:center;margin-top:1rem;color:#888;font-size:0.85rem;}
        .privacy-note{font-size:0.78rem;color:#999;text-align:center;margin-top:1rem;}
    </style>
</head>
<body>

<div class="apply-header">
    <div class="apply-wrap">
        <h1><i class="bi bi-pencil-square me-2"></i>応募フォーム</h1>
        <div class="job-name">{{ $job->seo_title }}</div>
    </div>
</div>

<div class="apply-body">
    <div class="apply-wrap">

        @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('lp.apply.store', ['token' => $job->token]) }}">
        @csrf

        <div class="form-section">
            <div class="mb-3">
                <label class="form-label">
                    お名前<span class="required-badge">必須</span>
                </label>
                <input type="text" name="applicant_name"
                       class="form-control @error('applicant_name') is-invalid @enderror"
                       value="{{ old('applicant_name') }}"
                       placeholder="山田 太郎">
                @error('applicant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">
                    電話番号<span class="required-badge">必須</span>
                </label>
                <input type="tel" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}"
                       placeholder="09012345678">
                <div class="form-text">ハイフンなしで入力してください</div>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label class="form-label">
                    メールアドレス<span class="optional-badge">任意</span>
                </label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="example@mail.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section">
            <div class="mb-0">
                <label class="form-label">
                    一言メッセージ<span class="optional-badge">任意</span>
                </label>
                <textarea name="appeal_message"
                          class="form-control @error('appeal_message') is-invalid @enderror"
                          rows="4"
                          placeholder="志望動機や希望条件など、自由にご記入ください。">{{ old('appeal_message') }}</textarea>
                @error('appeal_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-send-fill me-2"></i>この内容で応募する
        </button>

        </form>

        <a href="{{ route('lp.show', ['token' => $job->token]) }}" class="back-link">
            <i class="bi bi-arrow-left me-1"></i>求人詳細に戻る
        </a>

        <p class="privacy-note">
            <i class="bi bi-shield-check me-1"></i>
            入力いただいた個人情報は求人への応募目的にのみ使用します。
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
