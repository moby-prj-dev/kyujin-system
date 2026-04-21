<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | Care Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background:#f0f2f5; font-family:'Hiragino Kaku Gothic ProN',sans-serif; }
        .login-wrap { max-width:400px; margin:80px auto; }
        .login-card { background:#fff; border-radius:16px; padding:40px 36px; box-shadow:0 4px 24px rgba(0,0,0,.1); }
        .login-brand { text-align:center; margin-bottom:28px; }
        .login-brand .brand-name { font-size:1.3rem; font-weight:800; color:#1a73e8; }
        .login-brand small { display:block; color:#666; font-size:0.82rem; margin-top:4px; }
        .form-control { border:1.5px solid #d6e4f7; border-radius:8px; }
        .form-control:focus { border-color:#1a73e8; box-shadow:0 0 0 3px rgba(26,115,232,.1); }
        .btn-login { background:#1a73e8; color:#fff; border:none; border-radius:8px; font-weight:700; padding:12px; width:100%; }
        .btn-login:hover { background:#0d47a1; color:#fff; }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-name"><i class="bi bi-shield-lock me-1"></i>Care Entry</div>
            <small>管理者ログイン</small>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:0.88rem; background:#fce8e6; color:#c62828; border:none; border-radius:8px;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:0.9rem;">管理者ID</label>
                <input type="text" name="id" class="form-control" value="{{ old('id') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size:0.9rem;">パスワード</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i>ログイン
            </button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
