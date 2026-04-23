<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>沖縄の介護・福祉求人をハローワークで探す｜Care Entry</title>
    <meta name="description" content="沖縄の介護・福祉求人をハローワークで探す方向けの案内ページ。那覇・南部・中部・北部・離島エリア別にハローワークの求人検索へ案内します。">
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
        .hw-header__desc {
            font-size: 0.93rem;
            opacity: .88;
            margin-bottom: 24px;
        }
        .btn-hw {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #b71c1c;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 12px 28px;
            text-decoration: none;
            transition: .2s;
        }
        .btn-hw:hover { background: #ffebee; color: #b71c1c; }
        .btn-care {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--color-primary);
            color: #fff;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 12px 24px;
            text-decoration: none;
            transition: .2s;
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

        .area-card {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 20px;
            margin-bottom: 14px;
            transition: .2s;
            text-decoration: none;
            color: var(--color-text);
            display: block;
        }
        .area-card:hover {
            border-color: var(--color-hw);
            box-shadow: 0 4px 16px rgba(229,57,53,.1);
            transform: translateY(-1px);
            color: var(--color-text);
        }
        .area-card__region {
            font-size: 0.75rem;
            color: var(--color-muted);
            margin-bottom: 4px;
        }
        .area-card__name {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .area-card__desc {
            font-size: 0.82rem;
            color: var(--color-muted);
        }
        .area-card__arrow {
            color: var(--color-hw);
            font-size: 0.9rem;
        }

        .hw-guide {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--color-border);
            padding: 24px;
        }
        .hw-guide__step {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        .hw-guide__step:last-child { margin-bottom: 0; }
        .hw-guide__num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-hw);
            color: #fff;
            font-size: 0.88rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
            background: #1a1a2e;
            color: #aaa;
            padding: 28px 0;
            font-size: 0.82rem;
            text-align: center;
        }
        .hw-footer a { color: #aaa; text-decoration: none; }
        .hw-footer a:hover { color: #fff; }
        .hw-footer__links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px 16px;
            margin-bottom: 12px;
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

<div class="hw-header">
    <div class="container">
        <p class="hw-header__breadcrumb">
            <a href="{{ route('home') }}">ホーム</a>
            <span class="mx-1">›</span>沖縄の介護・福祉求人をハローワークで探す
        </p>
        <h1 class="hw-header__title">沖縄の介護・福祉求人を<br>ハローワークで探す</h1>
        <p class="hw-header__desc">エリアを選んでハローワークの求人検索へ。Care Entryの掲載求人もあわせてご確認ください。</p>
        <div class="d-flex flex-wrap gap-3">
            <a href="https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do" target="_blank" rel="noopener" class="btn-hw">
                <i class="bi bi-box-arrow-up-right"></i>ハローワーク求人検索を開く
            </a>
            <a href="{{ route('seo.jobs.okinawa') }}" class="btn-care">
                <i class="bi bi-search"></i>Care Entryで探す
            </a>
        </div>
    </div>
</div>

<div class="hw-main">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">

                {{-- エリア別リンク --}}
                @foreach($areasByRegion as $region => $areas)
                    <p class="section-title"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $region }}エリア</p>
                    <div class="row g-2 mb-4">
                        @foreach($areas as $area)
                            <div class="col-sm-6">
                                <a href="{{ route('hellowork.area', $area->slug) }}" class="area-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="area-card__region">{{ $region }}</p>
                                            <p class="area-card__name">{{ $area->name }}の介護・福祉求人</p>
                                            <p class="area-card__desc mb-0">ハローワークで{{ $area->name }}の求人を探す</p>
                                        </div>
                                        <i class="bi bi-chevron-right area-card__arrow mt-1"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
            <div class="col-lg-4">

                {{-- ハローワーク活用ガイド --}}
                <div class="hw-guide mb-4">
                    <p class="section-title mb-3"><i class="bi bi-info-circle-fill text-danger me-1"></i>ハローワークの使い方</p>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">1</div>
                        <div class="hw-guide__text"><strong>ハローワーク求人検索を開く</strong><br>上のボタンからアクセスしてください。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">2</div>
                        <div class="hw-guide__text"><strong>都道府県・市区町村を選択</strong><br>「沖縄県」→ご希望のエリアを選択します。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">3</div>
                        <div class="hw-guide__text"><strong>職種・条件を入力</strong><br>「介護」「福祉」などのキーワードで絞り込めます。</div>
                    </div>
                    <div class="hw-guide__step">
                        <div class="hw-guide__num">4</div>
                        <div class="hw-guide__text"><strong>気になる求人はハローワークへ</strong><br>応募はお近くのハローワーク窓口で行います。</div>
                    </div>
                </div>

                <div class="hw-note">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                    <strong>Care Entryなら登録不要・LINE応募OK</strong><br>
                    当サイト掲載の求人はLINEまたはフォームから直接応募できます。
                    <a href="{{ route('seo.jobs.okinawa') }}" class="d-block mt-2 text-primary fw-bold">
                        <i class="bi bi-arrow-right-circle me-1"></i>Care Entryの求人を見る
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
