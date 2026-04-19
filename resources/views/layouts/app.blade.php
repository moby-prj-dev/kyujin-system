<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '求人掲載システム')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-section { background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .form-section h5 { font-weight: 700; border-left: 4px solid #0d6efd; padding-left: 10px; margin-bottom: 16px; }
        .check-group label { cursor: pointer; }
        .form-check-input { border-color: #999; border-width: 2px; }
        .form-check-input:checked { border-color: #0d6efd; }
        .form-control, .form-select { border-color: #999; border-width: 1.5px; }
        .form-control:focus, .form-select:focus { border-color: #0d6efd; border-width: 2px; }
        .form-section { border: 2px solid #aaa; }
        .tab-content { border: 2px solid #aaa; border-top: none; border-radius: 0 0 6px 6px; padding: 12px; }
        .nav-tabs { border-bottom: 2px solid #aaa; }
        .nav-tabs .nav-link { border-color: #aaa; }
        .nav-tabs .nav-link.active { border-bottom-color: #fff; }
        .agreement-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 16px; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold">求人掲載システム</span>
    </div>
</nav>
<main class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('updated'))
        <div class="alert alert-info">求人情報を更新しました。</div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
