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
    <title>沖縄の介護・福祉求人｜Care Entry（ケアエントリー）</title>
    <meta name="description" content="沖縄の介護・福祉求人に特化した求人サービス。地域・職種から自分に合う職場を探せます。会員登録不要、LINEでそのまま応募できます。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary:      #1a73e8;
            --color-primary-dark: #0d47a1;
            --color-green:        #2e7d32;
            --color-green-light:  #e8f5e9;
            --color-green-border: #c8e6c9;
            --color-line:         #06C755;
            --color-text:         #2d2d2d;
            --color-muted:        #6c757d;
            --color-border:       #dee2e6;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--color-text);
            margin: 0;
            background: #fff;
        }

        /* =====================
           ナビ
        ===================== */
        .nav {
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .nav__logo img { height: 44px; }
        .nav__link-client {
            font-size: 0.82rem;
            color: var(--color-muted);
            text-decoration: none;
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
            transition: .15s;
        }
        .nav__link-client:hover {
            background: #f8f9fa;
            color: var(--color-text);
        }

        /* =====================
           ファーストビュー
        ===================== */
        .hero {
            background: linear-gradient(140deg, #f0faf5 0%, #e8f4fd 60%, #fafffe 100%);
            padding: 60px 0 48px;
        }
        .hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--color-green);
            font-size: 0.82rem;
            font-weight: 700;
            border: 1.5px solid var(--color-green-border);
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 18px;
        }
        .hero__title {
            font-size: clamp(1.55rem, 4vw, 2.2rem);
            font-weight: 900;
            line-height: 1.5;
            color: #1a2a1a;
            margin-bottom: 14px;
        }
        .hero__sub {
            font-size: 0.97rem;
            color: #4a5a4a;
            line-height: 1.85;
            margin-bottom: 24px;
        }
        .hero__features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 28px;
        }
        .hero__feature-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1.5px solid var(--color-green-border);
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--color-green);
            padding: 5px 13px;
        }
        .hero__cta-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .hero__cta-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 800;
            padding: 14px 32px;
            text-decoration: none;
            transition: .2s;
        }
        .hero__cta-primary:hover {
            background: var(--color-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }
        .hero__cta-secondary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--color-muted);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            padding: 4px 0;
            border-bottom: 1.5px solid transparent;
            transition: .15s;
        }
        .hero__cta-secondary:hover {
            color: var(--color-green);
            border-bottom-color: var(--color-green);
        }
        .hero__note {
            font-size: 0.74rem;
            color: var(--color-muted);
        }

        /* ヒーロー ビジュアル */
        .hero__visual {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .hero__visual-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 14px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            border: 1px solid #e8f0e8;
        }
        .hero__visual-card--green  { border-top: 3px solid #66bb6a; }
        .hero__visual-card--blue   { border-top: 3px solid #42a5f5; }
        .hero__visual-card--teal   { border-top: 3px solid #26a69a; }
        .hero__visual-card--orange { border-top: 3px solid #ffa726; }
        .hero__visual-icon {
            font-size: 1.9rem;
            margin-bottom: 7px;
            display: block;
        }
        .hero__visual-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2px;
        }
        .hero__visual-sub {
            font-size: 0.72rem;
            color: var(--color-muted);
        }

        /* =====================
           検索フォーム
        ===================== */
        .search {
            background: #fff;
            padding: 40px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .search__heading {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .search__sub {
            font-size: 0.84rem;
            color: var(--color-muted);
            margin-bottom: 20px;
        }
        .search__form {
            background: #f8fafc;
            border: 1.5px solid #d0e4f7;
            border-radius: 16px;
            padding: 22px;
        }
        .search__label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #444;
            margin-bottom: 5px;
            display: block;
        }
        .search__select,
        .search__input {
            width: 100%;
            border: 1.5px solid #c8d8ec;
            border-radius: 8px;
            font-size: 0.93rem;
            padding: 10px 12px;
            color: var(--color-text);
            background-color: #fff;
            appearance: auto;
        }
        .search__select:focus,
        .search__input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(26,115,232,.12);
        }
        .search__submit {
            width: 100%;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 800;
            padding: 11px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            transition: .2s;
        }
        .search__submit:hover { background: var(--color-primary-dark); }

        /* =====================
           特長セクション
        ===================== */
        .points {
            background: var(--color-green-light);
            padding: 56px 0;
        }
        .points__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--color-green);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .points__heading {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900;
            line-height: 1.4;
            color: #1a2a1a;
            margin-bottom: 36px;
        }
        .point-card {
            background: #fff;
            border-radius: 14px;
            padding: 26px 22px;
            height: 100%;
            border: 1.5px solid var(--color-green-border);
            transition: .2s;
        }
        .point-card:hover {
            box-shadow: 0 4px 18px rgba(46,125,50,.1);
            transform: translateY(-2px);
        }
        .point-card__icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--color-green-light);
            color: var(--color-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 13px;
        }
        .point-card__title {
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 7px;
            color: #1a2a1a;
        }
        .point-card__body {
            font-size: 0.86rem;
            color: #555;
            line-height: 1.75;
            margin: 0;
        }

        /* =====================
           2ndビュー リスト
        ===================== */
        .points__cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }
        .points__cta-primary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--color-primary);
            color: #fff;
            border-radius: 30px;
            font-size: 0.97rem;
            font-weight: 800;
            padding: 12px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .points__cta-primary:hover { background: var(--color-primary-dark); color: #fff; transform: translateY(-1px); }
        .points__cta-line {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--color-line);
            color: #fff;
            border-radius: 30px;
            font-size: 0.97rem;
            font-weight: 800;
            padding: 12px 24px;
            text-decoration: none;
            transition: .2s;
        }
        .points__cta-line:hover { background: #05a847; color: #fff; transform: translateY(-1px); }
        .points__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .points__item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 18px 0;
            border-bottom: 1px solid var(--color-green-border);
        }
        .points__item:last-child { border-bottom: none; }
        .points__item-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #fff;
            color: var(--color-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            border: 1.5px solid var(--color-green-border);
        }
        .points__item-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1a2a1a;
            margin-bottom: 3px;
        }
        .points__item-body {
            font-size: 0.85rem;
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* =====================
           LINE応募 CTA帯
        ===================== */
        .cta-band {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff;
            padding: 60px 0;
            text-align: center;
        }
        .cta-band__title {
            font-size: clamp(1.15rem, 3vw, 1.6rem);
            font-weight: 900;
            margin-bottom: 10px;
        }
        .cta-band__body {
            opacity: .88;
            font-size: 0.94rem;
            margin-bottom: 30px;
        }
        .cta-band__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 14px;
        }
        .cta-band__btn-line {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--color-line);
            color: #fff;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 800;
            padding: 15px 36px;
            text-decoration: none;
            transition: .2s;
        }
        .cta-band__btn-line:hover { background: #05a847; color: #fff; transform: translateY(-2px); }
        .cta-band__btn-search {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,.65);
            border-radius: 30px;
            font-size: 0.94rem;
            font-weight: 700;
            padding: 13px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .cta-band__btn-search:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* =====================
           フッター
        ===================== */
        .footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 34px 0;
            font-size: 0.83rem;
            text-align: center;
        }
        .footer a { color: #aaa; text-decoration: none; }
        .footer a:hover { color: #fff; }
        .footer__links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px 18px;
            margin-bottom: 14px;
        }

        /* =====================
           レスポンシブ
        ===================== */
        @media (max-width: 767px) {
            .hero { padding: 40px 0 32px; }
            .hero__visual { margin-top: 30px; }
            .points { padding: 44px 0; }
            .cta-band { padding: 48px 0; }
            .search { padding: 32px 0; }
        }
        @media (min-width: 992px) {
            .hero { padding: 72px 0 60px; }
            .search { padding: 48px 0; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ナビ --}}
<nav class="nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('client') }}" class="nav__link-client">
            <i class="bi bi-building me-1"></i>求人掲載をお考えの方
        </a>
    </div>
</nav>

{{-- ① ファーストビュー --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero__badge">
                    <i class="bi bi-geo-alt-fill"></i>沖縄の介護・福祉求人
                </div>
                <h1 class="hero__title">
                    沖縄での介護の仕事探しを、<br>もっとシンプルに。
                </h1>
                <p class="hero__sub">
                    ケアエントリーは、沖縄の介護・福祉求人に特化した求人サービスです。<br>
                    地域・職種から、自分に合う職場を探せます。
                </p>
                <div class="hero__features">
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>会員登録なしで応募OK
                    </span>
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>30秒でかんたん応募
                    </span>
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>LINEからそのまま応募できる
                    </span>
                </div>
                <div class="hero__cta-group">
                    <a href="#search" class="hero__cta-primary">
                        <i class="bi bi-search"></i>求人を探す
                    </a>
                    <a href="https://lin.ee/" class="hero__cta-secondary">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                        LINEで応募する
                    </a>
                </div>
                <p class="hero__note">
                    <i class="bi bi-shield-check me-1"></i>応募は無料。個人情報は適切に管理されます。
                </p>
            </div>
            <div class="col-lg-6">
                <div class="hero__visual">
                    <div class="hero__visual-card hero__visual-card--green">
                        <span class="hero__visual-icon text-success"><i class="bi bi-geo-alt-fill"></i></span>
                        <div class="hero__visual-label">那覇・南部</div>
                        <div class="hero__visual-sub">那覇市・糸満市ほか</div>
                    </div>
                    <div class="hero__visual-card hero__visual-card--blue">
                        <span class="hero__visual-icon text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                        <div class="hero__visual-label">中部エリア</div>
                        <div class="hero__visual-sub">浦添市・沖縄市ほか</div>
                    </div>
                    <div class="hero__visual-card hero__visual-card--teal">
                        <span class="hero__visual-icon" style="color:#00897b"><i class="bi bi-geo-alt-fill"></i></span>
                        <div class="hero__visual-label">北部エリア</div>
                        <div class="hero__visual-sub">名護市・恩納村ほか</div>
                    </div>
                    <div class="hero__visual-card hero__visual-card--orange">
                        <span class="hero__visual-icon text-warning"><i class="bi bi-geo-alt-fill"></i></span>
                        <div class="hero__visual-label">離島エリア</div>
                        <div class="hero__visual-sub">石垣市・宮古島市ほか</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ② 求人検索フォーム --}}
<section class="search" id="search">
    <div class="container">
        <p class="search__heading">沖縄の求人を探す</p>
        <p class="search__sub">エリア・職種・キーワードで絞り込めます</p>
        <form action="/" method="GET" class="search__form">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-md-3">
                    <label class="search__label" for="search-area">
                        <i class="bi bi-geo-alt me-1"></i>エリア
                    </label>
                    <select class="search__select" id="search-area" name="area">
                        <option value="">エリアを選択（沖縄県）</option>
                        @foreach($areasByRegion as $region => $regionAreas)
                            <optgroup label="{{ $region }}">
                                @foreach($regionAreas as $area)
                                    <option value="{{ $area->slug }}" @selected(request('area') === $area->slug)>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="search__label" for="search-job-type">
                        <i class="bi bi-briefcase me-1"></i>職種
                    </label>
                    <select class="search__select" id="search-job-type" name="job_type">
                        <option value="">職種を選択</option>
                        @foreach($jobTypesByCategory as $category => $jobTypes)
                            <optgroup label="{{ $category }}">
                                @foreach($jobTypes as $jobType)
                                    <option value="{{ $jobType->slug }}" @selected(request('job_type') === $jobType->slug)>
                                        {{ $jobType->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="search__label" for="search-keyword">
                        <i class="bi bi-search me-1"></i>キーワード
                    </label>
                    <input
                        type="text"
                        class="search__input"
                        id="search-keyword"
                        name="keyword"
                        placeholder="例：夜勤なし、資格不問、駅近"
                        value="{{ request('keyword') }}"
                    >
                </div>
                <div class="col-md-2">
                    <button type="submit" class="search__submit">
                        <i class="bi bi-search"></i>求人を検索する
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- ③ 2ndビュー：サービス紹介 --}}
<section class="points">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <p class="points__eyebrow">WHY CARE ENTRY</p>
                <h2 class="points__heading">沖縄での介護の仕事探しを、<br>もっとシンプルにするために</h2>
                <div class="points__cta-group">
                    <a href="#search" class="points__cta-primary">
                        <i class="bi bi-search"></i>求人を探す
                    </a>
                    <a href="https://lin.ee/" class="points__cta-line">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                        LINEで応募する
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <ul class="points__list">
                    <li class="points__item">
                        <span class="points__item-icon"><i class="bi bi-geo-alt-fill"></i></span>
                        <div>
                            <p class="points__item-title">沖縄の介護・福祉求人に特化しています</p>
                            <p class="points__item-body">那覇・南部・中部・北部・離島と、沖縄全域の求人を掲載。エリアから絞り込んで探せます。</p>
                        </div>
                    </li>
                    <li class="points__item">
                        <span class="points__item-icon"><i class="bi bi-briefcase-fill"></i></span>
                        <div>
                            <p class="points__item-title">地域や職種から、自分に合う仕事を探せます</p>
                            <p class="points__item-body">介護職・福祉職・相談員・リハビリ職など、多様な職種から条件に合う求人を探せます。</p>
                        </div>
                    </li>
                    <li class="points__item">
                        <span class="points__item-icon"><i class="bi bi-person-fill-check"></i></span>
                        <div>
                            <p class="points__item-title">会員登録なしで、かんたんに応募できます</p>
                            <p class="points__item-body">アカウント作成不要。名前と電話番号を入力するだけで応募完了します。</p>
                        </div>
                    </li>
                    <li class="points__item">
                        <span class="points__item-icon" style="color:#06C755"><i class="bi bi-chat-dots-fill"></i></span>
                        <div>
                            <p class="points__item-title">LINEからそのまま応募することも可能です</p>
                            <p class="points__item-body">使い慣れたLINEで気軽に応募できます。担当者に直接連絡して、疑問をすぐ解消できます。</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ④ LINE応募 CTA --}}
<section class="cta-band">
    <div class="container">
        <h2 class="cta-band__title">気になる求人に、LINEでそのまま応募できます</h2>
        <p class="cta-band__body">会員登録不要。LINEを使って、今すぐかんたんに応募できます。</p>
        <div class="cta-band__actions">
            <a href="https://lin.ee/" class="cta-band__btn-line">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                LINEで応募する
            </a>
            <a href="#search" class="cta-band__btn-search">
                <i class="bi bi-search"></i>求人を探す
            </a>
        </div>
    </div>
</section>

{{-- フッター --}}
<footer class="footer">
    <div class="container">
        <div class="mb-3">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">沖縄の介護・福祉専門の求人サービス</span>
        </div>
        <div class="footer__links">
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
