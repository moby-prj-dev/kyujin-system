<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Entry（ケアエントリー）｜介護・福祉の成果報酬型求人サービス</title>
    <meta name="description" content="Care Entryは介護・福祉専門の成果報酬型求人サービスです。掲載無料、応募が来てから課金。LINE応募対応でかんたん応募。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #0d47a1;
            --accent: #06C755;
            --soft: #f0f7ff;
            --text: #2d2d2d;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif; color: var(--text); margin: 0; }

        /* ナビ */
        .site-nav { background: #fff; border-bottom: 1px solid #e8eef7; padding: 14px 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
        .site-nav .brand { font-size: 1.2rem; font-weight: 800; color: var(--primary); text-decoration: none; }
        .site-nav .brand span { color: #555; font-size: 0.78rem; font-weight: 400; margin-left: 6px; }
        .btn-nav { font-size: 0.88rem; font-weight: 700; padding: 8px 20px; border-radius: 20px; }

        /* ヒーロー */
        .hero { background: linear-gradient(135deg, #e8f0fe 0%, #f0f7ff 50%, #e8f5e9 100%); padding: 80px 0 64px; }
        .hero .badge-label { display: inline-block; background: #fff; color: var(--primary); font-size: 0.82rem; font-weight: 700; padding: 5px 14px; border-radius: 20px; border: 1.5px solid #c5d8f8; margin-bottom: 20px; }
        .hero h1 { font-size: clamp(1.6rem, 4vw, 2.5rem); font-weight: 900; line-height: 1.45; color: #1a1a2e; margin-bottom: 16px; }
        .hero h1 em { color: var(--primary); font-style: normal; }
        .hero .sub { font-size: 1rem; color: #555; line-height: 1.75; margin-bottom: 36px; }
        .btn-hero-company { background: var(--primary); color: #fff; border: none; border-radius: 30px; font-size: 1.05rem; font-weight: 800; padding: 16px 36px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: .2s; }
        .btn-hero-company:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }
        .btn-hero-seeker { background: #fff; color: var(--primary); border: 2px solid var(--primary); border-radius: 30px; font-size: 1rem; font-weight: 700; padding: 14px 30px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: .2s; }
        .btn-hero-seeker:hover { background: var(--soft); color: var(--primary); }
        .hero-note { font-size: 0.78rem; color: #888; margin-top: 14px; }
        .hero-visual { background: linear-gradient(135deg,#bbdefb,#c8e6c9); border-radius: 16px; height: 300px; display: flex; align-items: center; justify-content: center; }
        .hero-visual .icon-wrap { text-align: center; color: #fff; }
        .hero-visual .icon-wrap i { font-size: 5rem; opacity: .7; }
        .hero-visual .icon-wrap p { font-size: 1rem; font-weight: 700; opacity: .8; margin: 0; }

        /* セクション共通 */
        section { padding: 72px 0; }
        .section-label { font-size: 0.82rem; font-weight: 700; color: var(--primary); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 8px; }
        .section-title { font-size: clamp(1.3rem, 3vw, 1.9rem); font-weight: 900; line-height: 1.4; margin-bottom: 12px; }
        .section-sub { font-size: 0.95rem; color: #666; line-height: 1.8; margin-bottom: 0; }

        /* ステップ */
        .steps-section { background: #fff; }
        .step-card { text-align: center; padding: 32px 20px; }
        .step-num { width: 52px; height: 52px; background: var(--primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 900; margin: 0 auto 16px; }
        .step-card h4 { font-size: 1rem; font-weight: 800; margin-bottom: 8px; }
        .step-card p { font-size: 0.88rem; color: #666; line-height: 1.7; margin: 0; }
        .step-arrow { display: flex; align-items: center; justify-content: center; color: #bbb; font-size: 1.5rem; }

        /* 企業メリット */
        .company-section { background: var(--soft); }
        .merit-card { background: #fff; border-radius: 12px; padding: 28px 24px; height: 100%; border: 1.5px solid #e0ebf8; transition: .2s; }
        .merit-card:hover { box-shadow: 0 4px 16px rgba(26,115,232,.1); transform: translateY(-2px); }
        .merit-icon { width: 52px; height: 52px; background: #e8f0fe; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--primary); margin-bottom: 14px; }
        .merit-card h5 { font-size: 1rem; font-weight: 800; margin-bottom: 8px; }
        .merit-card p { font-size: 0.87rem; color: #666; line-height: 1.7; margin: 0; }

        /* 求職者メリット */
        .seeker-section { background: #fff; }
        .seeker-card { display: flex; gap: 16px; align-items: flex-start; padding: 20px 0; border-bottom: 1px solid #f0f0f0; }
        .seeker-card:last-child { border-bottom: none; }
        .seeker-icon { width: 44px; height: 44px; border-radius: 10px; background: #e8f5e9; color: #2e7d32; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .seeker-card h5 { font-size: 0.97rem; font-weight: 800; margin-bottom: 4px; }
        .seeker-card p { font-size: 0.86rem; color: #666; margin: 0; line-height: 1.6; }

        /* 中盤CTA */
        .mid-cta { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; text-align: center; padding: 72px 0; }
        .mid-cta h2 { font-size: clamp(1.3rem, 3vw, 1.9rem); font-weight: 900; margin-bottom: 12px; }
        .mid-cta p { opacity: .85; margin-bottom: 36px; }
        .btn-cta-white { background: #fff; color: var(--primary); border-radius: 30px; font-size: 1.05rem; font-weight: 800; padding: 16px 40px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: .2s; }
        .btn-cta-white:hover { background: #f0f7ff; color: var(--primary); transform: translateY(-1px); }
        .btn-cta-outline-white { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,.7); border-radius: 30px; font-size: 0.97rem; font-weight: 700; padding: 14px 32px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: .2s; }
        .btn-cta-outline-white:hover { background: rgba(255,255,255,.1); color: #fff; }

        /* LINE応募 */
        .line-section { background: #f0faf3; }
        .line-badge { background: var(--accent); color: #fff; font-size: 0.85rem; font-weight: 700; padding: 4px 14px; border-radius: 20px; display: inline-block; margin-bottom: 12px; }

        /* FAQ */
        .faq-section { background: #fafafa; }
        .accordion-button { font-weight: 700; font-size: 0.97rem; }
        .accordion-button:not(.collapsed) { color: var(--primary); background: var(--soft); }
        .accordion-body { font-size: 0.92rem; color: #555; line-height: 1.8; }

        /* 最終CTA */
        .final-cta { background: #fff; border-top: 1px solid #eee; padding: 72px 0; text-align: center; }
        .final-cta h2 { font-size: clamp(1.2rem, 3vw, 1.75rem); font-weight: 900; margin-bottom: 10px; }
        .final-cta p { color: #666; margin-bottom: 36px; }

        /* フッター */
        footer { background: #1a1a2e; color: #aaa; padding: 32px 0; font-size: 0.85rem; text-align: center; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: #fff; }

        @media (max-width: 767px) {
            .hero { padding: 52px 0 48px; }
            section { padding: 52px 0; }
            .step-arrow { display: none; }
            .hero-visual { height: 180px; margin-top: 36px; }
        }
    </style>
</head>
<body>

{{-- ナビゲーション --}}
<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="brand">
            <img src="/images/logo.svg" alt="Care Entry ケア・エントリー" height="52">
        </a>
        <a href="{{ route('jobs.create') }}" class="btn btn-nav btn-primary">
            <i class="bi bi-plus-circle me-1"></i>無料で掲載する
        </a>
    </div>
</nav>

{{-- ① ファーストビュー --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="badge-label"><i class="bi bi-heart-fill me-1"></i>介護・福祉専門の求人サービス</div>
                <h1>採用コスト<em>0円</em>から始める<br>介護求人掲載</h1>
                <p class="sub">
                    掲載は完全無料。<br>
                    応募が届いてから、初めて費用が発生する成果報酬型。<br>
                    LINE応募対応で、求職者がかんたんに応募できます。
                </p>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <a href="{{ route('jobs.create') }}" class="btn-hero-company">
                        <i class="bi bi-building"></i>無料で掲載する
                    </a>
                    <a href="#jobs" class="btn-hero-seeker">
                        <i class="bi bi-search"></i>求人を見る
                    </a>
                </div>
                <p class="hero-note"><i class="bi bi-shield-check me-1"></i>初期費用・月額費用は一切かかりません</p>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="icon-wrap">
                        <i class="bi bi-people-fill"></i>
                        <p>介護・福祉のお仕事探し</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ② サービスの仕組み --}}
<section class="steps-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">HOW IT WORKS</div>
            <h2 class="section-title">たった3ステップで採用まで</h2>
            <p class="section-sub">難しい設定は不要。フォームに入力するだけで求人を公開できます。</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-3 col-6">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h4>求人を登録</h4>
                    <p>フォームに情報を入力して送信。AIがタイトルと本文を自動生成します。</p>
                </div>
            </div>
            <div class="col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-chevron-right"></i></div>
            <div class="col-md-3 col-6">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h4>求人が公開される</h4>
                    <p>メール認証後すぐに公開。LINE応募と通常フォームの両方に対応。</p>
                </div>
            </div>
            <div class="col-md-1 d-none d-md-flex step-arrow"><i class="bi bi-chevron-right"></i></div>
            <div class="col-md-3 col-6 mx-auto">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h4>応募来てから課金</h4>
                    <p>応募が届いたときだけ費用が発生。成果のない掲載は完全無料。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ③ 企業向けメリット --}}
<section class="company-section">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-5">
                <div class="section-label">FOR COMPANIES</div>
                <h2 class="section-title">掲載企業が選ぶ<br>5つの理由</h2>
                <p class="section-sub">採用コストを最小化しながら、質の高い応募者に出会えます。</p>
                <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-lg rounded-pill mt-4 px-4">
                    <i class="bi bi-plus-circle me-1"></i>無料で掲載をはじめる
                </a>
            </div>
            <div class="col-lg-7 mt-4 mt-lg-0">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-currency-yen"></i></div>
                            <h5>初期費用・月額費用0円</h5>
                            <p>掲載にかかる費用は一切ありません。応募が来るまでは完全無料で使えます。</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <h5>成果報酬型で無駄なし</h5>
                            <p>費用が発生するのは応募が届いたときだけ。採用できなかった場合のリスクがありません。</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-lightning-charge"></i></div>
                            <h5>最短当日から掲載開始</h5>
                            <p>フォーム入力後、メール認証するだけですぐに公開。面倒な審査はありません。</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-chat-dots"></i></div>
                            <h5>LINE応募で応募率アップ</h5>
                            <p>求職者がLINEで気軽に応募できる仕組みで、応募のハードルを大幅に下げます。</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-person-check"></i></div>
                            <h5>ログイン不要で簡単管理</h5>
                            <p>パスワード不要。メールに届くリンクから求人の編集・確認ができます。</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="merit-card">
                            <div class="merit-icon"><i class="bi bi-stars"></i></div>
                            <h5>AIが求人文を自動生成</h5>
                            <p>チェックを選ぶだけでAIが魅力的な求人タイトルと本文を自動で作成します。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ④ 求職者向けメリット --}}
<section class="seeker-section" id="jobs">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-label">FOR JOB SEEKERS</div>
                <h2 class="section-title">かんたんに<br>応募できます</h2>
                <p class="section-sub">会員登録不要。名前と電話番号だけで応募完了。LINEを使えばさらに簡単です。</p>
            </div>
            <div class="col-lg-7">
                <div class="seeker-card">
                    <div class="seeker-icon"><i class="bi bi-phone"></i></div>
                    <div>
                        <h5>LINEでかんたん応募</h5>
                        <p>使い慣れたLINEアプリから応募できます。担当者と直接やり取りも可能です。</p>
                    </div>
                </div>
                <div class="seeker-card">
                    <div class="seeker-icon"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <h5>会員登録・ログイン不要</h5>
                        <p>アカウント作成は必要ありません。気になった求人にすぐ応募できます。</p>
                    </div>
                </div>
                <div class="seeker-card">
                    <div class="seeker-icon"><i class="bi bi-pencil-square"></i></div>
                    <div>
                        <h5>入力は最小限</h5>
                        <p>お名前と電話番号（またはLINE）だけで応募完了。フォームは30秒で入力できます。</p>
                    </div>
                </div>
                <div class="seeker-card">
                    <div class="seeker-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h5>沖縄エリアの介護求人に特化</h5>
                        <p>沖縄県内の介護・福祉求人を専門に掲載。自分に合う職場が見つかります。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ⑤ 中盤CTA --}}
<section class="mid-cta">
    <div class="container">
        <h2>今すぐ無料で求人掲載をはじめる</h2>
        <p>初期費用0円・月額費用0円。応募が来てから課金されます。</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('jobs.create') }}" class="btn-cta-white">
                <i class="bi bi-building"></i>無料で求人掲載をはじめる
            </a>
            <a href="#jobs" class="btn-cta-outline-white">
                <i class="bi bi-search"></i>求人を見る
            </a>
        </div>
    </div>
</section>

{{-- LINE応募説明 --}}
<section class="line-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="line-badge"><i class="bi bi-chat-fill me-1"></i>LINE応募対応</span>
                <h2 class="section-title mt-2">求職者がLINEで<br>気軽に応募できる</h2>
                <p class="section-sub">
                    Care EntryはLINE応募に対応しています。<br>
                    求職者はLINEアプリから1タップで応募でき、応募率が大幅にアップします。<br>
                    担当者とのやり取りもLINEで行えるので、採用までがスムーズです。
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-white rounded-3 text-center shadow-sm h-100">
                            <i class="bi bi-chat-dots-fill text-success fs-2 mb-2 d-block"></i>
                            <div class="fw-bold small">LINEで応募</div>
                            <div class="text-muted" style="font-size:.8rem">1タップで応募完了</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-white rounded-3 text-center shadow-sm h-100">
                            <i class="bi bi-pencil-fill text-primary fs-2 mb-2 d-block"></i>
                            <div class="fw-bold small">フォームで応募</div>
                            <div class="text-muted" style="font-size:.8rem">LINEなしでも安心</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-white rounded-3 text-center shadow-sm h-100">
                            <i class="bi bi-bell-fill text-warning fs-2 mb-2 d-block"></i>
                            <div class="fw-bold small">応募通知メール</div>
                            <div class="text-muted" style="font-size:.8rem">すぐに確認できる</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-white rounded-3 text-center shadow-sm h-100">
                            <i class="bi bi-shield-check-fill text-primary fs-2 mb-2 d-block"></i>
                            <div class="fw-bold small">個人情報保護</div>
                            <div class="text-muted" style="font-size:.8rem">安全に管理します</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ⑥ FAQ --}}
<section class="faq-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">FAQ</div>
            <h2 class="section-title">よくある質問</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                本当に無料で始められますか？
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                はい、掲載・登録にかかる費用は一切ありません。初期費用・月額費用ともに0円です。費用が発生するのは応募が届いたときのみです。
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                いつ料金が発生しますか？
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                求職者から応募が届いたときに費用が発生します。応募がない場合は掲載期間中も費用は0円です。詳しくは掲載登録時に表示される課金説明をご確認ください。
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                掲載後の修正はできますか？
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                はい、掲載後もいつでも編集できます。登録時に届くメールに含まれる編集用リンクからアクセスするだけで、タイトルや求人内容を自由に変更できます。
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                応募はどのように届きますか？
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                応募が届くと登録したメールアドレスに通知が送られます。また、管理ページから応募者情報を一覧で確認できます。LINEでの応募の場合はLINEでのやり取りも可能です。
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                求職者は会員登録が必要ですか？
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                不要です。名前と電話番号（またはLINE）だけで応募できます。アカウントの作成やパスワードの設定は一切必要ありません。
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ⑦ 最終CTA --}}
<section class="final-cta">
    <div class="container">
        <div class="section-label">GET STARTED</div>
        <h2 class="section-title mt-1">まずは無料で<br>求人掲載をはじめましょう</h2>
        <p>初期費用0円・月額費用0円。<br>応募が来てから課金されるので、リスクなしで始められます。</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                <i class="bi bi-building me-1"></i>無料で求人掲載をはじめる
            </a>
            <a href="#jobs" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                <i class="bi bi-search me-1"></i>求人を見る
            </a>
        </div>
    </div>
</section>

{{-- フッター --}}
<footer>
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry</strong>
            <span class="ms-2">介護・福祉専門の成果報酬型求人サービス</span>
        </div>
        <div class="mb-3">
            <a href="{{ route('jobs.create') }}" class="me-3">求人を掲載する</a>
            <a href="#jobs" class="me-3">求人を見る</a>
        </div>
        <p class="mb-0" style="font-size:.8rem;">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
