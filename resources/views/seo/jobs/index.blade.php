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
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}｜Care Entry（ケアエントリー）</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- ItemList JSON-LD: 求人カルーセル候補 --}}
    @if($jobs->isNotEmpty())
    <script type="application/ld+json">
    {!! json_encode([
        '@' . 'context' => 'https://schema.org',
        '@' . 'type'    => 'ItemList',
        'itemListElement' => $jobs->take(10)->values()->map(fn($j, $i) => [
            '@' . 'type'    => 'ListItem',
            'position' => $i + 1,
            'url'      => route('lp.show', $j->token),
            'name'     => $j->displayTitle(),
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    @endif

    {{-- BreadcrumbList JSON-LD: 階層構造をGoogleに伝え、SERPパンくず表示を狙う --}}
    @php
        $breadcrumbItems = [
            ['@' . 'type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => url('/')],
            ['@' . 'type' => 'ListItem', 'position' => 2, 'name' => '沖縄の介護・福祉求人', 'item' => route('seo.jobs.okinawa')],
        ];
        if ($currentArea) {
            $breadcrumbItems[] = ['@' . 'type' => 'ListItem', 'position' => 3, 'name' => "{$currentArea->name}の求人", 'item' => route('seo.jobs.area', $currentArea->slug)];
        }
        if (($currentJobType ?? null) && $currentArea) {
            $breadcrumbItems[] = ['@' . 'type' => 'ListItem', 'position' => 4, 'name' => "{$currentArea->name}の{$currentJobType->name}求人", 'item' => url()->current()];
        }
        $seoBreadcrumb = [
            '@' . 'context' => 'https://schema.org',
            '@' . 'type'    => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($seoBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
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
                <span class="mx-1">›</span>
                @if(($currentJobType ?? null))
                    <a href="{{ route('seo.jobs.area', $currentArea->slug) }}">{{ $currentArea->name }}</a>
                    <span class="mx-1">›</span>{{ $currentJobType->name }}
                @else
                    {{ $currentArea->name }}
                @endif
            @endif
        </p>
        <h1 class="seo-header__title">{{ $pageTitle }}</h1>
        <p class="seo-header__desc">{{ $pageDesc }}</p>
        @if(!empty($stats))
        <p style="font-size:1rem;font-weight:700;opacity:.95;margin-bottom:8px;">
            <i class="bi bi-briefcase-fill me-1"></i>現在 <span style="font-size:1.3rem;">{{ number_format($stats['total']) }}</span> 件掲載中
            @if($stats['own_count'] > 0)
                <span style="font-size:0.78rem;opacity:0.85;margin-left:0.5rem;">(自社 {{ $stats['own_count'] }}件 / ハローワーク {{ $stats['hw_count'] }}件)</span>
            @endif
        </p>
        <p style="margin-bottom:20px;"></p>
        @else
        <p style="font-size:1rem;font-weight:700;opacity:.95;margin-bottom:20px;">
            <i class="bi bi-briefcase-fill me-1"></i>現在 <span style="font-size:1.3rem;">{{ number_format(\App\Models\Job::active()->whereNotNull('email_verified_at')->where('is_admin_hidden',false)->whereHas('jobAreas.area',fn($q)=>$q->where('prefecture','沖縄県'))->count()) }}</span> 件の求人を掲載中
        </p>
        @endif
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
                {{-- データで見る〇〇の介護・福祉求人(エリアページのみ・掲載中の求人から自動集計) --}}
                @if($currentArea && !($currentJobType ?? null) && !empty($stats) && $stats['total'] > 0)
                    @php
                        $empRows  = $stats['emp_breakdown'] ?? [];
                        $typeRows = $stats['type_breakdown'] ?? [];
                        $fullTime = collect($empRows)->firstWhere('name', '正社員')['count'] ?? 0;
                    @endphp
                    <div class="seo-supplement mb-4">
                        <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:.75rem;">
                            <i class="bi bi-bar-chart-fill text-primary me-1"></i>データで見る{{ $currentArea->name }}の介護・福祉求人
                        </h2>
                        <ul class="mb-2" style="padding-left:1.2rem;line-height:1.9;">
                            <li>掲載中の求人：<strong>{{ number_format($stats['total']) }}件</strong>@if($fullTime > 0)（うち正社員 {{ $fullTime }}件）@endif</li>
                            @if(count($empRows) > 0)
                                <li>雇用形態：{{ collect($empRows)->map(fn($r) => $r['name'] . ' ' . $r['count'] . '件')->implode('／') }}</li>
                            @endif
                        </ul>
                        @if(count($typeRows) > 0)
                            <p class="mb-1 small fw-bold">職種の内訳</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($typeRows as $tr)
                                    <a href="{{ route('seo.jobs.area_jobtype', [$currentArea->slug, $tr['slug']]) }}"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#fff;border:1px solid #d6e4f7;border-radius:16px;text-decoration:none;color:#1a1a2e;font-size:0.82rem;">
                                        {{ $tr['name'] }} <strong>{{ $tr['count'] }}</strong>件
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-muted mt-2 mb-0" style="font-size:0.75rem;">※ 当サイトに掲載中の求人（ハローワーク求人を含む）から自動で集計しています。</p>
                    </div>
                @endif

                @php $hasFilters = request()->hasAny(['job_types', 'employment_types', 'condition_ids', 'appeal_ids', 'area', 'areas']); @endphp
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
                    @include('seo.jobs._job_card', ['job' => $job])

                    {{-- 近くのエリアのおすすめ求人(PR): 一覧の3件目の後に差し込む(一覧・件数とは別枠) --}}
                    @if($loop->iteration === 3 && ($nearbyJobs ?? collect())->isNotEmpty())
                        <div style="margin:-1rem 0 14px;">
                            @include('partials.recommended_jobs', ['jobs' => $nearbyJobs, 'heading' => '近くのエリアのおすすめ求人', 'wrap' => 'box'])
                        </div>
                    @endif
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

                {{-- 一覧が3件未満(0件含む)のページは一覧の直後に表示 --}}
                @if($jobs->count() < 3)
                    @include('partials.recommended_jobs', ['jobs' => $nearbyJobs ?? collect(), 'heading' => '近くのエリアのおすすめ求人', 'wrap' => 'box'])
                @endif

                {{-- ページネーション --}}
                @if($jobs->hasPages())
                    {{ $jobs->links() }}
                @endif

                {{-- 周辺エリアの求人(掲載が少ない市町村のみ)。一覧の件数とは別に表示 --}}
                @if(!empty($surrounding))
                    <div class="mt-4">
                        <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:.25rem;">
                            <i class="bi bi-geo-alt-fill text-primary me-1"></i>{{ $currentArea->name }}周辺（{{ $surrounding['region'] }}エリア）の介護・福祉求人
                        </h2>
                        <p class="small text-muted mb-3">
                            {{ $currentArea->name }}の近くの市町村（{{ collect($surrounding['areas'])->pluck('name')->implode('・') }}）に{{ number_format($surrounding['total']) }}件の求人があります。@if($surrounding['total'] > $surrounding['jobs']->count())そのうち{{ $surrounding['jobs']->count() }}件を表示しています。@endif
                        </p>
                        @foreach($surrounding['jobs'] as $job)
                            @include('seo.jobs._job_card', ['job' => $job])
                        @endforeach
                        @if($surrounding['total'] > $surrounding['jobs']->count())
                            <div class="text-center mb-3">
                                <a href="{{ route('seo.jobs.okinawa', ['areas' => collect($surrounding['areas'])->pluck('slug')->all()]) }}" class="btn btn-outline-primary btn-sm">
                                    周辺の市町村の求人をすべて見る（{{ number_format($surrounding['total']) }}件）
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- エリアページなら職種別リンク網を表示(内部リンク強化) --}}
                @if($currentArea && !($currentJobType ?? null))
                    @php
                        $popularJobTypes = \App\Models\MasterJobType::active()
                            ->whereIn('slug', ['care_staff_facility','home_helper','care_manager','care_welfare_worker','life_support_worker','childcare_worker','social_welfare_worker','service_provision_manager'])
                            ->orderBy('sort_order')
                            ->get();
                    @endphp
                    <div class="seo-supplement mt-4">
                        <h3><i class="bi bi-tags-fill text-primary me-1"></i>{{ $currentArea->name }}の職種から求人を探す</h3>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($popularJobTypes as $jt)
                                <a href="{{ route('seo.jobs.area_jobtype', [$currentArea->slug, $jt->slug]) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fff;border:1px solid #d6e4f7;border-radius:20px;text-decoration:none;color:#1a1a2e;font-size:0.85rem;">
                                    <i class="bi bi-briefcase"></i>{{ $currentArea->name }}の{{ $jt->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 職種×エリアページなら他エリアの同職種リンクを表示 --}}
                @if(($currentJobType ?? null) && $currentArea)
                    @php
                        $otherAreas = \App\Models\MasterArea::active()
                            ->where('prefecture', '沖縄県')
                            ->where('id', '!=', $currentArea->id)
                            ->orderBy('sort_order')
                            ->get();
                    @endphp
                    <div class="seo-supplement mt-4">
                        <h3><i class="bi bi-geo-alt-fill text-primary me-1"></i>他のエリアの{{ $currentJobType->name }}求人</h3>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($otherAreas as $ar)
                                <a href="{{ route('seo.jobs.area_jobtype', [$ar->slug, $currentJobType->slug]) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fff;border:1px solid #d6e4f7;border-radius:20px;text-decoration:none;color:#1a1a2e;font-size:0.85rem;">
                                    <i class="bi bi-geo-alt"></i>{{ $ar->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
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
                        @if(($currentJobType ?? null) && $currentArea)
                            {{ $currentArea->name }}で{{ $currentJobType->name }}の仕事を探す
                        @elseif($currentArea)
                            {{ $currentArea->name }}の介護・福祉の仕事
                        @else
                            介護・福祉の仕事【沖縄エリア対応】
                        @endif
                    </h3>
                    @if(($currentJobType ?? null) && $currentArea)
                        <p>沖縄県{{ $currentArea->name }}で{{ $currentJobType->name }}として働ける求人を掲載しています。{{ $currentArea->name }}は{{ $currentArea->region }}エリアに位置し、地域密着型の施設から中規模施設まで多様な職場があります。通勤圏内で{{ $currentJobType->name }}の仕事を探している方におすすめのエリアです。</p>
                        <p>正社員・パート・契約社員など雇用形態も選べます。給与や勤務条件から絞り込んで、自分に合った{{ $currentJobType->name }}の求人を見つけましょう。</p>
                    @elseif($currentArea)
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

<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<span hidden data-page-type="search"></span>
@include('partials.click_tracker')
</body>
</html>
