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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募完了｜{{ $job->seo_title }}</title>
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
    </div>
</nav>

<main class="flex-grow-1 d-flex align-items-center py-5">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-5 col-md-7">

    <div class="text-center" style="background:#fff; border-radius:16px; border:1.5px solid var(--border); box-shadow:0 2px 12px rgba(26,115,232,.07); padding:3rem 2rem;">
        <div style="font-size:3.5rem; color:var(--primary); margin-bottom:1rem;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h1 style="font-size:1.4rem; font-weight:800; margin-bottom:0.75rem;">応募が完了しました</h1>
        <p style="font-size:0.93rem; color:#555; line-height:1.8; margin-bottom:2rem;">
            ご応募ありがとうございます。<br>
            担当者より折り返しご連絡いたします。<br>
            しばらくお待ちください。
        </p>
        <a href="{{ route('lp.show', ['token' => $job->token]) }}"
           style="display:inline-flex; align-items:center; gap:0.5rem; background:var(--primary); color:#fff; border-radius:30px; font-size:0.95rem; font-weight:700; padding:0.7rem 2rem; text-decoration:none;">
            <i class="bi bi-arrow-left"></i>求人詳細に戻る
        </a>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
