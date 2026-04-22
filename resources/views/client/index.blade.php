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
    <title>介護・福祉の求人掲載｜Care Entry（ケアエントリー）</title>
    <meta name="description" content="Care Entryは介護・福祉専門の求人サービスです。応募につながる導線設計を重視。掲載費0円スタート、成果型の課金体系で始めやすい求人掲載を提供します。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --brand-primary:      #1a73e8;
            --brand-primary-dark: #0d47a1;
            --brand-blue-light:   #e8f0fe;
            --brand-blue-mid:     #c5d8f8;
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
        .client-nav {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .client-nav .nav-logo img { height: 44px; }
        .client-nav .nav-link-seeker {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
            transition: .15s;
        }
        .client-nav .nav-link-seeker:hover {
            background: #f8f9fa;
            color: var(--text);
        }

        /* =====================
           ファーストビュー
        ===================== */
        .client-hero {
            background: linear-gradient(140deg, #e8f0fe 0%, #f3f5ff 60%, #fafbff 100%);
            padding: 64px 0 52px;
        }
        .client-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--brand-primary);
            font-size: 0.82rem;
            font-weight: 700;
            border: 1.5px solid var(--brand-blue-mid);
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 20px;
        }
        .client-hero-title {
            font-size: clamp(1.5rem, 4vw, 2.1rem);
            font-weight: 900;
            line-height: 1.5;
            color: #0d1a2e;
            margin-bottom: 16px;
        }
        .client-hero-sub {
            font-size: 0.97rem;
            color: #3a4a5a;
            line-height: 1.85;
            margin-bottom: 28px;
        }
        .client-feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 32px;
        }
        .client-feature-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1.5px solid var(--brand-blue-mid);
            border-radius: 20px;
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--brand-primary);
            padding: 5px 14px;
        }
        .client-cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-client-primary {
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
        .btn-client-primary:hover {
            background: var(--brand-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-client-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--brand-primary);
            border: 2px solid var(--brand-primary);
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 700;
            padding: 12px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-client-outline:hover {
            background: var(--brand-blue-light);
            color: var(--brand-primary);
        }
        .client-hero-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 12px;
        }

        /* ヒーロー ビジュアル側カード */
        .client-hero-visual {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(26,115,232,.1);
            border: 1.5px solid var(--brand-blue-mid);
            padding: 28px;
            margin-top: 8px;
        }
        .client-visual-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f0f4fc;
        }
        .client-visual-row:first-child { padding-top: 0; }
        .client-visual-row:last-child  { border-bottom: none; padding-bottom: 0; }
        .client-visual-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--brand-blue-light);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .client-visual-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1a1a1a;
        }
        .client-visual-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* =====================
           補足ストリップ
        ===================== */
        .client-supplement-strip {
            background: var(--brand-blue-light);
            border-top: 1px solid var(--brand-blue-mid);
            border-bottom: 1px solid var(--brand-blue-mid);
            padding: 14px 0;
        }
        .supplement-items {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px 24px;
        }
        .supplement-item {
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* =====================
           サービス特長
        ===================== */
        .client-merits-section {
            background: #fff;
            padding: 64px 0;
        }
        .section-eyebrow {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--brand-primary);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .section-heading {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900;
            line-height: 1.4;
            color: #0d1a2e;
            margin-bottom: 40px;
        }
        .merit-card {
            background: #fff;
            border: 1.5px solid var(--brand-blue-mid);
            border-radius: 14px;
            padding: 28px 24px;
            height: 100%;
            transition: .2s;
        }
        .merit-card:hover {
            box-shadow: 0 4px 20px rgba(26,115,232,.1);
            transform: translateY(-2px);
        }
        .merit-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--brand-blue-light);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 14px;
        }
        .merit-card h5 {
            font-size: 0.97rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #0d1a2e;
        }
        .merit-card p {
            font-size: 0.87rem;
            color: #555;
            line-height: 1.75;
            margin: 0;
        }

        /* =====================
           掲載の流れ
        ===================== */
        .client-steps-section {
            background: #f8faff;
            padding: 64px 0;
            border-top: 1px solid var(--brand-blue-mid);
        }
        .step-item {
            display: flex;
            gap: 16px;
            padding: 20px 0;
            border-bottom: 1px solid #e8eeff;
        }
        .step-item:last-child { border-bottom: none; }
        .step-num {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--brand-primary);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-content h5 {
            font-size: 0.97rem;
            font-weight: 800;
            margin-bottom: 4px;
            color: #0d1a2e;
        }
        .step-content p {
            font-size: 0.87rem;
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* =====================
           最終 CTA
        ===================== */
        .client-final-cta {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            color: #fff;
            padding: 64px 0;
            text-align: center;
        }
        .client-final-cta h2 {
            font-size: clamp(1.2rem, 3vw, 1.65rem);
            font-weight: 900;
            margin-bottom: 10px;
        }
        .client-final-cta .cta-sub {
            opacity: .88;
            font-size: 0.95rem;
            margin-bottom: 32px;
        }
        .btn-cta-white {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--brand-primary);
            border-radius: 30px;
            font-size: 1.05rem;
            font-weight: 800;
            padding: 16px 40px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-cta-white:hover {
            background: var(--brand-blue-light);
            color: var(--brand-primary);
            transform: translateY(-2px);
        }
        .btn-cta-outline-white {
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
        .btn-cta-outline-white:hover {
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        /* =====================
           フッター
        ===================== */
        .client-footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 36px 0;
            font-size: 0.84rem;
            text-align: center;
        }
        .client-footer a { color: #aaa; text-decoration: none; }
        .client-footer a:hover { color: #fff; }
        .client-footer .footer-links {
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
            .client-hero { padding: 44px 0 36px; }
            .client-hero-visual { margin-top: 32px; }
            .client-merits-section { padding: 48px 0; }
            .client-steps-section { padding: 48px 0; }
            .client-final-cta { padding: 48px 0; }
        }
        @media (min-width: 992px) {
            .client-hero { padding: 80px 0 64px; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ナビ --}}
<nav class="client-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="nav-logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('home') }}" class="nav-link-seeker">
            <i class="bi bi-search me-1"></i>お仕事を探している方はこちら
        </a>
    </div>
</nav>

{{-- ① ファーストビュー --}}
<section class="client-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="client-hero-badge">
                    <i class="bi bi-building"></i>介護・福祉専門の求人掲載サービス
                </div>
                <h1 class="client-hero-title">
                    求人を出しても<br>
                    応募が来ない——<br>
                    そのお悩みに。
                </h1>
                <p class="client-hero-sub">
                    ケアエントリーは、応募につながる導線設計を重視した<br>
                    介護・福祉専門の求人サービスです。<br>
                    掲載して終わりではなく、求職者が動きやすい仕組みを目指しています。
                </p>
                <div class="client-feature-list">
                    <span class="client-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>掲載費0円スタート
                    </span>
                    <span class="client-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>応募につながる導線設計
                    </span>
                    <span class="client-feature-chip">
                        <i class="bi bi-check-circle-fill"></i>介護・福祉に特化
                    </span>
                </div>
                <div class="client-cta-group">
                    <a href="{{ route('jobs.create') }}" class="btn-client-primary">
                        <i class="bi bi-plus-circle-fill"></i>無料で掲載を始める
                    </a>
                    <a href="#steps" class="btn-client-outline">
                        <i class="bi bi-list-ol"></i>掲載の流れを見る
                    </a>
                </div>
                <p class="client-hero-note">
                    <i class="bi bi-shield-check me-1"></i>初期費用・月額費用は一切かかりません
                </p>
            </div>
            <div class="col-lg-6">
                <div class="client-hero-visual">
                    <div class="client-visual-row">
                        <div class="client-visual-icon"><i class="bi bi-currency-yen"></i></div>
                        <div>
                            <div class="client-visual-label">掲載費0円で始められる</div>
                            <div class="client-visual-desc">初期費用・月額費用なし。応募が届いたときだけ課金。</div>
                        </div>
                    </div>
                    <div class="client-visual-row">
                        <div class="client-visual-icon"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                        <div>
                            <div class="client-visual-label">応募につながる導線設計</div>
                            <div class="client-visual-desc">LINE応募対応で求職者のハードルを下げる仕組み。</div>
                        </div>
                    </div>
                    <div class="client-visual-row">
                        <div class="client-visual-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div>
                            <div class="client-visual-label">最短当日から掲載開始</div>
                            <div class="client-visual-desc">フォーム入力後、メール認証するだけですぐ公開。</div>
                        </div>
                    </div>
                    <div class="client-visual-row">
                        <div class="client-visual-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="client-visual-label">ログイン不要でかんたん管理</div>
                            <div class="client-visual-desc">メールリンクから求人の編集・確認ができます。</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 補足ストリップ --}}
<div class="client-supplement-strip">
    <div class="container">
        <div class="supplement-items">
            <span class="supplement-item">
                <i class="bi bi-check-circle-fill"></i>介護・福祉業界向け求人掲載
            </span>
            <span class="supplement-item">
                <i class="bi bi-check-circle-fill"></i>応募につながる設計を重視
            </span>
            <span class="supplement-item">
                <i class="bi bi-check-circle-fill"></i>掲載導線をシンプルに
            </span>
        </div>
    </div>
</div>

{{-- ② サービス特長 --}}
<section class="client-merits-section">
    <div class="container">
        <div class="text-center">
            <div class="section-eyebrow">WHY CARE ENTRY</div>
            <h2 class="section-heading">選ばれる理由</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card-icon"><i class="bi bi-currency-yen"></i></div>
                    <h5>初期費用・月額費用0円</h5>
                    <p>掲載にかかる固定費用は一切ありません。応募が届いたときだけ費用が発生する、シンプルな成果型の課金体系です。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card-icon"><i class="bi bi-chat-dots-fill"></i></div>
                    <h5>LINE応募で応募率アップ</h5>
                    <p>求職者が使い慣れたLINEから応募できる仕組みで、応募へのハードルを下げます。フォーム応募にも対応しています。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="merit-card">
                    <div class="merit-card-icon"><i class="bi bi-stars"></i></div>
                    <h5>AIが求人文を自動生成</h5>
                    <p>チェックを選ぶだけでAIが魅力的な求人タイトルと本文を自動作成。専門的な文章力がなくても、伝わる求人が作れます。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ③ 掲載の流れ --}}
<section class="client-steps-section" id="steps">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center">
                    <div class="section-eyebrow">HOW IT WORKS</div>
                    <h2 class="section-heading">掲載の流れ</h2>
                </div>
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-content">
                        <h5>フォームに求人情報を入力</h5>
                        <p>会社名・勤務地・雇用形態・給与などの基本情報を入力します。難しい設定は不要です。</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-content">
                        <h5>AIが求人タイトル・本文を自動生成</h5>
                        <p>入力内容をもとにAIが求人文を自動で作成します。そのまま使っても、手直しして使っても構いません。</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-content">
                        <h5>メール認証して掲載開始</h5>
                        <p>登録メールアドレスに届くリンクを確認するだけで掲載がスタート。当日中に公開されます。</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-content">
                        <h5>応募が届いたら通知</h5>
                        <p>求職者から応募が届くとメールで通知。応募者情報を確認して採用活動を進められます。</p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('jobs.create') }}" class="btn-client-primary">
                        <i class="bi bi-plus-circle-fill"></i>今すぐ無料で掲載を始める
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ④ 最終CTA --}}
<section class="client-final-cta">
    <div class="container">
        <h2>まずは無料で、掲載を試してみませんか</h2>
        <p class="cta-sub">難しい設定なし。フォームに入力するだけで、最短当日から掲載できます。</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('jobs.create') }}" class="btn-cta-white">
                <i class="bi bi-plus-circle-fill"></i>無料で掲載を始める
            </a>
            <a href="{{ route('jobs.create') }}" class="btn-cta-outline-white">
                <i class="bi bi-envelope"></i>掲載について相談する
            </a>
        </div>
        <p class="mt-4 mb-0" style="font-size:0.78rem;opacity:.7;">
            <i class="bi bi-shield-check me-1"></i>初期費用・月額費用は一切かかりません
        </p>
    </div>
</section>

{{-- フッター --}}
<footer class="client-footer">
    <div class="container">
        <div class="mb-3">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">介護・福祉専門の求人掲載サービス</span>
        </div>
        <div class="footer-links">
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
