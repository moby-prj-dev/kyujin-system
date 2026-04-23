<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>沖縄の介護・福祉お役立ち情報｜Care Entry</title>
    <meta name="description" content="沖縄の介護・福祉業界の仕事情報。職種別の仕事内容・給与相場、介護資格の取り方、未経験からの就職など、求職者向けの情報をまとめています。">
    <link rel="canonical" href="{{ url()->current() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary: #1a73e8;
            --color-primary-dark: #0d47a1;
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

        .art-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: #fff; padding: 40px 0 36px;
        }
        .art-header__title { font-size: clamp(1.3rem, 3.5vw, 1.9rem); font-weight: 900; margin-bottom: 8px; }
        .art-header__desc { font-size: 0.93rem; opacity: .88; }

        .art-main { padding: 32px 0 60px; }

        .category-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.75rem; font-weight: 700;
            padding: 3px 10px; border-radius: 4px; margin-bottom: 14px;
        }
        .category-label--industry    { background: #e8f0fb; color: var(--color-primary); }
        .category-label--job_type    { background: #e8f5e9; color: #2e7d32; }
        .category-label--area        { background: #fff3e0; color: #e65100; }
        .category-label--qualification { background: #f3e5f5; color: #6a1b9a; }
        .category-label--beginner    { background: #e0f7fa; color: #00695c; }

        .article-card {
            background: #fff; border-radius: 12px; border: 1.5px solid var(--color-border);
            padding: 20px; margin-bottom: 14px; transition: .2s;
            text-decoration: none; color: var(--color-text); display: block;
        }
        .article-card:hover {
            box-shadow: 0 4px 16px rgba(26,115,232,.1);
            transform: translateY(-1px); color: var(--color-text);
            border-color: var(--color-primary);
        }
        .article-card__title { font-size: 1rem; font-weight: 800; margin-bottom: 8px; }
        .article-card__meta  { font-size: 0.8rem; color: var(--color-muted); }

        .section-title {
            font-size: 1rem; font-weight: 800; color: #1a1a2e;
            margin-bottom: 16px; padding-bottom: 8px;
            border-bottom: 2px solid var(--color-border);
        }

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

<div class="art-header">
    <div class="container">
        <p style="font-size:0.78rem;opacity:.8;margin-bottom:10px;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.8);text-decoration:none;">ホーム</a>
            <span class="mx-1">›</span>お役立ち情報
        </p>
        <h1 class="art-header__title">沖縄の介護・福祉お役立ち情報</h1>
        <p class="art-header__desc">職種別の仕事内容・給与相場、資格取得、未経験からの就職など、求職者向けの情報をまとめています。</p>
    </div>
</div>

<div class="art-main">
    <div class="container">

        @php
        $categoryMeta = [
            'industry'      => ['label' => '業界情報',   'icon' => 'bi-bar-chart-fill'],
            'job_type'      => ['label' => '職種別情報', 'icon' => 'bi-briefcase-fill'],
            'area'          => ['label' => 'エリア情報', 'icon' => 'bi-geo-alt-fill'],
            'qualification' => ['label' => '資格・研修', 'icon' => 'bi-award-fill'],
            'beginner'      => ['label' => '未経験・転職', 'icon' => 'bi-person-plus-fill'],
        ];
        @endphp

        @forelse($articles as $category => $items)
            @php $meta = $categoryMeta[$category] ?? ['label' => $category, 'icon' => 'bi-file-text']; @endphp
            <p class="section-title">
                <i class="{{ $meta['icon'] }} me-1" style="color:var(--color-primary)"></i>{{ $meta['label'] }}
            </p>
            <div class="row g-3 mb-5">
                @foreach($items as $article)
                    <div class="col-md-6">
                        <a href="{{ route('articles.show', $article->slug) }}" class="article-card">
                            <span class="category-label category-label--{{ $category }}">
                                <i class="{{ $meta['icon'] }}"></i>{{ $meta['label'] }}
                            </span>
                            <p class="article-card__title">{{ $article->h1 }}</p>
                            <p class="article-card__meta mb-0">
                                <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at->format('Y年m月d日') }}
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-text" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px;"></i>
                <p>記事を準備中です</p>
            </div>
        @endforelse

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
