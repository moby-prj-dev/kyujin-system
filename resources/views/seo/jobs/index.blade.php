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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
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
    <link rel="stylesheet" href="/css/jobs.css">
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
            <a href="{{ route('seo.jobs.okinawa') }}">介護・福祉求人(沖縄エリア)</a>
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
                        {{ $currentArea ? "{$currentArea->name}の介護・福祉の仕事" : '介護・福祉の仕事【沖縄エリア対応】' }}
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
            <span class="ms-2">介護・福祉専門の求人サービス【対応エリア:沖縄県】</span>
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
