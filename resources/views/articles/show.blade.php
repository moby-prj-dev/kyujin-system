<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
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
                         style="width:100%;border-radius:10px;margin-bottom:20px;object-fit:cover;max-height:300px;">
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

                    <div class="article-body">
                        @foreach(explode("\n\n", $article->body) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- CTA --}}
                <div class="cta-box">
                    <h3>沖縄の介護・福祉求人を探してみませんか？</h3>
                    <p>Care Entry（ケアエントリー）では沖縄の介護・福祉求人を掲載しています。<br>登録不要でLINEからそのまま応募できます。</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('seo.jobs.okinawa') }}" class="btn-cta-search">
                            <i class="bi bi-search"></i>求人を探す
                        </a>
                        <a href="https://lin.ee/" class="btn-cta-line">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                            LINEで相談する
                        </a>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">

                {{-- 関連記事 --}}
                @if($related->isNotEmpty())
                    <p style="font-size:0.9rem;font-weight:800;color:#1a1a2e;margin-bottom:12px;">
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
                        沖縄の介護・福祉求人一覧
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
            <span class="ms-2">沖縄の介護・福祉専門の求人サービス</span>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
