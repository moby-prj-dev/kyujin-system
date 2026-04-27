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
    <meta property="og:title" content="沖縄の介護・福祉求人｜Care Entry（ケアエントリー）">
    <meta property="og:description" content="沖縄の介護・福祉に特化した求人サービス。地域・職種から自分に合う職場を探せます。会員登録不要、LINEでそのまま応募できます。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ url('/images/ogp.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ url('/images/ogp.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/welcome.css">
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
        <p class="search__heading">
            沖縄の求人を探す
            <span class="search__count-badge">
                <i class="bi bi-briefcase-fill me-1"></i>現在 {{ number_format(\App\Models\Job::active()->whereNotNull('email_verified_at')->where('is_admin_hidden', false)->count()) }} 件掲載中
            </span>
        </p>
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
                <button type="button" class="search__reset" id="searchResetBtn">
                    <i class="bi bi-x-circle me-1"></i>リセット
                </button>
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

        // リセットボタン
        const resetBtn = document.getElementById('searchResetBtn');
        if (resetBtn && form) {
            resetBtn.addEventListener('click', () => {
                form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                });
                const sel = form.querySelector('select[name="area"]');
                if (sel) { sel.value = ''; sel.dispatchEvent(new Event('change')); }
                localStorage.removeItem(STORAGE_KEY);
            });
        }
    })();
</script>
</body>
</html>
