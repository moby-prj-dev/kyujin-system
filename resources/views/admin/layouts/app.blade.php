<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') | Care Entry Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary:#1a73e8; --primary-dark:#0d47a1; --border:#d6e4f7; }
        body { font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif; background:#f0f2f5; min-height:100vh; }
        .admin-nav { background:#1a1a2e; padding:0; }
        .admin-nav .navbar-brand { color:#fff; font-weight:800; font-size:1rem; padding:14px 20px; }
        .admin-nav .nav-link { color:rgba(255,255,255,.7); font-size:0.88rem; font-weight:600; padding:14px 16px; }
        .admin-nav .nav-link:hover, .admin-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.08); }
        .admin-nav .nav-link i { margin-right:4px; }
        .admin-main { padding:28px 0 60px; }
        .page-title { font-size:1.2rem; font-weight:800; color:#1a1a2e; margin-bottom:20px; }
        .card { border:1.5px solid var(--border); border-radius:12px; box-shadow:0 1px 6px rgba(26,115,232,.05); }
        .card-header { background:#f8faff; border-bottom:1.5px solid var(--border); font-weight:700; font-size:0.93rem; border-radius:10px 10px 0 0 !important; }
        .table th { font-size:0.83rem; font-weight:700; color:#555; background:#f8faff; }
        .table td { font-size:0.88rem; vertical-align:middle; }
        .badge { font-weight:700; font-size:0.75rem; }
        .btn-xs { padding:2px 10px; font-size:0.78rem; border-radius:4px; font-weight:600; }
        .alert { border-radius:8px; border:none; font-size:0.9rem; }
        .alert-success { background:#e6f4ea; color:#1e7e34; }
        .trial-active      { background:#e6f4ea; color:#137333; }
        .trial-ending-soon { background:#fff8e1; color:#856404; }
        .trial-ended       { background:#e8eaf6; color:#3949ab; }
        .trial-billing     { background:#fce8e6; color:#c62828; }
    </style>
</head>
<body>

<nav class="admin-nav navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.jobs.index') }}">
            <i class="bi bi-shield-lock me-1"></i>Care Entry Admin
        </a>
        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <i class="bi bi-list text-white fs-5"></i>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}"
                       href="{{ route('admin.jobs.index') }}">
                        <i class="bi bi-building"></i>企業・求人
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}"
                       href="{{ route('admin.applications.index') }}">
                        <i class="bi bi-people"></i>応募一覧
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.billings.*') ? 'active' : '' }}"
                       href="{{ route('admin.billings.index') }}">
                        <i class="bi bi-receipt"></i>請求管理
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.logout') }}">
                        <i class="bi bi-box-arrow-right"></i>ログアウト
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="admin-main">
    <div class="container-fluid" style="max-width:1400px;">
        @if(session('success'))
        <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
