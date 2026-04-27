<!DOCTYPE html>
<html lang="ja">
<head>
@if(app()->environment('production'))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
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
    <title>{{ $pageTitle }}｜Care Entry（ケアエントリー）</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if($jobs->filter(fn($j) => $j->source === 'care_entry')->isEmpty())
        <meta name="robots" content="noindex,follow">
    @endif
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

        /* ナビ */
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
            transition: .15s;
        }
        .seo-nav__link:hover { background: #f8f9fa; color: var(--color-text); }

        /* ページヘッダー */
        .seo-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff;
            padding: 40px 0 36px;
        }
        .seo-header__breadcrumb {
            font-size: 0.78rem;
            opacity: .8;
            margin-bottom: 10px;
        }
        .seo-header__breadcrumb a { color: rgba(255,255,255,.8); text-decoration: none; }
        .seo-header__breadcrumb a:hover { color: #fff; }
        .seo-header__title {
            font-size: clamp(1.3rem, 3.5vw, 1.9rem);
            font-weight: 900;
            margin-bottom: 8px;
        }
        .seo-header__desc {
            font-size: 0.93rem;
            opacity: .88;
            margin-bottom: 24px;
        }
        .seo-header__cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-seo-search {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            color: var(--color-primary);
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 12px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-seo-search:hover { background: var(--color-soft); color: var(--color-primary); }
        .btn-seo-line {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--color-line);
            color: #fff;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 12px 24px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-seo-line:hover { background: #05a847; color: #fff; }

        /* エリアナビ */
        .area-nav {
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            padding: 14px 0;
            overflow-x: auto;
        }
        .area-nav__inner {
            display: flex;
            gap: 8px;
            white-space: nowrap;
            padding: 0 4px;
        }
        .area-nav__link {
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--color-muted);
            text-decoration: none;
            padding: 5px 14px;
            border: 1px solid var(--color-border);
            border-radius: 20px;
            transition: .15s;
        }
        .area-nav__link:hover,
        .area-nav__link--active {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
        }

        /* メインレイアウト */
        .seo-main {
            padding: 28px 0 60px;
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
        .job-card:hover {
            box-shadow: 0 4px 16px rgba(26,115,232,.1);
            transform: translateY(-1px);
        }
        .job-card__company {
            font-size: 0.8rem;
            color: var(--color-muted);
            margin-bottom: 5px;
        }
        .job-card__title {
            font-size: 1rem;
            font-weight: 800;
            color: #0d1a2e;
            margin-bottom: 10px;
            line-height: 1.45;
        }
        .job-card__title a {
            color: inherit;
            text-decoration: none;
        }
        .job-card__title a:hover { color: var(--color-primary); }
        .job-card__tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
        .job-card__tag {
            font-size: 0.76rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 4px;
        }
        .job-card__tag--area     { background: var(--color-soft); color: var(--color-primary); }
        .job-card__tag--job-type { background: #e8f5e9; color: #2e7d32; }
        .job-card__salary {
            font-size: 0.88rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 14px;
        }
        .job-card__salary span { color: var(--color-primary); }
        .job-card__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-card-line {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--color-line);
            color: #fff;
            border-radius: 24px;
            font-size: 0.88rem;
            font-weight: 800;
            padding: 9px 20px;
            text-decoration: none;
            transition: .15s;
        }
        .btn-card-line:hover { background: #05a847; color: #fff; }
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
        .btn-card-detail:hover { background: var(--color-soft); color: var(--color-primary); }

        /* 件数 */
        .result-count {
            font-size: 0.85rem;
            color: var(--color-muted);
            margin-bottom: 16px;
        }
        .result-count strong { color: var(--color-text); }

        /* 求人なし */
        .no-jobs {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 48px 24px;
            text-align: center;
            color: var(--color-muted);
        }
        .no-jobs i { font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: .4; }

        /* ページネーション */
        .pagination { justify-content: center; margin-top: 24px; }

        /* 補助コンテンツ */
        .seo-supplement {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 24px;
            margin-top: 32px;
            font-size: 0.9rem;
            color: #555;
            line-height: 1.85;
        }
        .seo-supplement h3 {
            font-size: 1rem;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        /* LP誘導 */
        .seo-lp-cta {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff;
            border-radius: 16px;
            padding: 32px 24px;
            margin-top: 24px;
            text-align: center;
        }
        .seo-lp-cta h3 {
            font-size: 1.1rem;
            font-weight: 900;
            margin-bottom: 8px;
        }
        .seo-lp-cta p {
            font-size: 0.88rem;
            opacity: .88;
            margin-bottom: 20px;
        }
        .seo-lp-cta__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }

        /* フッター */
        .seo-footer {
            background: #1a1a2e;
            color: #aaa;
            padding: 28px 0;
            font-size: 0.82rem;
            text-align: center;
        }
        .seo-footer a { color: #aaa; text-decoration: none; }
        .seo-footer a:hover { color: #fff; }
        .seo-footer__links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px 16px;
            margin-bottom: 12px;
        }

        @media (max-width: 767px) {
            .seo-header { padding: 32px 0 28px; }
            .seo-main { padding: 20px 0 48px; }
        }
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

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
            <a href="{{ route('seo.jobs.okinawa') }}">沖縄の介護・福祉求人</a>
            @if($currentArea)
                <span class="mx-1">›</span>{{ $currentArea->name }}
            @endif
        </p>
        <h1 class="seo-header__title">{{ $pageTitle }}</h1>
        <p class="seo-header__desc">{{ $pageDesc }}</p>
        <p style="font-size:1rem;font-weight:700;opacity:.95;margin-bottom:20px;">
            <i class="bi bi-briefcase-fill me-1"></i>現在 <span style="font-size:1.3rem;">{{ number_format(\App\Models\Job::active()->whereNotNull('email_verified_at')->where('is_admin_hidden',false)->whereHas('jobAreas.area',fn($q)=>$q->where('prefecture','沖縄県'))->count()) }}</span> 件の求人を掲載中
        </p>
        <div class="seo-header__cta-group">
            <a href="{{ route('home') }}#search" class="btn-seo-search">
                <i class="bi bi-search"></i>求人を検索する
            </a>
        </div>
    </div>
</div>


{{-- メインコンテンツ --}}
<div class="seo-main">
    <div class="container">
        <div class="row g-4">
            {{-- 求人一覧 --}}
            <div class="col-lg-8">
                @php $hasFilters = request()->hasAny(['job_types', 'employment_types', 'condition_ids', 'appeal_ids', 'area']); @endphp
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <p class="result-count mb-0">
                        <strong>{{ number_format($jobs->total()) }}件</strong> の求人が見つかりました
                    </p>
                    @if($hasFilters)
                        <a href="{{ route('seo.jobs.okinawa') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>条件をリセット
                        </a>
                    @endif
                </div>

                @php $entryBaseParams = $searchConditionIds ?? []; @endphp
                @forelse($jobs as $job)
                    @php $isHw = $job->source === 'hellowork'; @endphp
                    <div class="job-card">
                        <p class="job-card__company">{{ $job->company_name }}</p>
                        <p class="job-card__title">
                            <a href="{{ route('lp.show', $job->token) }}">
                                {{ $job->seo_title ?: $job->title }}
                            </a>
                        </p>
                        <div class="job-card__tags">
                            @if($isHw)
                                <span class="job-card__tag" style="background:#fff3e0;color:#e65100;font-size:.76rem;font-weight:600;padding:3px 10px;border-radius:4px;">ハローワーク</span>
                            @endif
                            @foreach($job->jobAreas->take(2) as $ja)
                                @if($ja->area)
                                    <span class="job-card__tag job-card__tag--area">
                                        <i class="bi bi-geo-alt-fill"></i> {{ $ja->area->name }}
                                    </span>
                                @endif
                            @endforeach
                            @foreach($job->jobJobTypes->take(2) as $jt)
                                @if($jt->jobType)
                                    <span class="job-card__tag job-card__tag--job-type">{{ $jt->jobType->name }}</span>
                                @endif
                            @endforeach
                        </div>
                        @if($job->salary_type && $job->salary_min)
                            <p class="job-card__salary">
                                <span>{{ $job->salaryText() }}</span>
                            </p>
                        @endif
                        <div class="job-card__actions">
                            <a href="{{ route('lp.show', $job->token) }}" class="btn-card-detail">
                                <i class="bi bi-arrow-right-circle"></i>詳細を見る
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="no-jobs">
                        <i class="bi bi-search"></i>
                        <p class="mb-2 fw-bold">現在掲載中の求人がありません</p>
                        <p class="small mb-0">他のエリアや職種でも探してみましょう</p>
                        <a href="{{ route('seo.jobs.okinawa') }}" class="btn btn-outline-primary btn-sm mt-3">
                            沖縄県すべての求人を見る
                        </a>
                    </div>
                @endforelse

                {{-- ページネーション --}}
                @if($jobs->hasPages())
                    {{ $jobs->links() }}
                @endif
            </div>

            {{-- サイドバー --}}
            <div class="col-lg-4">
                {{-- LP誘導 --}}
                <div class="seo-lp-cta">
                    <h3>自分に合う求人を探す</h3>
                    <p>職種や勤務条件から絞り込んで、自分に合う職場を見つけましょう</p>
                    <div class="seo-lp-cta__actions">
                        <a href="{{ route('home') }}#search" class="btn-seo-search">
                            <i class="bi bi-search"></i>求人を検索する
                        </a>
                    </div>
                </div>


                {{-- 補助コンテンツ --}}
                <div class="seo-supplement">
                    <h3>
                        <i class="bi bi-info-circle-fill text-primary me-1"></i>
                        {{ $currentArea ? "{$currentArea->name}の介護・福祉の仕事" : '沖縄の介護・福祉の仕事' }}
                    </h3>
                    @if($currentArea)
                        <p>{{ $currentArea->name }}は沖縄県{{ $currentArea->region }}エリアに位置し、介護・福祉施設の求人が多く掲載されています。地域に根ざした職場で働きたい方に向けた求人を探せます。</p>
                    @else
                        <p>沖縄県内の介護・福祉求人を一覧で確認できます。那覇・南部・中部・北部・離島と、沖縄全域のエリアから自分に合う職場を探せます。</p>
                    @endif
                    <p class="mb-0">会員登録不要で応募できます。LINEからそのまま応募することも可能です。</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- フッター --}}
<footer class="seo-footer">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">沖縄の介護・福祉専門の求人サービス</span>
        </div>
        <div class="seo-footer__links">
            <a href="{{ route('home') }}">求人を探す</a>
            <a href="{{ route('client') }}">求人掲載をお考えの方</a>
            <a href="{{ route('company') }}">運営者情報</a>
            <a href="{{ route('privacy-policy') }}">プライバシーポリシー</a>
            <a href="{{ route('terms') }}">利用規約</a>
        </div>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
