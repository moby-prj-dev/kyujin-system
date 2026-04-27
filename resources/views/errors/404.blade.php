<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページが見つかりません｜Care Entry（ケアエントリー）</title>
    <meta name="robots" content="noindex,follow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            background: #f5f7fa;
            color: #2d2d2d;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .nav {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 0;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .nav__logo img { height: 44px; }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 16px;
        }
        .card-404 {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #dde8f8;
            box-shadow: 0 2px 16px rgba(26,115,232,.07);
            padding: 48px 40px;
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .num {
            font-size: 5rem;
            font-weight: 900;
            color: #1a73e8;
            line-height: 1;
            margin-bottom: 12px;
        }
        .title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .desc {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.8;
            margin-bottom: 32px;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1a73e8;
            color: #fff;
            border-radius: 30px;
            font-size: 0.97rem;
            font-weight: 800;
            padding: 13px 32px;
            text-decoration: none;
            transition: .2s;
            margin-bottom: 12px;
        }
        .btn-home:hover { background: #0d47a1; color: #fff; transform: translateY(-1px); }
        .btn-search {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #1a73e8;
            border: 1.5px solid #1a73e8;
            border-radius: 30px;
            font-size: 0.97rem;
            font-weight: 800;
            padding: 12px 32px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-search:hover { background: #f0f7ff; color: #1a73e8; }
        .footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 24px 0;
            font-size: 0.82rem;
            text-align: center;
        }
        .footer a { color: #aaa; text-decoration: none; }
        .footer a:hover { color: #fff; }
    </style>
</head>
<body>

<nav class="nav">
    <div class="container">
        <a href="{{ route('home') }}">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー" style="height:44px;">
        </a>
    </div>
</nav>

<div class="main">
    <div class="card-404">
        <div class="num">404</div>
        <p class="title">ページが見つかりません</p>
        <p class="desc">
            お探しのページは削除されたか、URLが変更された可能性があります。<br>
            求人情報はトップページの検索からお探しください。
        </p>
        <div class="d-flex flex-column align-items-center gap-2">
            <a href="{{ route('home') }}" class="btn-home">
                <i class="bi bi-house-fill"></i>トップページへ
            </a>
            <a href="{{ route('home') }}#search" class="btn-search">
                <i class="bi bi-search"></i>求人を検索する
            </a>
        </div>
    </div>
</div>

<footer class="footer">
    <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
</footer>

</body>
</html>
