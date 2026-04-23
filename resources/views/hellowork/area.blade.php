<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $area->name }}の介護・福祉求人をハローワークで探す｜Care Entry</title>
    <meta name="description" content="{{ $area->name }}の介護・福祉求人をハローワークで探す方向けの案内ページ。職種・雇用形態の検索方法をわかりやすく案内します。Care Entryの掲載求人もあわせてご確認ください。">
    <link rel="canonical" href="{{ url()->current() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-primary: #1a73e8;
            --color-primary-dark: #0d47a1;
            --color-hw: #e53935;
            --color-text: #2d2d2d;
            --color-muted: #6c757d;
            --color-border: #dee2e6;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            color: var(--color-text);
            margin: 0;
            background: #f5f7fa;
        }
        .hw-nav {
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .hw-nav__logo img { height: 40px; }
        .hw-nav__link {
            font-size: 0.82rem;
            color: var(--color-muted);
            text-decoration: none;
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 6px 14px;
        }
        .hw-nav__link:hover { background: #f8f9fa; color: var(--color-text); }

        .hw-header {
            background: linear-gradient(135deg, #b71c1c 0%, #e53935 100%);
            color: #fff;
            padding: 40px 0 36px;
        }
        .hw-header__breadcrumb {
            font-size: 0.78rem;
            opacity: .8;
            margin-bottom: 10px;
        }
        .hw-header__breadcrumb a { color: rgba(255,255,255,.8); text-decoration: none; }
        .hw-header__title {
            font-size: clamp(1.3rem, 3.5vw, 1.9rem);
            font-weight: 900;
            margin-bottom: 8px;
        }
        .hw-header__desc { font-size: 0.93rem; opacity: .88; margin-bottom: 24px; }
        .btn-hw {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: #b71c1c; border-radius: 30px;
            font-size: 0.95rem; font-weight: 800; padding: 12px 28px;
            text-decoration: none; transition: .2s;
        }
        .btn-hw:hover { background: #ffebee; color: #b71c1c; }
        .btn-care {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--color-primary); color: #fff; border-radius: 30px;
            font-size: 0.95rem; font-weight: 800; padding: 12px 24px;
            text-decoration: none; transition: .2s;
        }
        .btn-care:hover { background: var(--color-primary-dark); color: #fff; }

        .hw-main { padding: 32px 0 60px; }

        .section-title {
            font-size: 1rem;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--color-border);
        }

        .search-tip {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 20px;
            margin-bottom: 14px;
        }
        .search-tip__title {
            font-size: 0.9rem;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .search-tip__keyword {
            display: inline-block;
            background: #ffebee;
            color: #b71c1c;
            border-radius: 4px;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 3px 10px;
            margin: 3px 3px 3px 0;
        }

        .area-nav {
            background: #fff;
            border-bottom: 1px solid var(--color-border);
            padding: 14px 0;
            overflow-x: auto;
        }
        .area-nav__inner {
            display: flex; gap: 8px; white-space: nowrap; padding: 0 4px;
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
            background: var(--color-hw);
            color: #fff;
            border-color: var(--color-hw);
        }

        .hw-guide {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 24px;
        }
        .hw-guide__step {
            display: flex; gap: 14px; align-items: flex-start; margin-bottom: 18px;
        }
        .hw-guide__step:last-child { margin-bottom: 0; }
        .hw-guide__num {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--color-hw); color: #fff;
            font-size: 0.88rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .hw-guide__text { font-size: 0.88rem; line-height: 1.7; }
        .hw-guide__text strong { color: #1a1a2e; }

        .hw-note {
            background: #fff8f8;
            border: 1.5px solid #ffcdd2;
            border-radius: 10px;
            padding: 16px 20px;
            font-size: 0.84rem;
            color: #555;
            margin-top: 20px;
        }

        .hw-footer {
            background: #1a1a2e; color: #aaa;
            padding: 28px 0; font-size: 0.82rem; text-align: center;
        }
        .hw-footer a { color: #aaa; text-decoration: none; }
        .hw-footer a:hover { color: #fff; }
        .hw-footer__links {
            display: flex; justify-content: center; flex-wrap: wrap;
            gap: 5px 16px; margin-bottom: 12px;
        }
    </style>
</head>
<body>

<nav class="hw-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="hw-nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
        <a href="{{ route('home') }}" class="hw-nav__link">
            <i class="bi bi-house me-1"></i>トップへ
        </a>
    </div>
</nav>

{{-- エリアナビ --}}
<div class="area-nav">
    <div class="container">
        <div class="area-nav__inner">
            <a href="{{ route('hellowork.index') }}" class="area-nav__link">沖縄県すべて</a>
            @foreach($areasByRegion as $region => $areas)
                @foreach($areas as $a)
                    <a href="{{ route('hellowork.area', $a->slug) }}"
                       class="area-nav__link {{ $a->slug === $area->slug ? 'area-nav__link--active' : '' }}">
                        {{ $a->name }}
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
</div>

<div class="hw-header">
    <div class="container">
        <p class="hw-header__breadcrumb">
            <a href="{{ route('home') }}">ホーム</a>
            <span class="mx-1">›</span>
            <a href="{{ route('hellowork.index') }}">沖縄の介護・福祉求人をハローワークで探す</a>
            <span class="mx-1">›</span>{{ $area->name }}
        </p>
        <h1 class="hw-header__title">{{ $area->name }}の介護・福祉求人を<br>ハローワークで探す</h1>
        <p class="hw-header__desc">{{ $area->name }}（{{ $area->region }}エリア）の介護・福祉求人をハローワークで探す方向けの案内です。</p>
        <div class="d-flex flex-wrap gap-3">
            <a href="https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do" target="_blank" rel="noopener" class="btn-hw">
                <i class="bi bi-box-arrow-up-right"></i>ハローワーク求人検索を開く
            </a>
            <a href="{{ route('seo.jobs.area', $area->slug) }}" class="btn-care">
                <i class="bi bi-search"></i>Care Entryで{{ $area->name }}の求人を見る
            </a>
        </div>
    </div>
</div>

<div class="hw-main">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">

                <p class="section-title"><i class="bi bi-search text-danger me-1"></i>{{ $area->name }}でよく検索される職種キーワード</p>

                @foreach($jobTypes as $category => $types)
                    <div class="search-tip">
                        <p class="search-tip__title"><i class="bi bi-briefcase me-1"></i>{{ $category }}</p>
                        <div>
                            @foreach($types as $type)
                                <span class="search-tip__keyword">{{ $type->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="alert alert-light border mt-3" style="font-size:0.84rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    上記のキーワードをハローワークの求人検索に入力し、勤務地で「<strong>沖縄県 › {{ $area->name }}</strong>」を選択してください。
                </div>

            </div>
            <div class="col-lg-4">

                <div class="hw-guide mb-4">
                    <p class="section-title mb-3"><i class="bi bi-info-circle-fill text-danger me-1"></i>{{ $area->name }}での検索手順</p>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">1</div>
                        <div class="hw-guide__text"><strong>ハローワーク求人検索を開く</strong><br>上のボタンからアクセスしてください。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">2</div>
                        <div class="hw-guide__text"><strong>勤務地で「{{ $area->name }}」を選択</strong><br>都道府県「沖縄県」→ 市区町村「{{ $area->name }}」を選びます。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">3</div>
                        <div class="hw-guide__text"><strong>職種キーワードを入力</strong><br>左側の職種キーワードを参考に絞り込んでください。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">4</div>
                        <div class="hw-guide__text"><strong>応募はハローワーク窓口で</strong><br>お近くのハローワーク沖縄（那覇市おもろまち）で相談できます。</div>
                    </div>
                </div>

                <div class="hw-note">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                    <strong>Care Entryならもっと手軽に</strong><br>
                    当サイト掲載の{{ $area->name }}求人はLINEまたはフォームから直接応募できます。登録不要です。
                    <a href="{{ route('seo.jobs.area', $area->slug) }}" class="d-block mt-2 text-primary fw-bold">
                        <i class="bi bi-arrow-right-circle me-1"></i>{{ $area->name }}の求人をCare Entryで見る
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<footer class="hw-footer">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry（ケアエントリー）</strong>
            <span class="ms-2">沖縄の介護・福祉専門の求人サービス</span>
        </div>
        <div class="hw-footer__links">
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
