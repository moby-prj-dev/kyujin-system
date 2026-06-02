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
    <title>介護・福祉 求人掲載【沖縄エリア対応】｜Care Entry(ケアエントリー)</title>
    <meta name="description" content="介護・福祉事業者向け求人掲載サービス(対応エリア:沖縄県)。求職者が動きやすい仕組みにこだわっています。掲載費0円スタート、成果型課金で始めやすい。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary:      #1a73e8;
            --color-primary-dark: #0d47a1;
            --color-blue-light:   #e8f0fe;
            --color-blue-mid:     #c5d8f8;
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
        .nav__link-seeker {
            font-size: 0.82rem;
            color: var(--color-muted);
            text-decoration: none;
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
            transition: .15s;
        }
        .nav__link-seeker:hover {
            background: #f8f9fa;
            color: var(--color-text);
        }

        /* =====================
           ファーストビュー
        ===================== */
        .hero {
            background: linear-gradient(140deg, #e8f0fe 0%, #f3f5ff 60%, #fafbff 100%);
            padding: 64px 0 52px;
        }
        .hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--color-primary);
            font-size: 0.82rem;
            font-weight: 700;
            border: 1.5px solid var(--color-blue-mid);
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 18px;
        }
        .hero__title {
            font-size: clamp(1.5rem, 4vw, 2.1rem);
            font-weight: 900;
            line-height: 1.5;
            color: #0d1a2e;
            margin-bottom: 14px;
        }
        .hero__sub {
            font-size: 0.97rem;
            color: #3a4a5a;
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
            border: 1.5px solid var(--color-blue-mid);
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--color-primary);
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
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }
        .hero__note {
            font-size: 0.74rem;
            color: var(--color-muted);
        }

        /* ヒーロー ビジュアルカード */
        .hero__visual {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 22px rgba(26,115,232,.1);
            border: 1.5px solid var(--color-blue-mid);
            padding: 24px;
        }
        .hero__visual-row {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding: 13px 0;
            border-bottom: 1px solid #f0f4fc;
        }
        .hero__visual-row:first-child { padding-top: 0; }
        .hero__visual-row:last-child  { border-bottom: none; padding-bottom: 0; }
        .hero__visual-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--color-blue-light);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .hero__visual-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1a1a1a;
        }
        .hero__visual-sub {
            font-size: 0.77rem;
            color: var(--color-muted);
            margin-top: 3px;
        }

        /* =====================
           補足ストリップ
        ===================== */
        .strip {
            background: var(--color-blue-light);
            border-top: 1px solid var(--color-blue-mid);
            border-bottom: 1px solid var(--color-blue-mid);
            padding: 13px 0;
        }
        .strip__items {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px 22px;
        }
        .strip__item {
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* =====================
           特長セクション
        ===================== */
        .merits {
            background: #fff;
            padding: 64px 0;
        }
        .merits__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .merits__heading {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900;
            line-height: 1.4;
            color: #0d1a2e;
            margin-bottom: 36px;
        }
        .merit-card {
            background: #fff;
            border: 1.5px solid var(--color-blue-mid);
            border-radius: 14px;
            padding: 26px 22px;
            height: 100%;
            transition: .2s;
        }
        .merit-card:hover {
            box-shadow: 0 4px 18px rgba(26,115,232,.1);
            transform: translateY(-2px);
        }
        .merit-card__icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--color-blue-light);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 13px;
        }
        .merit-card__title {
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 7px;
            color: #0d1a2e;
        }
        .merit-card__body {
            font-size: 0.86rem;
            color: #555;
            line-height: 1.75;
            margin: 0;
        }

        /* =====================
           掲載の流れ
        ===================== */
        .steps {
            background: #f8faff;
            padding: 64px 0;
            border-top: 1px solid var(--color-blue-mid);
        }
        .steps__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .steps__heading {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900;
            line-height: 1.4;
            color: #0d1a2e;
            margin-bottom: 32px;
        }
        .step {
            display: flex;
            gap: 15px;
            padding: 18px 0;
            border-bottom: 1px solid #e8eeff;
        }
        .step:last-child { border-bottom: none; }
        .step__num {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--color-primary);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step__title {
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 4px;
            color: #0d1a2e;
        }
        .step__body {
            font-size: 0.86rem;
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* =====================
           最終CTA帯
        ===================== */
        .cta-band {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff;
            padding: 64px 0;
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
        .cta-band__btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--color-primary);
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 800;
            padding: 15px 38px;
            text-decoration: none;
            transition: .2s;
        }
        .cta-band__btn-primary:hover {
            background: var(--color-blue-light);
            color: var(--color-primary);
            transform: translateY(-2px);
        }
        .cta-band__btn-secondary {
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
        .cta-band__btn-secondary:hover { background: rgba(255,255,255,.12); color: #fff; }

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
            .hero { padding: 44px 0 36px; }
            .hero__visual { margin-top: 30px; }
            .merits { padding: 48px 0; }
            .steps { padding: 48px 0; }
            .cta-band { padding: 48px 0; }
        }
        @media (min-width: 992px) {
            .hero { padding: 80px 0 64px; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- モニター告知帯 --}}
@if(now()->lte($monitorCutoff))
<div style="position:sticky;top:0;z-index:200;background:linear-gradient(90deg,#fff8e1,#fff3cd);border-bottom:3px solid #f9a825;padding:10px 0;text-align:center;">
    <span style="font-size:0.85rem;font-weight:800;color:#f57f17;">
        <i class="bi bi-star-fill me-1"></i>無料モニター募集中
        &nbsp;〜&nbsp;{{ $monitorCutoff->format('Y年m月d日') }}まで
        &nbsp;｜&nbsp;3か月間または有効応募3件まで成果報酬0円
    </span>
</div>
@endif

{{-- ナビ --}}
<nav class="nav" style="{{ now()->lte($monitorCutoff) ? 'top:44px;' : '' }}">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('home') }}" class="nav__link-seeker">
            <i class="bi bi-search me-1"></i>お仕事を探している方はこちら
        </a>
    </div>
</nav>

{{-- ① ファーストビュー --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero__badge">
                    <i class="bi bi-building"></i>介護・福祉専門 求人掲載サービス【対応エリア:沖縄県】
                </div>
                <h1 class="hero__title">
                    沖縄での介護・福祉人材の<br>採用を、もっとシンプルに。
                </h1>
                <p class="hero__sub">
                    応募前に条件を確認する仕組みにより、ミスマッチを減らす設計です。<br>
                    求職者と企業の条件を事前にすり合わせることで、無駄な応募を減らします。<br>
                    <span style="font-size:0.85em;color:#888;">（フォームまたはLINEから応募可能）</span>
                </p>
                <div class="hero__features">
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>掲載費0円スタート
                    </span>
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>ミスマッチを減らす設計
                    </span>
                    <span class="hero__feature-item">
                        <i class="bi bi-check-circle-fill"></i>介護・福祉領域に特化
                    </span>
                </div>
                <div class="hero__cta-group">
                    <a href="{{ route('jobs.create') }}" class="hero__cta-primary">
                        <i class="bi bi-plus-circle-fill"></i>無料で求人を掲載する
                    </a>
                    <a href="#steps" class="hero__cta-secondary">
                        <i class="bi bi-list-ol"></i>掲載の流れを見る
                    </a>
                </div>
                <p class="hero__note">
                    <i class="bi bi-shield-check me-1"></i>まずは無料で掲載し、反応を確認していただけます
                </p>
                <p style="margin-top:10px;font-size:0.82rem;background:#fff3cd;border:1.5px solid #f9a825;border-radius:8px;padding:8px 14px;display:inline-block;">
                    <i class="bi bi-clock me-1" style="color:#f57f17;"></i>
                    <strong style="color:#f57f17;">無料モニター締切：{{ $monitorCutoff->format('Y年m月d日') }}</strong>
                    &nbsp;／ 3か月間または有効応募3件まで無料
                </p>
            </div>
            <div class="col-lg-6">
                <div class="hero__visual">
                    <div class="hero__visual-row">
                        <div class="hero__visual-icon"><i class="bi bi-currency-yen"></i></div>
                        <div>
                            <div class="hero__visual-label">掲載費0円で始められる</div>
                            <div class="hero__visual-sub">初期費用・月額費用なし。応募が届いたときだけ課金。</div>
                        </div>
                    </div>
                    <div class="hero__visual-row">
                        <div class="hero__visual-icon"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                        <div>
                            <div class="hero__visual-label">条件確認の仕組みでミスマッチを減らす</div>
                            <div class="hero__visual-sub">応募前に条件をすり合わせる設計で、無駄な応募を減らします。</div>
                        </div>
                    </div>
                    <div class="hero__visual-row">
                        <div class="hero__visual-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div>
                            <div class="hero__visual-label">最短当日から掲載開始</div>
                            <div class="hero__visual-sub">フォーム入力後、メール認証するだけですぐ公開。</div>
                        </div>
                    </div>
                    <div class="hero__visual-row">
                        <div class="hero__visual-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="hero__visual-label">ログイン不要でかんたん管理</div>
                            <div class="hero__visual-sub">メールリンクから求人の編集・応募確認ができます。</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 料金 --}}
<section style="background:#f8faff;border-top:1px solid #e3eaf8;border-bottom:1px solid #e3eaf8;padding:36px 0;">
    <div class="container">
        <p style="text-align:center;font-size:0.78rem;font-weight:800;color:var(--color-primary);letter-spacing:.08em;margin-bottom:20px;">PRICING</p>
        <div class="row justify-content-center g-3">
            <div class="col-6 col-md-3">
                <div style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);border:2px solid #f57c00;border-radius:12px;padding:20px 16px;text-align:center;">
                    <p style="font-size:0.75rem;color:#e65100;font-weight:700;margin-bottom:6px;">掲載費</p>
                    <p style="font-size:2.2rem;font-weight:900;color:#e65100;margin:0;">¥0</p>
                    <p style="font-size:0.72rem;color:#e65100;opacity:.7;margin-top:4px;">初期費用なし</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);border:2px solid #f57c00;border-radius:12px;padding:20px 16px;text-align:center;">
                    <p style="font-size:0.75rem;color:#e65100;font-weight:700;margin-bottom:6px;">月額費用</p>
                    <p style="font-size:2.2rem;font-weight:900;color:#e65100;margin:0;">¥0</p>
                    <p style="font-size:0.72rem;color:#e65100;opacity:.7;margin-top:4px;">固定費なし</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div style="background:#fff;border:1.5px solid var(--color-primary);border-radius:12px;padding:20px 16px;text-align:center;">
                    <p style="font-size:0.75rem;color:var(--color-primary);font-weight:700;margin-bottom:6px;">成果報酬（応募1件）</p>
                    <p style="font-size:1.7rem;font-weight:900;color:#1a1a2e;margin:0;">¥3,000</p>
                    <p style="font-size:0.72rem;color:#aaa;margin-top:4px;margin-bottom:0;"><strong>有効応募のみ課金</strong>・税別</p>
                    <p style="font-size:0.68rem;color:#bbb;margin-top:1px;">※有効応募とは、重複・スパムを除いた応募のことです</p>
                </div>
            </div>
        </div>
        <p style="text-align:center;font-size:0.78rem;color:#888;margin-top:16px;margin-bottom:4px;">
            ※ 現在はモニター期間中のため、掲載費・成果報酬すべて<strong>無料</strong>でご利用いただけます
            （{{ $monitorCutoff->format('Y年m月d日') }}までの登録・<strong style="color:#e65100;">有効応募3件まで無料</strong>）
        </p>
        <p style="text-align:center;font-size:0.75rem;color:#aaa;margin-top:6px;margin-bottom:0;">
            ※ 振込手数料はご負担ください　※ 掲載の継続・停止はいつでも管理ページから操作できます
        </p>
    </div>
</section>

{{-- 補足ストリップ --}}
<div class="strip">
    <div class="container">
        <div class="strip__items">
            <span class="strip__item"><i class="bi bi-check-circle-fill"></i>介護・福祉業界向け(沖縄エリア)</span>
            <span class="strip__item"><i class="bi bi-check-circle-fill"></i>LINEでかんたん応募対応</span>
            <span class="strip__item"><i class="bi bi-check-circle-fill"></i>掲載から応募まで、シンプルに</span>
        </div>
    </div>
</div>

{{-- ② サービス特長 --}}
<section class="merits">
    <div class="container">
        <div class="text-center">
            <p class="merits__eyebrow">WHY CARE ENTRY</p>
            <h2 class="merits__heading">選ばれる理由</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card__icon"><i class="bi bi-currency-yen"></i></div>
                    <p class="merit-card__title">初期費用・月額費用0円</p>
                    <p class="merit-card__body">掲載にかかる固定費用は一切ありません。応募が届いたときだけ費用が発生する、シンプルな成果型の課金体系です。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card__icon"><i class="bi bi-toggle-on"></i></div>
                    <p class="merit-card__title">継続・停止は掲載主が決める</p>
                    <p class="merit-card__body">管理ページからいつでも掲載の継続・停止を操作できます。気づいたら課金が続いていた、ということはありません。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card__icon"><i class="bi bi-funnel-fill"></i></div>
                    <p class="merit-card__title">条件マッチングで無駄な応募を減らす</p>
                    <p class="merit-card__body">応募の際、掲載主様の募集条件と求職者の方の希望条件を自動で照合。条件が合った応募のみメールでお届けします。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card__icon"><i class="bi bi-stars"></i></div>
                    <p class="merit-card__title">AIが求人文を自動生成</p>
                    <p class="merit-card__body">チェックを選ぶだけでAIが魅力的な求人タイトルと本文を自動作成。専門的な文章力がなくても、伝わる求人が作れます。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card__icon"><i class="bi bi-chat-dots-fill"></i></div>
                    <p class="merit-card__title">LINE応募で応募率アップ</p>
                    <p class="merit-card__body">求職者が使い慣れたLINEから応募できる仕組みで、応募へのハードルを下げます。フォーム応募にも対応しています。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ③ 掲載の流れ --}}
<section class="steps" id="steps">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center">
                    <p class="steps__eyebrow">HOW IT WORKS</p>
                    <h2 class="steps__heading">掲載の流れ</h2>
                </div>
                <div class="step">
                    <div class="step__num">1</div>
                    <div>
                        <p class="step__title">フォームに求人情報を入力</p>
                        <p class="step__body">会社名・勤務地・雇用形態・給与などを入力します。難しい設定は不要です。</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step__num">2</div>
                    <div>
                        <p class="step__title">AIが求人タイトル・本文を自動生成</p>
                        <p class="step__body">入力内容をもとにAIが求人文を自動作成。そのまま使っても、手直しして使っても構いません。</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step__num">3</div>
                    <div>
                        <p class="step__title">メール認証して掲載開始</p>
                        <p class="step__body">登録メールに届くリンクを確認するだけで掲載がスタート。当日中に公開されます。</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step__num">4</div>
                    <div>
                        <p class="step__title">応募が届いたら通知</p>
                        <p class="step__body">応募が届くとメールで通知。応募者情報を確認して採用活動を進められます。</p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('jobs.create') }}" class="hero__cta-primary">
                        <i class="bi bi-plus-circle-fill"></i>今すぐ無料で掲載を始める
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 無料モニター募集バナー --}}
<section style="background:linear-gradient(90deg,#fff8e1,#fff3cd);border-top:3px solid #f9a825;border-bottom:3px solid #f9a825;padding:28px 0;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <p style="font-size:0.72rem;font-weight:800;color:#f57f17;letter-spacing:.08em;margin-bottom:4px;">
                    <i class="bi bi-star-fill me-1"></i>期間限定・無料モニター募集中
                    &nbsp;〜&nbsp;{{ $monitorCutoff->format('Y年m月d日') }}まで
                </p>
                <p style="font-size:1.05rem;font-weight:900;color:#1a1a2e;margin-bottom:6px;">
                    無料でご利用いただける「モニター企業」を募集しています
                </p>
                <p style="font-size:0.86rem;color:#555;margin:0;">
                    掲載開始から<strong>3か月間または有効応募3件</strong>まで、掲載費・成果課金を一切いただかずサービスをお試しいただけます。<br>
                    ご利用のご感想やフィードバックをいただける企業様が対象です。
                </p>
            </div>
            <a href="{{ route('jobs.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;background:#f9a825;color:#fff;font-weight:800;font-size:0.95rem;padding:14px 28px;border-radius:30px;text-decoration:none;white-space:nowrap;flex-shrink:0;">
                <i class="bi bi-plus-circle-fill"></i>モニターとして掲載する
            </a>
        </div>
    </div>
</section>

{{-- ④ 最終CTA --}}
<section class="cta-band">
    <div class="container">
        <h2 class="cta-band__title">まずは無料で掲載し、反応を確認してみませんか</h2>
        <p class="cta-band__body">難しい設定なし。フォームに入力するだけで、最短当日から掲載できます。<br>初期費用・月額費用は一切かかりません。</p>
        <div class="cta-band__actions">
            <a href="{{ route('jobs.create') }}" class="cta-band__btn-primary">
                <i class="bi bi-plus-circle-fill"></i>無料で求人を掲載する
            </a>
            <a href="#steps" class="cta-band__btn-secondary">
                <i class="bi bi-list-ol"></i>掲載の流れを見る
            </a>
        </div>
        <p class="mt-4 mb-0" style="font-size:0.76rem;opacity:.7;">
            <i class="bi bi-shield-check me-1"></i>「試してみよう」という気持ちで始めていただけます
        </p>
    </div>
</section>

{{-- フッター --}}
<footer class="footer">
    <div class="container">
        <div class="mb-3">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">介護・福祉専門 求人掲載サービス【対応エリア:沖縄県】</span>
        </div>
        <div class="footer__links">
            <a href="{{ route('home') }}">お仕事を探している方</a>
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
