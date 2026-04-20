<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募フォーム｜{{ $job->seo_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary:      #1a73e8;
            --primary-dark: #0d47a1;
            --soft:         #f0f7ff;
            --border:       #d6e4f7;
            --text:         #2d2d2d;
            --muted:        #6c757d;
        }
        body {
            background: #f5f7fa;
            color: var(--text);
            font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;
        }

        /* ナビ */
        .site-nav {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 13px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
        }
        .site-nav .brand {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .site-nav .brand small { font-size: 0.72rem; font-weight: 400; color: var(--muted); }

        /* ページヘッダー */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 28px 0 24px;
            margin-bottom: 32px;
        }
        .page-header h1 { font-size: 1.4rem; font-weight: 800; margin: 0 0 4px; }
        .page-header p  { font-size: 0.87rem; opacity: .82; margin: 0; }

        /* フォームセクション */
        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 20px;
            border: 1.5px solid var(--border);
            box-shadow: 0 1px 6px rgba(26,115,232,.05);
        }
        .form-section .section-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--soft);
        }

        .form-label  { font-weight: 700; font-size: 0.9rem; }
        .form-control {
            border: 1.5px solid #c8d9f0;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--primary);
            border-width: 2px;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }

        .required-badge { font-size: 0.72rem; background: #dc3545; color: #fff; border-radius: 3px; padding: 1px 5px; margin-left: 6px; vertical-align: middle; }

        /* チェックタグ */
        .check-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--soft);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            padding: 0.35rem 0.85rem;
            margin: 0.25rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            transition: all .15s;
        }
        .check-tag input { display: none; }
        .check-tag.selected { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* アラート */
        .alert { border-radius: 10px; border: none; font-size: 0.92rem; }
        .alert-danger { background: #fce8e6; color: #c62828; }

        /* ボタン */
        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1.05rem;
            font-weight: 800;
            padding: 0.85rem;
            margin-top: 0.5rem;
            transition: background .15s;
        }
        .btn-submit:hover { background: var(--primary-dark); color: #fff; }

        /* フッター */
        .site-footer { background: #1a1a2e; color: #aaa; text-align: center; padding: 24px 0; font-size: 0.82rem; margin-top: auto; }
        .site-footer a { color: #aaa; text-decoration: none; }
        .site-footer a:hover { color: #fff; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

{{-- ナビ --}}
<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="brand">
            <img src="/images/logo.svg" alt="Care Entry ケア・エントリー" height="52">
        </a>
        <a href="{{ route('lp.show', ['token' => $job->token]) }}"
           class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:.82rem;">
            <i class="bi bi-arrow-left me-1"></i>求人に戻る
        </a>
    </div>
</nav>

{{-- ページヘッダー --}}
<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-pencil-square me-2"></i>応募フォーム</h1>
        <p>{{ $job->seo_title }}</p>
    </div>
</div>

<main class="flex-grow-1" style="padding-bottom: 48px;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6">

    @if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('lp.apply.store', ['token' => $job->token]) }}" autocomplete="off">
    @csrf

    {{-- 基本情報 --}}
    <div class="form-section">
        <div class="section-title"><i class="bi bi-person-fill"></i> 基本情報</div>

        <div class="mb-3">
            <label class="form-label">お名前<span class="required-badge">必須</span></label>
            <input type="text" name="applicant_name"
                   class="form-control @error('applicant_name') is-invalid @enderror"
                   value="{{ old('applicant_name') }}" placeholder="山田 太郎">
            @error('applicant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">電話番号<span class="required-badge">必須</span></label>
            <input type="tel" name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}" placeholder="09012345678">
            <div class="form-text small text-muted">ハイフンなしで入力してください</div>
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-0">
            <label class="form-label">メールアドレス<span class="required-badge">必須</span></label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="example@mail.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- 希望職種 --}}
    @if($job->jobJobTypes->isNotEmpty())
    <div class="form-section">
        <div class="section-title"><i class="bi bi-briefcase-fill"></i> 希望職種<span class="required-badge ms-2">必須</span></div>
        <div>
            @foreach($job->jobJobTypes as $jjt)
            <label class="check-tag {{ in_array($jjt->job_type_id, old('desired_job_types', [])) ? 'selected' : '' }}">
                <input type="checkbox" name="desired_job_types[]"
                       value="{{ $jjt->job_type_id }}"
                       {{ in_array($jjt->job_type_id, old('desired_job_types', [])) ? 'checked' : '' }}>
                {{ $jjt->jobType->name }}
            </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 希望勤務条件 --}}
    @if($job->jobConditions->isNotEmpty())
    <div class="form-section">
        <div class="section-title"><i class="bi bi-check2-circle"></i> 希望勤務条件<span class="required-badge ms-2">必須</span></div>
        <div>
            @foreach($job->jobConditions as $jc)
            <label class="check-tag {{ in_array($jc->condition_id, old('desired_conditions', [])) ? 'selected' : '' }}">
                <input type="checkbox" name="desired_conditions[]"
                       value="{{ $jc->condition_id }}"
                       {{ in_array($jc->condition_id, old('desired_conditions', [])) ? 'checked' : '' }}>
                {{ $jc->condition->name }}
            </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- メッセージ --}}
    <div class="form-section">
        <div class="section-title"><i class="bi bi-chat-text-fill"></i> メッセージ<span class="text-muted fw-normal ms-2" style="font-size:0.85rem;">（任意）</span></div>
        <textarea name="appeal_message"
                  class="form-control @error('appeal_message') is-invalid @enderror"
                  rows="4"
                  placeholder="志望動機や希望条件など、自由にご記入ください。">{{ old('appeal_message') }}</textarea>
        @error('appeal_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn-submit">
        <i class="bi bi-send-fill me-2"></i>この内容で応募する
    </button>

    </form>

    <div class="text-center mt-3">
        <a href="{{ route('lp.show', ['token' => $job->token]) }}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>求人詳細に戻る
        </a>
    </div>

    <p class="text-center mt-2" style="font-size:0.78rem;color:#aaa;">
        <i class="bi bi-shield-check me-1"></i>入力いただいた個人情報は求人への応募目的にのみ使用します。
    </p>

</div>
</div>
</div>
</main>

{{-- フッター --}}
<footer class="site-footer">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry</strong>
            <span class="ms-2">介護・福祉専門の成果報酬型求人サービス</span>
        </div>
        <div class="mb-3 d-flex justify-content-center gap-3 flex-wrap" style="font-size:0.8rem;">
            <a href="/company">運営者情報</a>
            <a href="/privacy-policy">プライバシーポリシー</a>
            <a href="/terms">利用規約</a>
            <a href="/legal">特定商取引法に基づく表記</a>
        </div>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

<script>
document.querySelectorAll('.check-tag').forEach(label => {
    label.addEventListener('click', () => {
        const cb = label.querySelector('input');
        setTimeout(() => label.classList.toggle('selected', cb.checked), 0);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
