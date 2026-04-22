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
    <title>介護・福祉のお仕事探し｜Care Entry（ケアエントリー）</title>
    <meta name="description" content="介護・福祉職の求人探しをわかりやすくサポート。エリアや職種から探して、自分に合う職場を見つけましょう。LINE応募対応でかんたん応募。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --brand-primary:      #1a73e8;
            --brand-primary-dark: #0d47a1;
            --brand-green:        #2e7d32;
            --brand-green-light:  #e8f5e9;
            --brand-blue-light:   #e8f0fe;
            --brand-teal:         #00897b;
            --brand-line:         #06C755;
            --text:               #2d2d2d;
            --text-muted:         #6c757d;
            --border:             #dee2e6;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--text);
            margin: 0;
            background: #fff;
        }

        /* =====================
           ナビ
        ===================== */
        .seeker-nav {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .seeker-nav .nav-logo img {
            height: 44px;
        }
        .seeker-nav .nav-link-client {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
            transition: .15s;
        }
        .seeker-nav .nav-link-client:hover {
            background: #f8f9fa;
            color: var(--text);
        }

        /* =====================
           ファーストビュー（ヒーロー）
        ===================== */
        .seeker-hero {
            background: linear-gradient(140deg, #f0faf5 0%, #e8f4fd 60%, #fafffe 100%);
            padding: 60px 0 48px;
        }
        .seeker-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--brand-green);
            font-size: 0.82rem;
            font-weight: 700;
            border: 1.5px solid #c8e6c9;
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 20px;
        }
        .seeker-hero-title {
            font-size: clamp(1.55rem, 4vw, 2.2rem);
            font-weight: 900;
            line-height: 1.5;
            color: #1a2a1a;
            margin-bottom: 16px;
        }
        .seeker-hero-title em {
            font-style: normal;
            color: var(--brand-primary);
        }
        .seeker-hero-sub {
            font-size: 0.97rem;
            color: #4a5a4a;
            line-height: 1.8;
            margin-bottom: 28px;
        }
        .seeker-feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 32px;
        }
        .seeker-feature-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1.5px solid #c8e6c9;
            border-radius: 20px;
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--brand-green);
            padding: 5px 14px;
        }
        .seeker-cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-seeker-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-primary);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 800;
            padding: 14px 32px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-seeker-primary:hover {
            background: var(--brand-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-seeker-line {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-line);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 800;
            padding: 14px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-seeker-line:hover {
            background: #05a847;
            color: #fff;
            transform: translateY(-1px);
        }
        .seeker-hero-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 12px;
        }

        /* ヒーローのビジュアル側 */
        .seeker-hero-visual {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }
        .seeker-visual-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px 16px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            border: 1px solid #e8f0e8;
        }
        .seeker-visual-card .vis-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            display: block;
        }
        .seeker-visual-card .vis-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2px;
        }
        .seeker-visual-card .vis-sub {
            font-size: 0.73rem;
            color: var(--text-muted);
        }
        .seeker-visual-card.card-green  { border-top: 3px solid #66bb6a; }
        .seeker-visual-card.card-blue   { border-top: 3px solid #42a5f5; }
        .seeker-visual-card.card-teal   { border-top: 3px solid #26a69a; }
        .seeker-visual-card.card-orange { border-top: 3px solid #ffa726; }

        /* =====================
           検索セクション
        ===================== */
        .search-section {
            background: #fff;
            padding: 40px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .search-section-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .search-section-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        .job-search-form {
            background: #f8fafc;
            border: 1.5px solid #d0e4f7;
            border-radius: 16px;
            padding: 24px;
        }
        .job-search-form .form-label {
            font-size: 0.83rem;
            font-weight: 700;
            color: #444;
            margin-bottom: 5px;
        }
        .job-search-form .form-select,
        .job-search-form .form-control {
            border: 1.5px solid #c8d8ec;
            border-radius: 8px;
            font-size: 0.93rem;
            padding: 10px 14px;
            color: var(--text);
            background-color: #fff;
        }
        .job-search-form .form-select:focus,
        .job-search-form .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(26,115,232,.12);
        }
        .btn-search {
            background: var(--brand-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 800;
            padding: 11px 24px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .2s;
        }
        .btn-search:hover {
            background: var(--brand-primary-dark);
            color: #fff;
        }

        /* =====================
           サービス紹介（3点）
        ===================== */
        .seeker-points-section {
            background: var(--brand-green-light);
            padding: 56px 0;
        }
        .seeker-points-section .section-eyebrow {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--brand-green);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .seeker-points-section .section-heading {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900;
            line-height: 1.4;
            color: #1a2a1a;
            margin-bottom: 40px;
        }
        .point-card {
            background: #fff;
            border-radius: 14px;
            padding: 28px 24px;
            height: 100%;
            border: 1.5px solid #c8e6c9;
            transition: .2s;
        }
        .point-card:hover {
            box-shadow: 0 4px 20px rgba(46,125,50,.1);
            transform: translateY(-2px);
        }
        .point-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--brand-green-light);
            color: var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 14px;
        }
        .point-card h5 {
            font-size: 0.97rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #1a2a1a;
        }
        .point-card p {
            font-size: 0.87rem;
            color: #555;
            line-height: 1.75;
            margin: 0;
        }

        /* =====================
           LINE応募 CTA帯
        ===================== */
        .seeker-line-cta {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            color: #fff;
            padding: 60px 0;
            text-align: center;
        }
        .seeker-line-cta h2 {
            font-size: clamp(1.2rem, 3vw, 1.65rem);
            font-weight: 900;
            margin-bottom: 10px;
        }
        .seeker-line-cta p {
            opacity: .88;
            font-size: 0.95rem;
            margin-bottom: 32px;
        }
        .btn-line-large {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--brand-line);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1.05rem;
            font-weight: 800;
            padding: 16px 40px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-line-large:hover {
            background: #05a847;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-search-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,.7);
            border-radius: 30px;
            font-size: 0.97rem;
            font-weight: 700;
            padding: 14px 32px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-search-outline:hover {
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        /* =====================
           フッター
        ===================== */
        .seeker-footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 36px 0;
            font-size: 0.84rem;
            text-align: center;
        }
        .seeker-footer a { color: #aaa; text-decoration: none; }
        .seeker-footer a:hover { color: #fff; }
        .seeker-footer .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px 20px;
            margin-bottom: 16px;
        }

        /* =====================
           レスポンシブ
        ===================== */
        @media (max-width: 767px) {
            .seeker-hero { padding: 44px 0 36px; }
            .seeker-hero-visual { margin-top: 32px; }
            .seeker-points-section { padding: 44px 0; }
            .seeker-line-cta { padding: 48px 0; }
        }
        @media (min-width: 992px) {
            .seeker-hero { padding: 72px 0 60px; }
            .search-section { padding: 48px 0; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ナビ --}}
<nav class="seeker-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="nav-logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('client') }}" class="nav-link-client">
            <i class="bi bi-building me-1"></i>求人掲載をお考えの方
        </a>
    </div>
</nav>

{{-- ① ファーストビュー --}}
<section class="seeker-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="seeker-hero-badge">
                    <i class="bi bi-heart-fill"></i>介護・福祉のお仕事探し
                </div>
                <h1 class="seeker-hero-title">
                    今の働き方に、<br>
                    少しでも迷いがあるなら。
                </h1>
                <p class="seeker-hero-sub">
                    ケアエントリーは、介護・福祉職の求人探しをわかりやすくサポートします。<br>
                    地域や職種から探して、自分に合う職場を見つけましょう。
                </p>
                <div class="seeker-feature-list">
                    <span class="seeker-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>会員登録不要
                    </span>
                    <span class="seeker-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>30秒でかんたん応募
                    </span>
                    <span class="seeker-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>LINEで気軽に相談OK
                    </span>
                </div>
                <div class="seeker-cta-group">
                    <a href="#search" class="btn-seeker-primary">
                        <i class="bi bi-search"></i>求人を探す
                    </a>
                    <a href="https://lin.ee/" class="btn-seeker-line">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                        LINEで相談する
                    </a>
                </div>
                <p class="seeker-hero-note">
                    <i class="bi bi-shield-check me-1"></i>応募は無料。個人情報は適切に管理されます。
                </p>
            </div>
            <div class="col-lg-6">
                <div class="seeker-hero-visual">
                    <div class="seeker-visual-card card-green">
                        <span class="vis-icon text-success"><i class="bi bi-geo-alt-fill"></i></span>
                        <div class="vis-label">エリアから探す</div>
                        <div class="vis-sub">地域に合わせて絞り込み</div>
                    </div>
                    <div class="seeker-visual-card card-blue">
                        <span class="vis-icon text-primary"><i class="bi bi-briefcase-fill"></i></span>
                        <div class="vis-label">職種から探す</div>
                        <div class="vis-sub">介護・福祉の多様な職種</div>
                    </div>
                    <div class="seeker-visual-card card-teal">
                        <span class="vis-icon" style="color:#00897b"><i class="bi bi-chat-dots-fill"></i></span>
                        <div class="vis-label">LINEで応募・相談</div>
                        <div class="vis-sub">担当者に気軽に連絡</div>
                    </div>
                    <div class="seeker-visual-card card-orange">
                        <span class="vis-icon text-warning"><i class="bi bi-person-check-fill"></i></span>
                        <div class="vis-label">かんたん応募</div>
                        <div class="vis-sub">名前と電話番号だけでOK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ② 求人検索フォーム --}}
<section class="search-section" id="search">
    <div class="container">
        <p class="search-section-title">求人を探す</p>
        <p class="search-section-sub">エリア・職種・キーワードで絞り込めます</p>
        <form action="/" method="GET" class="job-search-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="search-area">
                        <i class="bi bi-geo-alt me-1"></i>エリア
                    </label>
                    <select class="form-select" id="search-area" name="area">
                        <option value="">すべてのエリア</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->slug }}" @selected(request('area') === $area->slug)>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="search-job-type">
                        <i class="bi bi-briefcase me-1"></i>職種
                    </label>
                    <select class="form-select" id="search-job-type" name="job_type">
                        <option value="">すべての職種</option>
                        @foreach($jobTypes as $jobType)
                            <option value="{{ $jobType->slug }}" @selected(request('job_type') === $jobType->slug)>
                                {{ $jobType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="search-keyword">
                        <i class="bi bi-search me-1"></i>キーワード
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="search-keyword"
                        name="keyword"
                        placeholder="例：夜勤なし、駅近、資格不問"
                        value="{{ request('keyword') }}"
                    >
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i>求人を検索する
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- ③ サービスの3つの特長 --}}
<section class="seeker-points-section">
    <div class="container">
        <div class="text-center">
            <div class="section-eyebrow">WHY CARE ENTRY</div>
            <h2 class="section-heading">介護・福祉の転職を<br>もっと身近に</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="point-card">
                    <div class="point-card-icon">
                        <i class="bi bi-person-fill-check"></i>
                    </div>
                    <h5>会員登録なしで応募できる</h5>
                    <p>アカウント作成は不要です。名前と電話番号（またはLINE）を入力するだけで応募が完了します。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="point-card">
                    <div class="point-card-icon">
                        <i class="bi bi-chat-heart-fill"></i>
                    </div>
                    <h5>LINEで気軽に相談・応募</h5>
                    <p>使い慣れたLINEアプリで応募・相談ができます。担当者と直接やり取りできるので、不安なことをすぐに確認できます。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="point-card">
                    <div class="point-card-icon">
                        <i class="bi bi-shield-check-fill"></i>
                    </div>
                    <h5>介護・福祉に特化した求人</h5>
                    <p>介護・福祉領域の求人のみを掲載。職種・エリアから絞り込めるので、自分に合う職場を見つけやすい設計です。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ④ LINE相談 CTA --}}
<section class="seeker-line-cta">
    <div class="container">
        <h2>まずは気軽に、LINEで相談してみませんか</h2>
        <p>転職を迷っている段階でも大丈夫。どんな小さな疑問でも、お気軽にご相談ください。</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="https://lin.ee/" class="btn-line-large">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                LINEで相談する（無料）
            </a>
            <a href="#search" class="btn-search-outline">
                <i class="bi bi-search"></i>求人を探す
            </a>
        </div>
    </div>
</section>

{{-- フッター --}}
<footer class="seeker-footer">
    <div class="container">
        <div class="mb-3">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">介護・福祉専門の求人サービス</span>
        </div>
        <div class="footer-links">
            <a href="{{ route('client') }}">求人掲載をお考えの方</a>
            <a href="{{ route('company') }}">運営者情報</a>
            <a href="{{ route('privacy-policy') }}">プライバシーポリシー</a>
            <a href="{{ route('terms') }}">利用規約</a>
            <a href="{{ route('legal') }}">特定商取引法に基づく表記</a>
        </div>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
