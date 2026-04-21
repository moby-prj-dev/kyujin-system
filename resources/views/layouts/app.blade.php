<!DOCTYPE html>
<html lang="ja">
<head>
@if(app()->environment('production'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Care Entry</title>
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
            font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;
            background: #f5f7fa;
            color: var(--text);
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
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .site-nav .brand small {
            font-size: 0.75rem;
            font-weight: 400;
            color: var(--muted);
        }

        /* メインコンテンツ */
        main { padding: 36px 0 72px; }

        /* ページタイトルバー */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 28px 0 24px;
            margin-bottom: 32px;
        }
        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0 0 4px;
        }
        .page-header p {
            font-size: 0.87rem;
            opacity: .82;
            margin: 0;
        }

        /* フォームセクション */
        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 20px;
            border: 1.5px solid var(--border);
            box-shadow: 0 1px 6px rgba(26,115,232,.05);
        }
        .form-section h5 {
            font-weight: 800;
            font-size: 0.97rem;
            color: var(--primary);
            border-left: 4px solid var(--primary);
            padding-left: 10px;
            margin-bottom: 18px;
        }

        /* フォームコントロール */
        .form-control, .form-select {
            border-color: #c8d9f0;
            border-width: 1.5px;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            border-width: 2px;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }
        .form-label { font-weight: 600; font-size: 0.92rem; }
        .form-text { font-size: 0.82rem; }

        /* チェックボックス */
        .check-group label { cursor: pointer; }
        .form-check-input { border-color: #aabbd0; border-width: 2px; border-radius: 4px; }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* タブ */
        .nav-tabs { border-bottom: 2px solid var(--border); }
        .nav-tabs .nav-link {
            border-color: var(--border);
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: #fff;
            background: #fff;
        }
        .tab-content {
            border: 1.5px solid var(--border);
            border-top: none;
            border-radius: 0 0 10px 10px;
            padding: 16px;
            background: #fff;
        }

        /* btn-check（ボタン型チェック） */
        .btn-check + .btn-outline-primary {
            border-color: #c8d9f0;
            color: var(--muted);
            border-radius: 20px;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 6px 16px;
        }
        .btn-check:checked + .btn-outline-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* 同意ボックス */
        .agreement-box {
            background: #fffbeb;
            border: 1.5px solid #f59e0b;
            border-radius: 10px;
            padding: 16px 20px;
        }

        /* バッジ・ステータス */
        .badge { font-weight: 700; border-radius: 6px; }

        /* カード */
        .info-card {
            background: var(--soft);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 20px 24px;
        }

        /* アラート */
        .alert { border-radius: 10px; border: none; font-size: 0.92rem; }
        .alert-success { background: #e6f4ea; color: #1e7e34; }
        .alert-info    { background: #e8f0fe; color: #1a73e8; }
        .alert-warning { background: #fff8e1; color: #856404; }
        .alert-danger  { background: #fce8e6; color: #c62828; }

        /* ボタン */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            font-weight: 700;
        }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-outline-primary { border-color: var(--primary); color: var(--primary); border-radius: 8px; font-weight: 700; }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; }
        .btn-outline-secondary { border-radius: 8px; font-weight: 600; }
        .btn-outline-danger    { border-radius: 8px; font-weight: 600; }
        .btn-lg { padding: 12px 28px; font-size: 1rem; }

        /* フッター */
        .site-footer {
            background: #1a1a2e;
            color: #aaa;
            text-align: center;
            padding: 24px 0;
            font-size: 0.82rem;
            margin-top: auto;
        }
        .site-footer a { color: #aaa; text-decoration: none; }
        .site-footer a:hover { color: #fff; }
    </style>
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ナビゲーション --}}
<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="brand">
            <img src="{{ asset('images/logo.svg') }}" alt="Care Entry ケア・エントリー" height="52">
        </a>
        <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i>無料で掲載する
        </a>
    </div>
</nav>

{{-- フラッシュメッセージ --}}
@if(session('success') || session('updated') || session('verified'))
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
    @endif
    @if(session('updated'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>求人情報を更新しました。</div>
    @endif
    @if(session('verified'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>メール認証が完了しました。求人を公開しました。</div>
    @endif
</div>
@endif

<main class="flex-grow-1">
    @yield('content')
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
