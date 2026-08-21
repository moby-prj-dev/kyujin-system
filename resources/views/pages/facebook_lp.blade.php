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
    <!-- Meta Pixel -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.facebook.pixel_id', '') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ config('services.facebook.pixel_id', '') }}&ev=PageView&noscript=1"/></noscript>
    <!-- End Meta Pixel -->
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>求人を出しても応募が来ない方へ｜ケアエントリー【介護・福祉向け・沖縄エリア対応】</title>
    <meta name="description" content="介護・福祉事業者向け求人掲載サービス「ケアエントリー」(対応エリア:沖縄県)。応募前に条件を確認する仕組みでミスマッチを防ぎます。掲載無料・応募があった時のみ課金・採用時0円。">
    <meta name="robots" content="noindex">
    <meta property="og:title" content="求人を出しても応募が来ない方へ｜ケアエントリー">
    <meta property="og:description" content="介護・福祉事業者向け求人掲載サービス(対応エリア:沖縄県)。掲載無料・応募があった時のみ課金・採用時0円。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://care-entry.net/fb">
    <meta property="og:image" content="https://care-entry.net/images/ogp.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ja_JP">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #0d47a1;
            --primary-light: #e8f0fe;
            --primary-mid: #c5d8f8;
            --accent: #f57f17;
            --accent-light: #fff8e1;
            --text: #1a1a2e;
            --muted: #6c757d;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--text);
            margin: 0;
            background: #fff;
            font-size: 16px;
            line-height: 1.7;
        }

        /* ── ヘッダー ── */
        .site-header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 12px 0;
            text-align: center;
        }
        .site-header img { height: 40px; }

        /* ── モニターバー ── */
        .monitor-bar {
            background: linear-gradient(90deg,#fff8e1,#fff3cd);
            border-bottom: 3px solid #f9a825;
            padding: 10px 16px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 800;
            color: #f57f17;
        }

        /* ── ヒーロー ── */
        .hero {
            background: linear-gradient(150deg, #0d47a1 0%, #1a73e8 60%, #42a5f5 100%);
            color: #fff;
            padding: 56px 16px 48px;
            text-align: center;
        }
        .hero__tag {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 16px;
            margin-bottom: 20px;
            letter-spacing: .03em;
        }
        .hero__title {
            font-size: clamp(1.55rem, 5vw, 2.4rem);
            font-weight: 900;
            line-height: 1.45;
            margin-bottom: 18px;
        }
        .hero__title em {
            font-style: normal;
            background: linear-gradient(90deg,#ffe082,#ffcc02);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero__sub {
            font-size: 1rem;
            opacity: .9;
            margin-bottom: 32px;
            line-height: 1.8;
        }
        .hero__cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            color: var(--primary);
            font-size: 1.05rem;
            font-weight: 900;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 24px rgba(0,0,0,0.18);
            transition: .2s;
        }
        .hero__cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.22);
            color: var(--primary-dark);
        }
        .hero__note {
            margin-top: 14px;
            font-size: 0.78rem;
            opacity: .75;
        }
        .hero__badges {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .hero__badge-item {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 5px 14px;
        }

        /* ── 共感セクション ── */
        .pain {
            background: #f8f9fc;
            padding: 56px 16px;
            text-align: center;
        }
        .section-eyebrow {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .section-title {
            font-size: clamp(1.2rem, 4vw, 1.75rem);
            font-weight: 900;
            color: var(--text);
            line-height: 1.4;
            margin-bottom: 28px;
        }
        .pain__list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 480px;
            margin: 0 auto 28px;
            text-align: left;
        }
        .pain__item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #fff;
            border: 1.5px solid #e0e7f7;
            border-radius: 12px;
            padding: 16px 18px;
            font-size: 0.97rem;
            font-weight: 600;
        }
        .pain__item-icon {
            font-size: 1.2rem;
            color: #e53935;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .pain__resolve {
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary);
            background: var(--primary-light);
            border-radius: 10px;
            padding: 14px 20px;
            max-width: 480px;
            margin: 0 auto;
        }

        /* ── 解決・特長 ── */
        .merits {
            background: #fff;
            padding: 56px 16px;
        }
        .merit-item {
            display: flex;
            gap: 16px;
            padding: 22px 0;
            border-bottom: 1px solid #eef1f9;
        }
        .merit-item:last-child { border-bottom: none; }
        .merit-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .merit-title {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        .merit-body {
            font-size: 0.88rem;
            color: #555;
            line-height: 1.75;
            margin: 0;
        }
        .merit-arrow {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 700;
            margin-top: 6px;
        }

        /* ── オファー ── */
        .offer {
            background: linear-gradient(135deg, var(--accent-light) 0%, #fff8e1 100%);
            border-top: 3px solid #f9a825;
            border-bottom: 3px solid #f9a825;
            padding: 52px 16px;
            text-align: center;
        }
        .offer__badge {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 800;
            border-radius: 20px;
            padding: 5px 18px;
            margin-bottom: 16px;
            letter-spacing: .04em;
        }
        .offer__title {
            font-size: clamp(1.3rem, 4vw, 1.9rem);
            font-weight: 900;
            color: var(--text);
            line-height: 1.4;
            margin-bottom: 20px;
        }
        .offer__points {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            margin: 0 auto 28px;
            text-align: left;
        }
        .offer__point {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.97rem;
            font-weight: 700;
        }
        .offer__point i {
            color: var(--accent);
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .offer__cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--accent);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 900;
            padding: 17px 42px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(245,127,23,0.35);
            transition: .2s;
        }
        .offer__cta:hover {
            background: #e65100;
            color: #fff;
            transform: translateY(-2px);
        }
        .offer__note {
            margin-top: 12px;
            font-size: 0.76rem;
            color: #888;
        }

        /* ── 掲載の流れ ── */
        .steps {
            background: #f8faff;
            padding: 56px 16px;
            border-top: 1px solid var(--primary-mid);
        }
        .step {
            display: flex;
            gap: 15px;
            padding: 18px 0;
            border-bottom: 1px solid #e8eeff;
            max-width: 520px;
            margin: 0 auto;
        }
        .step:last-of-type { border-bottom: none; }
        .step__num {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 900;
            flex-shrink: 0;
        }
        .step__title { font-size: 0.95rem; font-weight: 800; margin-bottom: 4px; }
        .step__body { font-size: 0.85rem; color: #555; line-height: 1.7; margin: 0; }

        /* ── 最終CTA ── */
        .cta-final {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 64px 16px;
            text-align: center;
        }
        .cta-final__title {
            font-size: clamp(1.2rem, 4vw, 1.75rem);
            font-weight: 900;
            line-height: 1.45;
            margin-bottom: 12px;
        }
        .cta-final__body {
            font-size: 0.95rem;
            opacity: .88;
            margin-bottom: 32px;
            line-height: 1.8;
        }
        .cta-final__btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            color: var(--primary);
            font-size: 1.05rem;
            font-weight: 900;
            padding: 17px 42px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 24px rgba(0,0,0,0.18);
            transition: .2s;
        }
        .cta-final__btn:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
            transform: translateY(-2px);
        }
        .cta-final__note {
            margin-top: 16px;
            font-size: 0.76rem;
            opacity: .7;
        }

        /* ── フッター ── */
        .footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 28px 16px;
            font-size: 0.8rem;
            text-align: center;
        }
        .footer a { color: #aaa; text-decoration: none; }
        .footer a:hover { color: #fff; }
        .footer__links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 4px 16px;
            margin-bottom: 12px;
        }

        @media (min-width: 640px) {
            .hero { padding: 72px 24px 60px; }
            .pain { padding: 72px 24px; }
            .merits { padding: 72px 24px; }
            .offer { padding: 68px 24px; }
            .steps { padding: 72px 24px; }
            .cta-final { padding: 80px 24px; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- モニターバー --}}
@if(now()->lte($monitorCutoff))
<div class="monitor-bar">
    <i class="bi bi-star-fill me-1"></i>無料モニター募集中
    &nbsp;〜&nbsp;{{ $monitorCutoff->format('Y年m月d日') }}まで
    &nbsp;｜&nbsp;3か月間 または 有効応募3件まで成果報酬0円
</div>
@endif

{{-- ヘッダー --}}
<header class="site-header">
    <a href="{{ route('home') }}">
        <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
    </a>
</header>

{{-- ① ファーストビュー --}}
<section class="hero">
    <div class="hero__tag">
        <i class="bi bi-building me-1"></i>介護・福祉事業者向け 求人掲載サービス【対応エリア:沖縄県】
    </div>
    <h1 class="hero__title">
        求人を出しても<br>応募が来ない…<br>
        その悩み、<em>仕組みで変えませんか？</em>
    </h1>
    <p class="hero__sub">
        掲載は30秒で完了。<br>
        まずは試すだけでもOKです。
    </p>
    <div class="hero__badges">
        <span class="hero__badge-item"><i class="bi bi-check-circle-fill me-1"></i>掲載費0円スタート</span>
        <span class="hero__badge-item"><i class="bi bi-check-circle-fill me-1"></i>成果報酬型</span>
        <span class="hero__badge-item"><i class="bi bi-check-circle-fill me-1"></i>介護・福祉特化</span>
    </div>
    <a href="{{ route('jobs.create') }}" class="hero__cta"
       onclick="@if(app()->environment('production'))fbq('track', 'Lead');@endif">
        <i class="bi bi-plus-circle-fill"></i>無料で掲載してみる
    </a>
    <p class="hero__note">
        <i class="bi bi-shield-check me-1"></i>クレジットカード不要・途中でやめてもOK
    </p>
</section>

{{-- ② 共感 --}}
<section class="pain">
    <div class="container-sm">
        <p class="section-eyebrow">PAIN POINTS</p>
        <h2 class="section-title">こんなお悩みありませんか？</h2>
        <div class="pain__list">
            <div class="pain__item">
                <span class="pain__item-icon"><i class="bi bi-x-circle-fill"></i></span>
                <span>求人を出しても応募が来ない</span>
            </div>
            <div class="pain__item">
                <span class="pain__item-icon"><i class="bi bi-x-circle-fill"></i></span>
                <span>応募は来るけど条件が合わない</span>
            </div>
            <div class="pain__item">
                <span class="pain__item-icon"><i class="bi bi-x-circle-fill"></i></span>
                <span>面接まで進まない・すっぽかされる</span>
            </div>
            <div class="pain__item">
                <span class="pain__item-icon"><i class="bi bi-x-circle-fill"></i></span>
                <span>採用コストが高くて割に合わない</span>
            </div>
        </div>
        <div class="pain__resolve">
            <i class="bi bi-lightbulb-fill me-2" style="color:#f9a825;"></i>
            その原因、<strong>仕組みで解決</strong>できます。
        </div>
    </div>
</section>

{{-- ③ 解決・特長 --}}
<section class="merits">
    <div class="container-sm">
        <p class="section-eyebrow text-center">FEATURES</p>
        <h2 class="section-title text-center">ケアエントリーの特徴</h2>
        <div style="max-width:560px;margin:0 auto;">
            <div class="merit-item">
                <div class="merit-icon"><i class="bi bi-funnel-fill"></i></div>
                <div>
                    <p class="merit-title">応募前に条件を確認する仕組み</p>
                    <p class="merit-arrow">→ 無駄な応募を減らし、ミスマッチを防ぎます</p>
                    <p class="merit-body">求職者の希望条件と御社の募集条件を事前にすり合わせ。条件が合った応募だけがメールで届きます。</p>
                </div>
            </div>
            <div class="merit-item">
                <div class="merit-icon"><i class="bi bi-currency-yen"></i></div>
                <div>
                    <p class="merit-title">初期費用・掲載費 0円</p>
                    <p class="merit-arrow">→ 応募があった分だけのシンプルな料金</p>
                    <p class="merit-body">固定費なし。有効な応募が届いたときだけ課金（1件 ¥3,000）。リスクなく始められます。</p>
                </div>
            </div>
            <div class="merit-item">
                <div class="merit-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                    <p class="merit-title">介護・福祉×地域特化(対応エリア:沖縄県)</p>
                    <p class="merit-arrow">→ 必要な人材に届きやすい設計</p>
                    <p class="merit-body">総合求人サイトと違い、介護・福祉分野を探している求職者に絞って届けます(現在は沖縄エリアの掲載)。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ④ オファー（モニター期間中のみ表示） --}}
@if(now()->lte($monitorCutoff))
<section class="offer">
    <div class="container-sm text-center">
        <span class="offer__badge">
            <i class="bi bi-star-fill me-1"></i>期間限定・無料モニター募集中
        </span>
        <h2 class="offer__title">
            まずは反応を<br>確認していただけます
        </h2>
        <div class="offer__points">
            <div class="offer__point">
                <i class="bi bi-check-circle-fill"></i>
                <span>3か月間は掲載費・成果報酬 <strong>すべて無料</strong></span>
            </div>
            <div class="offer__point">
                <i class="bi bi-check-circle-fill"></i>
                <span>有効応募 <strong>3件まで</strong> 無料でご利用OK</span>
            </div>
            <div class="offer__point">
                <i class="bi bi-check-circle-fill"></i>
                <span>途中でやめても <strong>費用は一切かかりません</strong></span>
            </div>
        </div>
        <a href="{{ route('jobs.create') }}" class="offer__cta"
           onclick="@if(app()->environment('production'))fbq('track', 'Lead');@endif">
            <i class="bi bi-plus-circle-fill"></i>無料で求人を掲載する
        </a>
        <p class="offer__note">
            ※ モニター期間：{{ $monitorCutoff->format('Y年m月d日') }}まで登録の方が対象
        </p>
    </div>
</section>
@endif

{{-- ⑤ 掲載の流れ --}}
<section class="steps">
    <div class="container-sm">
        <p class="section-eyebrow text-center">HOW IT WORKS</p>
        <h2 class="section-title text-center">掲載の流れ（30秒で完了）</h2>
        <div class="step">
            <div class="step__num">1</div>
            <div>
                <p class="step__title">フォームに求人情報を入力</p>
                <p class="step__body">職種・勤務地・給与などを入力するだけ。難しい操作は不要です。</p>
            </div>
        </div>
        <div class="step">
            <div class="step__num">2</div>
            <div>
                <p class="step__title">AIが求人タイトル・本文を自動生成</p>
                <p class="step__body">入力内容をもとにAIが求人文を自動作成。そのまま使っても、手直ししてもOK。</p>
            </div>
        </div>
        <div class="step">
            <div class="step__num">3</div>
            <div>
                <p class="step__title">メール認証して掲載開始</p>
                <p class="step__body">届いたメールのリンクをクリックするだけ。最短当日に公開されます。</p>
            </div>
        </div>
        <div class="step">
            <div class="step__num">4</div>
            <div>
                <p class="step__title">応募が届いたらメールで通知</p>
                <p class="step__body">条件が合った応募が届いたときだけメールでお知らせ。ログイン不要で確認できます。</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('jobs.create') }}" class="hero__cta"
               style="display:inline-flex;align-items:center;gap:10px;background:var(--primary);color:#fff;font-size:1rem;font-weight:900;padding:15px 36px;border-radius:50px;text-decoration:none;box-shadow:0 6px 20px rgba(26,115,232,0.3);"
               onclick="@if(app()->environment('production'))fbq('track', 'Lead');@endif">
                <i class="bi bi-plus-circle-fill"></i>今すぐ無料で掲載を始める
            </a>
        </div>
    </div>
</section>

{{-- ⑥ 最終CTA --}}
<section class="cta-final">
    <div class="container-sm">
        <h2 class="cta-final__title">
            まずは無料で掲載してみませんか？
        </h2>
        <p class="cta-final__body">
            難しい設定なし。フォームに入力するだけで、<br>
            最短当日から掲載できます。<br>
            初期費用・月額費用は一切かかりません。
        </p>
        <a href="{{ route('jobs.create') }}" class="cta-final__btn"
           onclick="@if(app()->environment('production'))fbq('track', 'Lead');@endif">
            <i class="bi bi-plus-circle-fill"></i>無料で求人を掲載する
        </a>
        <p class="cta-final__note">
            <i class="bi bi-shield-check me-1"></i>「試してみよう」という気持ちで始めていただけます
        </p>
    </div>
</section>

{{-- フッター --}}
<footer class="footer">
    <div class="container">
        <div class="mb-2">
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
