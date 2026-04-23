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
    <meta name="description" content="沖縄の介護・福祉に特化した求人サービス。地域・職種から自分に合う職場を探せます。会員登録不要、LINEでそのまま応募できます。">
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
        .search__select {
            width: 100%;
            border: 1.5px solid #c8d8ec;
            border-radius: 8px;
            font-size: 0.93rem;
            padding: 10px 12px;
            color: var(--color-text);
            background-color: #fff;
            appearance: auto;
        }
        .search__select:focus {
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
        .search__detail-toggle {
            background: none;
            border: none;
            color: var(--color-primary);
            font-size: 0.84rem;
            font-weight: 700;
            padding: 0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 12px;
        }
        .search__detail { padding-top: 18px; border-top: 1px dashed #c8d8ec; margin-top: 18px; }
        .search__check-category { font-size: 0.78rem; font-weight: 700; color: var(--color-muted); margin-bottom: 8px; }
        .search__check-group { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 6px; margin-bottom: 14px; width: 100%; }
        .search__check-label {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.82rem; color: #333;
            background: #fff; border: 1.5px solid #c8d8ec; border-radius: 20px;
            padding: 5px 12px; cursor: pointer; transition: .15s;
        }
        .search__check-label input { display: none; }
        .search__check-label:has(input:checked) {
            background: var(--color-blue-light); border-color: var(--color-primary); color: var(--color-primary);
            font-weight: 700;
        }
        .search__selected-tags { display: flex; flex-wrap: wrap; gap: 4px; }
        .search__selected-tag { font-size: 0.75rem; background: var(--color-blue-light); color: var(--color-primary); border: 1px solid var(--color-blue-mid); border-radius: 12px; padding: 2px 10px; font-weight: 700; }
        .search__check-label:has(input:checked)::before {
            content: '✓';
            font-size: 0.75rem;
            margin-right: 2px;
        }
        .search__section-header {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin: 16px 0 10px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #d0e4f7;
        }
        .search__section-title {
            font-size: 0.87rem;
            font-weight: 800;
            color: #1a1a1a;
        }
        .search__section-note {
            font-size: 0.78rem;
            color: var(--color-muted);
        }
        .search__section-header--info .search__section-title { color: var(--color-muted); }
        .search__tabs-wrap { border-bottom: 2px solid #d0e4f7; margin-bottom: 12px; }
        .search__tabs-row { display: flex; flex-wrap: wrap; gap: 4px; padding-bottom: 3px; }
        .search__tab-btn {
            display: inline-flex; flex-direction: row; align-items: center;
            gap: 5px; white-space: nowrap;
            padding: 8px 14px; background: #f5f8fb;
            border: 1.5px solid #c8d8ec; border-bottom: none;
            border-radius: 6px 6px 0 0;
            font-size: 0.81rem; font-weight: 700; color: #666; cursor: pointer; transition: .15s;
        }
        .search__tab-btn:hover { background: #e8f0fb; color: var(--color-primary); }
        .search__tab-btn.active {
            background: var(--color-primary); color: #fff;
            border-color: var(--color-primary);
            margin-bottom: -2px; border-bottom: 2px solid var(--color-primary);
        }
        .search__tab-count {
            font-size: 0.7rem; font-weight: 700; color: #fff;
            background: transparent; border: 1.5px solid #fff; border-radius: 50%;
            width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; padding: 0;
        }
        .search__tab-btn:not(.active) .search__tab-count {
            color: var(--color-primary); border-color: var(--color-primary);
        }
        .search__tab-content { padding: 6px 0 4px; width: 100%; }
        .search__tab-content .tab-pane { width: 100%; }
        .search__acc-item { border-bottom: 1px solid #e8f0f8; overflow-anchor: none; }
        .search__acc-btn {
            display: flex; align-items: center;
            width: 100%; background: none; border: none;
            padding: 12px 2px; cursor: pointer;
            font-size: 0.86rem; font-weight: 700; color: #1a1a1a; text-align: left;
            gap: 6px;
        }
        .search__acc-btn:hover { color: var(--color-primary); }
        .search__acc-left { display: flex; align-items: center; gap: 8px; }
        .search__acc-badge {
            font-size: 0.72rem; font-weight: 700; color: #fff;
            background: var(--color-primary); border-radius: 10px; padding: 1px 8px;
        }
        .search__acc-btn--sticky {
            background: #fff; border-bottom: 1px solid #e8f0f8;
            margin: 0 -2px; padding: 12px 2px 12px 10px;
            border-left: 3px solid var(--color-primary);
        }
        .search__acc-btn--sticky .bi-check2-circle { color: var(--color-primary); font-size: 1rem; }
        .search__acc-btn--appeals {
            padding-left: 10px;
            border-left: 3px solid #f59e0b;
        }
        .search__acc-btn--appeals .bi-star { color: #f59e0b; font-size: 1rem; }
        .search__acc-btn--appeals .search__acc-note { color: #b45309; }
        .search__acc-btn--sticky.is-pinned {
            position: fixed; top: 0; z-index: 200;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            padding: 12px 16px;
        }
        .search__acc-close-row { display: flex; justify-content: center; padding-top: 8px; }
        .search__acc-close {
            background: none; border: 1.5px solid #c8d8ec; border-radius: 20px;
            color: var(--color-muted); font-size: 0.78rem; font-weight: 700;
            padding: 4px 16px; cursor: pointer; transition: .15s;
        }
        .search__acc-close:hover { border-color: var(--color-primary); color: var(--color-primary); }
        .search__acc-note { font-size: 0.75rem; color: var(--color-muted); font-weight: 400; }
        .search__acc-subnote { font-size: 0.75rem; color: var(--color-muted); margin-bottom: 8px; }

        /* 職種カテゴリーピル */
        /* 職種カテゴリータブ */
        .jt-cat-pills {
            display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 0;
            border-bottom: 2px solid #d0e4f7; margin-bottom: 14px;
            scrollbar-width: none;
        }
        .jt-cat-pills::-webkit-scrollbar { display: none; }
        .jt-cat-pill {
            flex-shrink: 0; display: inline-flex; align-items: center; gap: 5px;
            padding: 8px 15px; border: none; outline: none;
            border-bottom: 3px solid transparent; margin-bottom: -2px;
            background: transparent; font-size: 0.8rem; font-weight: 700; color: #888;
            cursor: pointer; transition: .15s; white-space: nowrap;
        }
        .jt-cat-pill:hover { color: var(--color-primary); background: #f5f8fc; }
        .jt-cat-pill.active { color: var(--color-primary); border-bottom-color: var(--color-primary); background: #f0f6ff; }
        .jt-cat-pill__count {
            font-size: 0.68rem; font-weight: 700; border-radius: 10px;
            padding: 0 6px; min-width: 18px; text-align: center;
            background: var(--color-primary); color: #fff;
        }
        .search__acc-chevron { transition: transform .2s; color: var(--color-primary); font-size: 1.1rem; font-weight: 900; }
        .search__acc-btn[aria-expanded="true"] .search__acc-chevron { transform: rotate(180deg); }
        .search__acc-body { padding: 4px 0 12px; }
        .search__footer {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }
        .search__footer .search__submit {
            width: auto;
            min-width: 200px;
            padding: 12px 28px;
        }

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
           お役立ち記事
        ===================== */
        .articles-section {
            background: #f8fafc;
            padding: 56px 0;
            border-top: 1px solid #e8f0e8;
        }
        .articles-section__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .articles-section__heading {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            font-weight: 900;
            color: #1a2a1a;
            margin-bottom: 24px;
        }
        .article-card-home {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 18px;
            height: 100%;
            text-decoration: none;
            color: var(--color-text);
            display: flex;
            flex-direction: column;
            transition: .2s;
        }
        .article-card-home:hover {
            border-color: var(--color-primary);
            box-shadow: 0 4px 16px rgba(26,115,232,.1);
            transform: translateY(-2px);
            color: var(--color-text);
        }
        .article-card-home__badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            width: fit-content;
        }
        .article-card-home__badge--industry      { background: #e8f0fb; color: var(--color-primary); }
        .article-card-home__badge--job_type      { background: #e8f5e9; color: #2e7d32; }
        .article-card-home__badge--area          { background: #fff3e0; color: #e65100; }
        .article-card-home__badge--qualification { background: #f3e5f5; color: #6a1b9a; }
        .article-card-home__badge--beginner      { background: #e0f7fa; color: #00695c; }
        .article-card-home__title {
            font-size: 0.93rem;
            font-weight: 800;
            line-height: 1.5;
            flex-grow: 1;
            margin-bottom: 10px;
        }
        .article-card-home__meta {
            font-size: 0.75rem;
            color: var(--color-muted);
        }
        .articles-section__more {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--color-primary);
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1.5px solid transparent;
            transition: .15s;
        }
        .articles-section__more:hover {
            border-bottom-color: var(--color-primary);
            color: var(--color-primary);
        }

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

{{-- 無料モニター告知バー --}}
<div style="background:#f9a825;padding:8px 0;text-align:center;">
    <a href="{{ route('client') }}" style="color:#fff;font-size:0.83rem;font-weight:700;text-decoration:none;">
        <i class="bi bi-star-fill me-1"></i>無料モニター企業を募集中！掲載開始から3か月間または有効応募3件まで無料でお試しいただけます
        <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

{{-- ① ファーストビュー --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero__badge">
                    <i class="bi bi-geo-alt-fill"></i>沖縄の介護・福祉求人
                </div>
                <h1 class="hero__title">
                    沖縄での介護・福祉の仕事探しを、<br>もっとシンプルに。
                </h1>
                <p class="hero__sub">
                    ケアエントリーは、沖縄の介護・福祉に特化した求人サービスです。<br>
                    地域・職種から、自分に合う職場を探せます。
                </p>
                <div class="hero__features">
                    <div style="display:flex;gap:8px;flex-wrap:nowrap;width:100%;">
                        <span class="hero__feature-item" style="flex:1;min-width:0;font-size:0.78rem;">
                            <i class="bi bi-check-circle-fill"></i>会員登録なしで応募OK
                        </span>
                        <span class="hero__feature-item" style="flex:1;min-width:0;font-size:0.78rem;background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;">
                            <i class="bi bi-chat-dots-fill"></i>LINEアプリで気軽に応募
                        </span>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:nowrap;width:100%;">
                        <span class="hero__feature-item" style="flex:1;min-width:0;font-size:0.78rem;">
                            <i class="bi bi-check-circle-fill"></i>30秒で応募完了
                        </span>
                        <span class="hero__feature-item" style="flex:1;min-width:0;font-size:0.78rem;">
                            <i class="bi bi-bullseye"></i>条件すり合わせで採用率アップ
                        </span>
                    </div>
                </div>
                <div class="hero__cta-group">
                    <a href="#search" class="hero__cta-primary">
                        <i class="bi bi-search"></i>求人を探す
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
        <p class="search__sub">エリア・職種・雇用形態・勤務条件から絞り込めます</p>
        <form action="{{ route('seo.jobs.okinawa') }}" method="GET" class="search__form">
            {{-- 基本条件：エリアのみ --}}
            <div class="row g-3 align-items-end">
                <div class="col-sm-8 col-md-5">
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
            </div>

            {{-- 詳細条件トグル --}}
            <div>
                <button type="button" class="search__detail-toggle" data-bs-toggle="collapse" data-bs-target="#searchDetail" aria-expanded="false">
                    <i class="bi bi-sliders me-1"></i>職種・雇用形態・勤務条件で絞り込む
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>

            {{-- 詳細条件 --}}
            <div class="collapse search__detail" id="searchDetail">

                @php
                    $jobTypeCount  = count(array_filter((array) request('job_types', [])));
                    $empTypeCount  = count(array_filter((array) request('employment_types', [])));
                    $condCount     = count(array_filter(array_map('intval', (array) request('condition_ids', []))));
                    $appealCount   = count(array_filter(array_map('intval', (array) request('appeal_ids', []))));
                    $requiredCount = $jobTypeCount + $empTypeCount + $condCount;
                    $activeTab = match(true) {
                        $empTypeCount > 0 => 'emp-types',
                        $condCount > 0    => 'conditions',
                        default           => 'job-types',
                    };
                    if ($jobTypeCount > 0) $activeTab = 'job-types';
                @endphp

                {{-- 必須項目 アコーディオン --}}
                <div class="search__acc-item">
                    <button class="search__acc-btn search__acc-btn--sticky" type="button"
                        data-bs-toggle="collapse" data-bs-target="#accRequired"
                        aria-expanded="{{ $requiredCount ? 'true' : 'false' }}">
                        <span class="search__acc-left">
                            <i class="bi bi-check2-circle me-1"></i>必須項目
                            <span class="search__acc-note">職種・雇用形態・勤務条件</span>
                        </span>
                        <i class="bi bi-chevron-down search__acc-chevron"></i>
                    </button>
                    <div class="collapse search__acc-body{{ $requiredCount ? ' show' : '' }}" id="accRequired">
                        {{-- 必須タブ --}}
                        <div class="search__tabs-wrap">
                            <div class="search__tabs-row">
                                <button class="search__tab-btn{{ $activeTab === 'job-types' ? ' active' : '' }}" type="button"
                                    data-tab-target="panel-job-types">
                                    <i class="bi bi-briefcase"></i>
                                    <span>職種</span>
                                    <span class="search__tab-count{{ $jobTypeCount ? '' : ' d-none' }}" id="cnt-job-types">{{ $jobTypeCount }}</span>
                                </button>
                                <button class="search__tab-btn{{ $activeTab === 'emp-types' ? ' active' : '' }}" type="button"
                                    data-tab-target="panel-emp-types">
                                    <i class="bi bi-person-badge"></i>
                                    <span>雇用形態</span>
                                    <span class="search__tab-count{{ $empTypeCount ? '' : ' d-none' }}" id="cnt-emp-types">{{ $empTypeCount }}</span>
                                </button>
                                <button class="search__tab-btn{{ $activeTab === 'conditions' ? ' active' : '' }}" type="button"
                                    data-tab-target="panel-conditions">
                                    <i class="bi bi-clock"></i>
                                    <span>勤務条件</span>
                                    <span class="search__tab-count{{ $condCount ? '' : ' d-none' }}" id="cnt-conditions">{{ $condCount }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- タブコンテンツ --}}
                        <div class="tab-content search__tab-content">
                            <div class="tab-pane fade{{ $activeTab === 'job-types' ? ' show active' : '' }}" id="panel-job-types">
                                <div id="tags-job-types" class="search__selected-tags" style="margin-bottom:8px;"></div>
                                @php
                                    $activeCat = collect($jobTypesByCategory)->keys()->first();
                                    foreach ($jobTypesByCategory as $cat => $types) {
                                        foreach ($types as $jt) {
                                            if (in_array($jt->slug, (array) request('job_types', []))) {
                                                $activeCat = $cat;
                                                break 2;
                                            }
                                        }
                                    }
                                @endphp
                                {{-- カテゴリーピル --}}
                                <div class="jt-cat-pills">
                                    @foreach($jobTypesByCategory as $category => $jobTypes)
                                        @php
                                            $cnt = collect($jobTypes)->filter(fn($jt) => in_array($jt->slug, (array) request('job_types', [])))->count();
                                        @endphp
                                        <button type="button"
                                            class="jt-cat-pill{{ $category === $activeCat ? ' active' : '' }}"
                                            data-cat="{{ $loop->index }}">
                                            {{ $category }}
                                            <span class="jt-cat-pill__count{{ $cnt ? '' : ' d-none' }}">{{ $cnt }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                {{-- カテゴリーごとのチェックグループ --}}
                                @foreach($jobTypesByCategory as $category => $jobTypes)
                                    <div class="jt-cat-group" data-cat="{{ $loop->index }}"
                                        {{ $category === $activeCat ? '' : 'style="display:none"' }}>
                                        <div class="search__check-group">
                                            @foreach($jobTypes as $jobType)
                                                <label class="search__check-label">
                                                    <input type="checkbox" name="job_types[]" value="{{ $jobType->slug }}"
                                                        @checked(in_array($jobType->slug, (array) request('job_types', [])))>
                                                    {{ $jobType->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade{{ $activeTab === 'emp-types' ? ' show active' : '' }}" id="panel-emp-types">
                                <div id="tags-emp-types" class="search__selected-tags" style="margin-bottom:8px;"></div>
                                <div class="search__check-group">
                                    @foreach($employmentTypes as $et)
                                        <label class="search__check-label">
                                            <input type="checkbox" name="employment_types[]" value="{{ $et->slug }}"
                                                @checked(in_array($et->slug, (array) request('employment_types', [])))>
                                            {{ $et->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="tab-pane fade{{ $activeTab === 'conditions' ? ' show active' : '' }}" id="panel-conditions">
                                <div id="tags-conditions" class="search__selected-tags" style="margin-bottom:8px;"></div>
                                @foreach($conditionsByCategory as $category => $conditions)
                                    <p class="search__check-category">{{ $category }}</p>
                                    <div class="search__check-group">
                                        @foreach($conditions as $cond)
                                            <label class="search__check-label">
                                                <input type="checkbox" name="condition_ids[]" value="{{ $cond->id }}"
                                                    @checked(in_array($cond->id, (array) request('condition_ids', [])))>
                                                {{ $cond->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="search__acc-close-row">
                            <button type="button" class="search__acc-close"
                                onclick="var col=this.closest('.search__acc-item').querySelector('.collapse');bootstrap.Collapse.getOrCreateInstance(col).hide();">
                                <i class="bi bi-chevron-up me-1"></i>閉じる
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 重視ポイント アコーディオン --}}
                <div class="search__acc-item">
                    <button class="search__acc-btn search__acc-btn--appeals" type="button"
                        data-bs-toggle="collapse" data-bs-target="#accAppeals"
                        aria-expanded="{{ $appealCount ? 'true' : 'false' }}">
                        <span class="search__acc-left">
                            <i class="bi bi-star me-1"></i>重視ポイント
                            <span class="search__acc-note">任意・掲載主への参考情報</span>
                        </span>
                        <i class="bi bi-chevron-down search__acc-chevron"></i>
                    </button>
                    <div class="collapse search__acc-body{{ $appealCount ? ' show' : '' }}" id="accAppeals">
                        <div id="appeals-tags" class="search__selected-tags" style="margin-bottom:8px;"></div>
                        @foreach($appealsByCategory as $category => $appeals)
                            <p class="search__check-category">{{ $category }}</p>
                            <div class="search__check-group">
                                @foreach($appeals as $appeal)
                                    <label class="search__check-label">
                                        <input type="checkbox" name="appeal_ids[]" value="{{ $appeal->id }}"
                                            @checked(in_array($appeal->id, (array) request('appeal_ids', [])))>
                                        {{ $appeal->name }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                        <div class="search__acc-close-row">
                            <button type="button" class="search__acc-close"
                                onclick="var col=this.closest('.search__acc-item').querySelector('.collapse');bootstrap.Collapse.getOrCreateInstance(col).hide();">
                                <i class="bi bi-chevron-up me-1"></i>閉じる
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 検索ボタン（常にフォーム末尾）--}}
            <div class="search__footer">
                <button type="submit" class="search__submit">
                    <i class="bi bi-search"></i>求人を検索する
                </button>
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
                <h2 class="points__heading">沖縄での介護・福祉の仕事探しを、<br>もっとシンプルにするために</h2>
<div class="points__cta-group">
                    <a href="#search" class="points__cta-primary">
                        <i class="bi bi-search"></i>求人を探す
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
                        <span class="points__item-icon" style="color:#06c755;"><i class="bi bi-chat-dots-fill"></i></span>
                        <div>
                            <p class="points__item-title">使い慣れたLINEから、そのまま応募できます</p>
                            <p class="points__item-body">アプリのダウンロード不要。普段使っているLINEで、そのまま応募できます。フォームが苦手な方にも安心です。</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ③-b お役立ち記事 --}}
@if($articles->isNotEmpty())
<section class="articles-section">
    <div class="container">
        <p class="articles-section__eyebrow">USEFUL INFO</p>
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="articles-section__heading mb-0">介護・福祉のお役立ち情報</h2>
            <a href="{{ route('articles.index') }}" class="articles-section__more">
                すべて見る <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @php
        $categoryMeta = [
            'industry'      => ['label' => '業界情報',    'icon' => 'bi-bar-chart-fill'],
            'job_type'      => ['label' => '職種別情報',  'icon' => 'bi-briefcase-fill'],
            'area'          => ['label' => 'エリア情報',  'icon' => 'bi-geo-alt-fill'],
            'qualification' => ['label' => '資格・研修',  'icon' => 'bi-award-fill'],
            'beginner'      => ['label' => '未経験・転職','icon' => 'bi-person-plus-fill'],
        ];
        @endphp
        <div class="row g-3">
            @foreach($articles as $article)
            @php $meta = $categoryMeta[$article->category] ?? ['label' => '', 'icon' => 'bi-file-text']; @endphp
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('articles.show', $article->slug) }}" class="article-card-home">
                    <span class="article-card-home__badge article-card-home__badge--{{ $article->category }}">
                        <i class="{{ $meta['icon'] }}"></i>{{ $meta['label'] }}
                    </span>
                    <p class="article-card-home__title">{{ $article->h1 }}</p>
                    <p class="article-card-home__meta mb-0">
                        <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at->format('Y年m月d日') }}
                    </p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


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
<script>
    // 詳細条件が選択済みの場合は詳細パネルを自動展開
    const detailEl = document.getElementById('searchDetail');
    if (detailEl && detailEl.querySelector('input[type="checkbox"]:checked')) {
        new bootstrap.Collapse(detailEl, { toggle: false }).show();
    }

    // タブ切替（バニラJS：Bootstrap tab pluginはcollapse内で初期化されないため）
    const tabPanels = ['panel-job-types', 'panel-emp-types', 'panel-conditions'];
    document.querySelectorAll('[data-tab-target]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.tabTarget;
            // 同じ行の他タブを非アクティブ
            btn.closest('.search__tabs-row').querySelectorAll('[data-tab-target]').forEach(b => b.classList.remove('active'));
            // すべてのパネルを非表示
            tabPanels.forEach(id => {
                const p = document.getElementById(id);
                if (p) { p.classList.remove('show', 'active'); }
            });
            // クリックしたタブとパネルをアクティブ
            btn.classList.add('active');
            const panel = document.getElementById(targetId);
            if (panel) { panel.classList.add('show', 'active'); }
        });
    });

    // ヘッダーボタンで閉じた場合もタイトルへスクロール
    ['accRequired', 'accAppeals'].forEach(id => {
        document.getElementById(id)?.addEventListener('hidden.bs.collapse', e => {
            if (e.target.id !== id) return;
            document.activeElement?.blur();
        });
    });


    // 必須項目ヘッダーをスクロール時に固定（position:fixed + placeholder）
    (function () {
        const btn  = document.querySelector('.search__acc-btn--sticky');
        if (!btn) return;
        const item = btn.closest('.search__acc-item');
        let ph     = null;

        function pin() {
            if (btn.classList.contains('is-pinned')) return;
            ph = document.createElement('div');
            ph.style.height = btn.offsetHeight + 'px';
            item.insertBefore(ph, btn);
            const r = item.getBoundingClientRect();
            btn.style.width = item.offsetWidth + 'px';
            btn.style.left  = r.left + 'px';
            btn.classList.add('is-pinned');
        }
        function unpin() {
            if (!btn.classList.contains('is-pinned')) return;
            btn.classList.remove('is-pinned');
            btn.style.width = '';
            btn.style.left  = '';
            ph?.remove();
            ph = null;
        }

        window.addEventListener('scroll', () => {
            if (!document.getElementById('accRequired')?.classList.contains('show')) {
                unpin(); return;
            }
            const r = item.getBoundingClientRect();
            if (r.top < 0 && r.bottom > btn.offsetHeight) pin();
            else unpin();
        }, { passive: true });

        // ウィンドウリサイズ時に幅を更新
        window.addEventListener('resize', () => {
            if (!btn.classList.contains('is-pinned')) return;
            btn.style.width = item.offsetWidth + 'px';
            btn.style.left  = item.getBoundingClientRect().left + 'px';
        }, { passive: true });
    })();

    // 必須項目：タブ件数バッジ＋アコーディオン合計バッジ
    const requiredBadge = document.getElementById('badge-required');
    const requiredCounts = { 'panel-job-types': 'cnt-job-types', 'panel-emp-types': 'cnt-emp-types', 'panel-conditions': 'cnt-conditions' };
    const updateRequiredBadge = () => {};
    const updatePanelTags = (panelId, tagsId) => {
        const tagsEl = document.getElementById(tagsId);
        if (!tagsEl) return;
        const checked = document.querySelectorAll(`#${panelId} input[type="checkbox"]:checked`);
        tagsEl.innerHTML = Array.from(checked).map(cb => {
            const label = cb.closest('label');
            const text = label ? label.textContent.trim() : '';
            return `<span class="search__selected-tag">${text}</span>`;
        }).join('');
    };
    const panelTagsMap = { 'panel-job-types': 'tags-job-types', 'panel-emp-types': 'tags-emp-types', 'panel-conditions': 'tags-conditions' };
    Object.entries(requiredCounts).forEach(([panelId, cntId]) => {
        const panelEl = document.getElementById(panelId);
        const cntEl   = document.getElementById(cntId);
        if (!panelEl || !cntEl) return;
        const cbs = panelEl.querySelectorAll('input[type="checkbox"]');
        const update = () => {
            const n = Array.from(cbs).filter(cb => cb.checked).length;
            cntEl.textContent = n;
            cntEl.classList.toggle('d-none', n === 0);
            updatePanelTags(panelId, panelTagsMap[panelId]);
        };
        cbs.forEach(cb => cb.addEventListener('change', update));
    });

    // 職種カテゴリーピル
    (function () {
        const pills  = document.querySelectorAll('.jt-cat-pill');
        const groups = document.querySelectorAll('.jt-cat-group');
        if (!pills.length) return;

        function activate(cat) {
            pills.forEach(p  => p.classList.toggle('active', p.dataset.cat === cat));
            groups.forEach(g => g.style.display = g.dataset.cat === cat ? '' : 'none');
        }

        pills.forEach(pill => pill.addEventListener('click', () => activate(pill.dataset.cat)));

        // チェック変更時にピルのバッジを更新
        groups.forEach(group => {
            group.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', () => {
                    const cat   = group.dataset.cat;
                    const count = group.querySelectorAll('input:checked').length;
                    const pill  = document.querySelector(`.jt-cat-pill[data-cat="${cat}"]`);
                    if (!pill) return;
                    const badge = pill.querySelector('.jt-cat-pill__count');
                    if (badge) { badge.textContent = count; badge.classList.toggle('d-none', count === 0); }
                });
            });
        });
    })();

    // 重視ポイント タグ表示
    const appealsPanel = document.getElementById('accAppeals');
    const appealsTagsEl = document.getElementById('appeals-tags');
    if (appealsPanel) {
        const cbs = appealsPanel.querySelectorAll('input[type="checkbox"]');
        const update = () => {
            const checked = Array.from(cbs).filter(cb => cb.checked);
            if (appealsTagsEl) {
                appealsTagsEl.innerHTML = checked.map(cb => {
                    const label = cb.closest('label');
                    const text = label ? label.textContent.trim() : '';
                    return `<span class="search__selected-tag">${text}</span>`;
                }).join('');
            }
        };
        cbs.forEach(cb => cb.addEventListener('change', update));
    }

    // チェックボックスの状態をlocalStorageに保存・復元
    (function () {
        const STORAGE_KEY = 'care_entry_search';
        const form = document.querySelector('.search__form');
        if (!form) return;

        // 復元
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            const key = cb.name + '=' + cb.value;
            if (saved[key]) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        const areaSelect = form.querySelector('select[name="area"]');
        if (areaSelect && saved['area']) areaSelect.value = saved['area'];

        // 保存
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', () => {
                const current = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
                const key = cb.name + '=' + cb.value;
                if (cb.checked) current[key] = true;
                else delete current[key];
                localStorage.setItem(STORAGE_KEY, JSON.stringify(current));
            });
        });
        if (areaSelect) {
            areaSelect.addEventListener('change', () => {
                const current = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
                if (areaSelect.value) current['area'] = areaSelect.value;
                else delete current['area'];
                localStorage.setItem(STORAGE_KEY, JSON.stringify(current));
            });
        }
    })();
</script>
</body>
</html>
