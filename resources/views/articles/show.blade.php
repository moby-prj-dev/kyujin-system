<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }}｜Care Entry</title>
    <meta name="description" content="{{ $article->meta_description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary: #1a73e8;
            --color-primary-dark: #0d47a1;
            --color-line: #06C755;
            --color-text: #2d2d2d;
            --color-muted: #6c757d;
            --color-border: #dee2e6;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--color-text); margin: 0; background: #f5f7fa;
        }
        .art-nav {
            background: #fff; border-bottom: 1px solid var(--color-border);
            padding: 12px 0; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .art-nav__logo img { height: 40px; }
        .art-nav__link {
            font-size: 0.82rem; color: var(--color-muted); text-decoration: none;
            border: 1px solid var(--color-border); border-radius: 20px; padding: 6px 14px;
        }
        .art-nav__link:hover { background: #f8f9fa; color: var(--color-text); }

        .art-main { padding: 32px 0 60px; }

        .article-wrap {
            background: #fff; border-radius: 14px;
            border: 1.5px solid var(--color-border);
            padding: 32px;
        }
        .article-wrap h1 {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            font-weight: 900; line-height: 1.5;
            color: #0d1a2e; margin-bottom: 16px;
        }
        .article-meta {
            font-size: 0.8rem; color: var(--color-muted);
            margin-bottom: 24px; padding-bottom: 20px;
            border-bottom: 1px solid var(--color-border);
        }
        .article-body {
            font-size: 0.95rem;
            line-height: 1.9;
            color: #333;
        }
        .article-body p { margin-bottom: 1.2em; }

        .category-badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.75rem; font-weight: 700;
            padding: 3px 10px; border-radius: 4px; margin-bottom: 16px;
        }
        .category-badge--industry      { background: #e8f0fb; color: var(--color-primary); }
        .category-badge--job_type      { background: #e8f5e9; color: #2e7d32; }
        .category-badge--area          { background: #fff3e0; color: #e65100; }
        .category-badge--qualification { background: #f3e5f5; color: #6a1b9a; }
        .category-badge--beginner      { background: #e0f7fa; color: #00695c; }

        .cta-box {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff; border-radius: 14px; padding: 28px 24px;
            margin-top: 32px; text-align: center;
        }
        .cta-box h3 { font-size: 1.05rem; font-weight: 900; margin-bottom: 8px; }
        .cta-box p  { font-size: 0.88rem; opacity: .88; margin-bottom: 18px; }
        .btn-cta-search {
            display: inline-flex; align-items: center; gap: 7px;
            background: #fff; color: var(--color-primary); border-radius: 30px;
            font-size: 0.95rem; font-weight: 800; padding: 11px 24px;
            text-decoration: none; transition: .2s;
        }
        .btn-cta-search:hover { background: #f0f6ff; color: var(--color-primary); }
        .btn-cta-line {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--color-line); color: #fff; border-radius: 30px;
            font-size: 0.95rem; font-weight: 800; padding: 11px 20px;
            text-decoration: none; transition: .2s;
        }
        .btn-cta-line:hover { background: #05a847; color: #fff; }

        .related-card {
            background: #fff; border-radius: 10px; border: 1.5px solid var(--color-border);
            padding: 16px; text-decoration: none; color: var(--color-text);
            display: block; transition: .2s;
        }
        .related-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 2px 10px rgba(26,115,232,.08);
            color: var(--color-text);
        }
        .related-card__title { font-size: 0.88rem; font-weight: 800; margin-bottom: 4px; }
        .related-card__meta  { font-size: 0.75rem; color: var(--color-muted); }

        .art-footer {
            background: #1a1a2e; color: #aaa; padding: 28px 0;
            font-size: 0.82rem; text-align: center;
        }
        .art-footer a { color: #aaa; text-decoration: none; }
        .art-footer a:hover { color: #fff; }
        .art-footer__links {
            display: flex; justify-content: center; flex-wrap: wrap;
            gap: 5px 16px; margin-bottom: 12px;
        }
    </style>

    {{-- BreadcrumbList JSON-LD --}}
    @php
        $articleBreadcrumb = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => '記事一覧', 'item' => route('articles.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $article->h1 ?: $article->title],
            ])),
        ];
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'headline' => $article->h1 ?: $article->title,
            'description' => $article->meta_description ?? '',
            'datePublished' => $article->published_at?->toIso8601String() ?? $article->created_at->toIso8601String(),
            'dateModified' => $article->updated_at->toIso8601String(),
            'author'   => ['@type' => 'Organization', 'name' => 'Care Entry'],
            'publisher' => ['@type' => 'Organization', 'name' => 'Care Entry', 'url' => url('/')],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($articleBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
</head>
<body>

<nav class="art-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="art-nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('home') }}" class="art-nav__link">
            <i class="bi bi-house me-1"></i>トップへ
        </a>
    </div>
</nav>

<div class="art-main">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">

                {{-- パンくず --}}
                <p style="font-size:0.78rem;color:var(--color-muted);margin-bottom:16px;">
                    <a href="{{ route('home') }}" style="color:var(--color-muted);text-decoration:none;">ホーム</a>
                    <span class="mx-1">›</span>
                    <a href="{{ route('articles.index') }}" style="color:var(--color-muted);text-decoration:none;">お役立ち情報</a>
                    <span class="mx-1">›</span>{{ $article->title }}
                </p>

                <div class="article-wrap">
                    @php
                    $categoryMeta = [
                        'industry'      => ['label' => '業界情報',   'icon' => 'bi-bar-chart-fill'],
                        'job_type'      => ['label' => '職種別情報', 'icon' => 'bi-briefcase-fill'],
                        'area'          => ['label' => 'エリア情報', 'icon' => 'bi-geo-alt-fill'],
                        'qualification' => ['label' => '資格・研修', 'icon' => 'bi-award-fill'],
                        'beginner'      => ['label' => '未経験・転職', 'icon' => 'bi-person-plus-fill'],
                    ];
                    $meta = $categoryMeta[$article->category] ?? ['label' => '', 'icon' => 'bi-file-text'];
                    @endphp

                    <span class="category-badge category-badge--{{ $article->category }}">
                        <i class="{{ $meta['icon'] }}"></i>{{ $meta['label'] }}
                    </span>

                    <h1>{{ $article->h1 }}</h1>

                    @if($article->image_url)
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                         style="width:100%;border-radius:10px;margin-bottom:20px;object-fit:cover;max-height:300px;"
                         loading="lazy" width="800" height="300">
                    @endif

                    <div class="article-meta">
                        <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at->format('Y年m月d日') }}
                        @if($article->area)
                            <span class="ms-3"><i class="bi bi-geo-alt me-1"></i>{{ $article->area->name }}</span>
                        @endif
                        @if($article->jobType)
                            <span class="ms-3"><i class="bi bi-briefcase me-1"></i>{{ $article->jobType->name }}</span>
                        @endif
                    </div>

                    {{-- 自社DBによるライブ統計(Care Entry独自データ・SEO独自性) --}}
                    @if(!empty($areaStats))
                    <div style="margin:1.5rem 0;padding:1.25rem;background:linear-gradient(135deg,#f0f7ff 0%,#e8f0fe 100%);border-radius:10px;border:1px solid #d6e4f7;">
                        <div style="font-weight:800;font-size:0.95rem;color:#0d47a1;margin-bottom:0.75rem;">
                            <i class="bi bi-bar-chart-fill me-1"></i>
                            @if($areaStats['area_name'] && $areaStats['job_type_name'])
                                {{ $areaStats['area_name'] }}の{{ $areaStats['job_type_name'] }}求人マーケット
                            @elseif($areaStats['area_name'])
                                {{ $areaStats['area_name'] }}の介護・福祉求人マーケット
                            @elseif($areaStats['job_type_name'])
                                沖縄県の{{ $areaStats['job_type_name'] }}求人マーケット
                            @else
                                沖縄県の介護・福祉求人マーケット
                            @endif
                            <span style="font-size:0.72rem;color:#666;font-weight:400;margin-left:0.5rem;">Care Entry調べ ({{ now()->format('Y年m月d日') }}時点)</span>
                        </div>
                        <div class="row g-3" style="font-size:0.88rem;">
                            <div class="col-md-4">
                                <div style="color:#666;font-size:0.78rem;">掲載求人数</div>
                                <div style="font-size:1.4rem;font-weight:800;color:#1a73e8;">
                                    {{ number_format($areaStats['total']) }}<span style="font-size:0.85rem;font-weight:600;">件</span>
                                </div>
                                <div style="font-size:0.72rem;color:#888;">
                                    @if($areaStats['own_count'] > 0)
                                        自社登録 {{ $areaStats['own_count'] }}件
                                    @endif
                                    @if($areaStats['hw_count'] > 0)
                                        @if($areaStats['own_count'] > 0) / @endif ハローワーク {{ $areaStats['hw_count'] }}件
                                    @endif
                                </div>
                            </div>
                            @if(!empty($areaStats['by_job_type']))
                            <div class="col-md-4">
                                <div style="color:#666;font-size:0.78rem;">主な職種</div>
                                <div style="font-size:0.85rem;line-height:1.5;">
                                    @foreach($areaStats['by_job_type'] as $idx => $jt)
                                        <span>{{ $jt['name'] }}<span style="color:#888;font-size:0.75rem;">({{ $jt['count'] }})</span></span>@if(!$loop->last) <span style="color:#ccc;">/</span> @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(!empty($areaStats['salary']))
                            <div class="col-md-4">
                                <div style="color:#666;font-size:0.78rem;">月給レンジ ({{ $areaStats['salary']['count'] }}件集計)</div>
                                <div style="font-size:1.0rem;font-weight:700;color:#1a73e8;">
                                    {{ number_format($areaStats['salary']['min']) }}〜{{ number_format($areaStats['salary']['max']) }}円
                                </div>
                                <div style="font-size:0.72rem;color:#888;">中央値 {{ number_format($areaStats['salary']['median']) }}円</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="article-body">
                        @foreach(explode("\n\n", $article->body) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- 公的統計データ(e-Stat 賃金センサス)・SEO独自性 --}}
                @if(!empty($publicStat))
                <div style="margin-top:2rem;padding:1.25rem;background:#fff;border:2px solid #ffd54f;border-radius:10px;">
                    <div style="font-weight:800;font-size:0.95rem;color:#5d4037;margin-bottom:0.75rem;">
                        <i class="bi bi-graph-up-arrow me-1" style="color:#f9a825;"></i>
                        公的統計データ - 沖縄県の{{ $publicStat->occupation }}平均賃金
                    </div>
                    <div class="row g-3" style="font-size:0.88rem;">
                        @if($publicStat->monthly_wage)
                        <div class="col-md-4">
                            <div style="color:#666;font-size:0.78rem;">月給(所定内給与額)</div>
                            <div style="font-size:1.4rem;font-weight:800;color:#f57c00;">
                                {{ number_format($publicStat->monthly_wage) }}<span style="font-size:0.85rem;font-weight:600;">円</span>
                            </div>
                        </div>
                        @endif
                        @if($publicStat->annual_special_wage)
                        <div class="col-md-4">
                            <div style="color:#666;font-size:0.78rem;">年間賞与等</div>
                            <div style="font-size:1.2rem;font-weight:700;color:#f57c00;">
                                {{ number_format($publicStat->annual_special_wage) }}<span style="font-size:0.8rem;font-weight:600;">円</span>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div style="color:#666;font-size:0.78rem;">統計年</div>
                            <div style="font-size:1.1rem;font-weight:700;color:#5d4037;">
                                {{ $publicStat->year }}年<span style="font-size:0.72rem;font-weight:400;color:#888;margin-left:4px;">(直近公表値)</span>
                            </div>
                            @if($publicStat->sample_size)
                            <div style="font-size:0.72rem;color:#888;">対象者数 {{ number_format($publicStat->sample_size) }}人</div>
                            @endif
                        </div>
                    </div>
                    <div style="font-size:0.7rem;color:#888;margin-top:0.5rem;line-height:1.5;">
                        出典: 厚生労働省「賃金構造基本統計調査」(e-Stat より取得)<br>
                        ※都道府県×職種別の細かい統計はサンプル数の関係で、最新年でなく1〜2年前のデータが公表値となる場合があります。
                    </div>
                </div>
                @endif

                {{-- 記事直後のおすすめ求人(SEO in-content + モバイル可視性UP) --}}
                @if(!empty($relatedJobs) && $relatedJobs->count() > 0)
                <div style="margin-top:2rem;padding:1.25rem;background:#f0f7ff;border-left:4px solid var(--color-primary);border-radius:8px;">
                    <p style="font-weight:800;font-size:1rem;color:#1a1a2e;margin-bottom:0.85rem;">
                        <i class="bi bi-briefcase-fill text-success me-1"></i>
                        この記事を読んだ方におすすめの求人
                        @if($article->area)<span class="text-muted fw-normal" style="font-size:0.78rem;">({{ $article->area->name }})</span>@endif
                    </p>
                    <div class="d-flex flex-column gap-2">
                        @foreach($relatedJobs->take(3) as $j)
                            <a href="{{ route('lp.show', $j->token) }}"
                               style="display:block;padding:12px 14px;background:#fff;border:1px solid #d6e4f7;border-radius:6px;text-decoration:none;color:#1a1a2e;transition:.15s;"
                               onmouseover="this.style.borderColor='#1a73e8';this.style.boxShadow='0 2px 8px rgba(26,115,232,0.15)';"
                               onmouseout="this.style.borderColor='#d6e4f7';this.style.boxShadow='none';">
                                <div style="font-weight:700;font-size:0.92rem;line-height:1.4;margin-bottom:4px;">{{ $j->seo_title ?: $j->title }}</div>
                                <div style="font-size:0.78rem;color:#666;">
                                    @foreach($j->jobAreas->take(1) as $ja)@if($ja->area)<i class="bi bi-geo-alt-fill me-1"></i>{{ $ja->area->name }}@endif @endforeach
                                    @foreach($j->jobJobTypes->take(1) as $jt)@if($jt->jobType)・{{ $jt->jobType->name }}@endif @endforeach
                                    @foreach($j->jobEmploymentTypes->take(1) as $et)@if($et->employmentType)・{{ $et->employmentType->name }}@endif @endforeach
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA --}}
                <div class="cta-box">
                    <h3>介護・福祉の求人を探してみませんか?</h3>
                    <p>Care Entry(ケアエントリー)は介護・福祉専門の求人サイトです(現在の対応エリアは沖縄県)。</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('seo.jobs.okinawa') }}" class="btn-cta-search">
                            <i class="bi bi-search"></i>求人を探す
                        </a>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">

                {{-- 関連求人 --}}
                @if(!empty($relatedJobs) && $relatedJobs->count() > 0)
                    <p style="font-size:0.9rem;font-weight:800;color:#1a1a2e;margin-bottom:12px;">
                        <i class="bi bi-briefcase-fill text-success me-1"></i>関連する求人
                        @if($article->area)<span class="text-muted fw-normal" style="font-size:0.78rem;">({{ $article->area->name }})</span>@endif
                        @if($article->jobType)<span class="text-muted fw-normal" style="font-size:0.78rem;">({{ $article->jobType->name }})</span>@endif
                    </p>
                    @foreach($relatedJobs as $j)
                        <a href="{{ route('lp.show', $j->token) }}" class="related-card mb-3" style="border-left:3px solid #06C755;">
                            <p class="related-card__title">{{ $j->seo_title ?: $j->title }}</p>
                            <p class="related-card__meta mb-0">
                                @foreach($j->jobAreas->take(1) as $ja)@if($ja->area)<i class="bi bi-geo-alt-fill me-1"></i>{{ $ja->area->name }}@endif @endforeach
                                @foreach($j->jobJobTypes->take(1) as $jt)@if($jt->jobType)・{{ $jt->jobType->name }}@endif @endforeach
                                @foreach($j->jobEmploymentTypes->take(1) as $et)@if($et->employmentType)・{{ $et->employmentType->name }}@endif @endforeach
                            </p>
                        </a>
                    @endforeach
                @endif

                {{-- 関連記事 --}}
                @if($related->isNotEmpty())
                    <p style="font-size:0.9rem;font-weight:800;color:#1a1a2e;margin-bottom:12px;margin-top:1.5rem;">
                        <i class="bi bi-file-text-fill text-primary me-1"></i>関連記事
                    </p>
                    @foreach($related as $r)
                        <a href="{{ route('articles.show', $r->slug) }}" class="related-card mb-3">
                            <p class="related-card__title">{{ $r->h1 }}</p>
                            <p class="related-card__meta mb-0">
                                <i class="bi bi-calendar3 me-1"></i>{{ $r->published_at->format('Y年m月d日') }}
                            </p>
                        </a>
                    @endforeach
                @endif

                {{-- 求人検索 --}}
                <div class="mt-3 p-3 bg-white rounded-3 border">
                    <p style="font-size:0.88rem;font-weight:800;margin-bottom:10px;">
                        <i class="bi bi-search text-primary me-1"></i>求人を探す
                    </p>
                    <a href="{{ route('seo.jobs.okinawa') }}"
                       style="display:block;background:var(--color-primary);color:#fff;border-radius:8px;padding:10px 16px;text-align:center;font-size:0.88rem;font-weight:800;text-decoration:none;">
                        介護・福祉求人一覧(沖縄エリア)
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<footer class="art-footer">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">介護・福祉専門の求人サービス【対応エリア:沖縄県】</span>
        </div>
        <div class="art-footer__links">
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
</body>
</html>
