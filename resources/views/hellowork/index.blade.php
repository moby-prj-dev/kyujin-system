<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ハローワーク 沖縄×介護・福祉求人一覧｜Care Entry</title>
    <meta name="description" content="ハローワークに掲載されている沖縄県の介護・福祉求人一覧です。{{ number_format($jobs->total()) }}件の求人を掲載中。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary:      #1a73e8;
            --color-primary-dark: #0d47a1;
            --color-line:         #06C755;
            --color-text:         #2d2d2d;
            --color-muted:        #6c757d;
            --color-border:       #dee2e6;
            --color-soft:         #f0f7ff;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--color-text);
            margin: 0;
            background: #f5f7fa;
        }
        .seo-nav {
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .seo-nav__logo img { height: 40px; }
        .seo-nav__link {
            font-size: 0.82rem;
            color: var(--color-muted);
            text-decoration: none;
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 6px 14px;
            white-space: nowrap;
        }
        .seo-nav__link:hover { background: #f8f9fa; color: var(--color-text); }
        .seo-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff;
            padding: 40px 0 36px;
        }
        .seo-header__breadcrumb { font-size: 0.78rem; opacity: .8; margin-bottom: 10px; }
        .seo-header__breadcrumb a { color: rgba(255,255,255,.8); text-decoration: none; }
        .seo-header__title { font-size: clamp(1.3rem, 3.5vw, 1.9rem); font-weight: 900; margin-bottom: 8px; }
        .seo-header__desc { font-size: 0.93rem; opacity: .88; margin-bottom: 24px; }
        .hw-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 20px;
            font-size: 0.8rem;
            padding: 5px 14px;
            margin-bottom: 16px;
        }
        .seo-main { padding: 28px 0 60px; }
        .result-count { font-size: 0.85rem; color: var(--color-muted); margin-bottom: 16px; }
        .result-count strong { color: var(--color-text); }

        /* 検索フォーム */
        .hw-search {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid #dde8f8;
            padding: 16px 20px;
            margin-bottom: 20px;
        }
        .hw-search__row { display: flex; gap: 10px; flex-wrap: wrap; }
        .hw-search__input {
            flex: 1;
            min-width: 180px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.9rem;
        }
        .hw-search__select {
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.9rem;
            background: #fff;
        }
        .hw-search__btn {
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
        }

        /* 求人カード */
        .job-card {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid #dde8f8;
            box-shadow: 0 1px 6px rgba(26,115,232,.05);
            padding: 20px;
            margin-bottom: 14px;
            transition: .2s;
        }
        .job-card:hover { box-shadow: 0 4px 16px rgba(26,115,232,.1); transform: translateY(-1px); }
        .job-card__company { font-size: 0.8rem; color: var(--color-muted); margin-bottom: 5px; }
        .job-card__title { font-size: 1rem; font-weight: 800; color: #0d1a2e; margin-bottom: 10px; line-height: 1.45; }
        .job-card__tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
        .job-card__tag { font-size: 0.76rem; font-weight: 600; padding: 3px 10px; border-radius: 4px; }
        .job-card__tag--area     { background: var(--color-soft); color: var(--color-primary); }
        .job-card__tag--job-type { background: #e8f5e9; color: #2e7d32; }
        .job-card__tag--hw       { background: #fff3e0; color: #e65100; }
        .job-card__salary { font-size: 0.88rem; font-weight: 700; color: #333; margin-bottom: 8px; }
        .job-card__salary span { color: var(--color-primary); }
        .job-card__desc { font-size: 0.82rem; color: #555; margin-bottom: 14px; line-height: 1.65; }
        .job-card__meta { font-size: 0.78rem; color: var(--color-muted); margin-bottom: 14px; }
        .job-card__actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-card-hw {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e65100;
            color: #fff;
            border-radius: 24px;
            font-size: 0.88rem;
            font-weight: 800;
            padding: 9px 20px;
            text-decoration: none;
            transition: .15s;
        }
        .btn-card-hw:hover { background: #bf360c; color: #fff; }
        .btn-card-detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--color-primary);
            border: 1.5px solid var(--color-primary);
            border-radius: 24px;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 8px 18px;
            text-decoration: none;
            transition: .15s;
        }
        .btn-card-detail:hover { background: var(--color-soft); }
        .no-jobs {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 48px 24px;
            text-align: center;
            color: var(--color-muted);
        }
        .pagination { justify-content: center; margin-top: 24px; }
        .seo-footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 28px 0;
            font-size: 0.82rem;
            text-align: center;
        }
        .seo-footer a { color: #aaa; text-decoration: none; }
        .seo-footer a:hover { color: #fff; }
        @media (max-width: 767px) {
            .seo-header { padding: 32px 0 28px; }
            .seo-main { padding: 20px 0 48px; }
        }
    </style>
</head>
<body>

{{-- ナビ --}}
<nav class="seo-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="seo-nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('home') }}" class="seo-nav__link">
            <i class="bi bi-house me-1"></i>トップへ
        </a>
    </div>
</nav>

{{-- ページヘッダー --}}
<div class="seo-header">
    <div class="container">
        <p class="seo-header__breadcrumb">
            <a href="{{ route('home') }}">ホーム</a>
            <span class="mx-1">›</span>
            <span>ハローワーク 沖縄×介護・福祉求人</span>
        </p>
        <div class="hw-badge">
            <i class="bi bi-building"></i> ハローワーク掲載求人
        </div>
        <h1 class="seo-header__title">沖縄県 介護・福祉の求人一覧<br><small style="font-size:.6em;opacity:.85">ハローワーク掲載分</small></h1>
        <p class="seo-header__desc">ハローワークに掲載されている沖縄県の介護・福祉求人を掲載しています。{{ number_format($jobs->total()) }}件掲載中。</p>
    </div>
</div>

{{-- メインコンテンツ --}}
<div class="seo-main">
    <div class="container">

        {{-- 検索フォーム --}}
        <div class="hw-search">
            <form method="GET" action="">
                <div class="hw-search__row">
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="キーワード（職種・事業所名など）" class="hw-search__input">
                    <select name="employment_type" class="hw-search__select">
                        <option value="">雇用形態 すべて</option>
                        <option value="正社員" {{ $employmentType === '正社員' ? 'selected' : '' }}>正社員</option>
                        <option value="パート" {{ $employmentType === 'パート' ? 'selected' : '' }}>パート</option>
                        <option value="フル" {{ $employmentType === 'フル' ? 'selected' : '' }}>フルタイム</option>
                    </select>
                    <button type="submit" class="hw-search__btn"><i class="bi bi-search me-1"></i>検索</button>
                </div>
            </form>
        </div>

        <p class="result-count">
            <strong>{{ number_format($jobs->total()) }}件</strong> の求人が見つかりました
        </p>

        @forelse($jobs as $job)
            @php
                $kjNo = $job['求人番号'] ?? '';
                $hwUrl = $kjNo
                    ? 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do?screenId=GECA110010&action=dispDetailBtn&kJNo=' . str_replace('-', '', $kjNo) . '&kJKbn=1&fullPart=1'
                    : 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do';
            @endphp
            <div class="job-card">
                <p class="job-card__company">{{ $job['事業所名'] ?? '' }}</p>
                <p class="job-card__title">{{ $job['職種'] ?? '（職種名なし）' }}</p>

                <div class="job-card__tags">
                    @if(!empty($job['就業場所']))
                        <span class="job-card__tag job-card__tag--area">
                            <i class="bi bi-geo-alt-fill"></i> {{ $job['就業場所'] }}
                        </span>
                    @endif
                    @if(!empty($job['雇用形態']))
                        <span class="job-card__tag job-card__tag--job-type">{{ $job['雇用形態'] }}</span>
                    @endif
                    <span class="job-card__tag job-card__tag--hw">ハローワーク</span>
                </div>

                @if(!empty($job['賃金']))
                    <p class="job-card__salary"><span>{{ $job['賃金'] }}</span></p>
                @endif

                @if(!empty($job['仕事の内容']))
                    <p class="job-card__desc">{{ Str::limit($job['仕事の内容'], 100) }}</p>
                @endif

                <p class="job-card__meta">
                    @if(!empty($job['受付年月日']))<i class="bi bi-calendar3 me-1"></i>受付：{{ $job['受付年月日'] }}　@endif
                    @if(!empty($job['紹介期限日']))<i class="bi bi-calendar-x me-1"></i>期限：{{ $job['紹介期限日'] }}　@endif
                    @if($kjNo)<i class="bi bi-hash me-1"></i>{{ $kjNo }}@endif
                </p>

                <div class="job-card__actions">
                    <a href="{{ $hwUrl }}" target="_blank" rel="noopener" class="btn-card-hw">
                        <i class="bi bi-box-arrow-up-right"></i>ハローワークで見る
                    </a>
                    <a href="{{ route('home') }}#search" class="btn-card-detail">
                        <i class="bi bi-arrow-right-circle"></i>Care Entryで探す
                    </a>
                </div>
            </div>
        @empty
            <div class="no-jobs">
                <i class="bi bi-search" style="font-size:2.5rem;display:block;opacity:.4;margin-bottom:12px"></i>
                <p class="mb-2 fw-bold">条件に合う求人が見つかりませんでした</p>
                <a href="{{ route('hellowork.index') }}" class="btn btn-outline-primary btn-sm mt-2">条件をリセット</a>
            </div>
        @endforelse

        {{-- ページネーション --}}
        @if($jobs->hasPages())
            {{ $jobs->links() }}
        @endif

    </div>
</div>

{{-- フッター --}}
<footer class="seo-footer">
    <div class="container">
        <div class="mb-2">
            <a href="{{ route('privacy-policy') }}" class="me-3">プライバシーポリシー</a>
            <a href="{{ route('terms') }}" class="me-3">利用規約</a>
            <a href="{{ route('home') }}">Care Entry トップ</a>
        </div>
        <p class="mb-0">求人情報はハローワークインターネットサービスより取得しています。</p>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry</p>
    </div>
</footer>

</body>
</html>
