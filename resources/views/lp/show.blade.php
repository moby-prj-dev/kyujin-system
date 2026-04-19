<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->seo_title }}</title>
    <meta name="description" content="{{ $job->meta_description }}">
    <meta property="og:title" content="{{ $job->seo_title }}">
    <meta property="og:description" content="{{ $job->meta_description }}">
    <meta property="og:type" content="website">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary:      #1a73e8;
            --primary-dark: #0d47a1;
            --soft:         #f0f7ff;
            --border:       #d6e4f7;
            --text:         #2d2d2d;
            --muted:        #6c757d;
        }
        body {
            background: #f5f7fa;
            color: var(--text);
            font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;
        }

        /* ナビ */
        .site-nav {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 13px 0;
            position: sticky;
            top: 0;
            z-index: 300;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
        }
        .site-nav .brand {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .site-nav .brand small { font-size: 0.72rem; font-weight: 400; color: var(--muted); }

        /* LPラッパー */
        .lp-wrap { max-width: 640px; margin: 0 auto; }

        /* ヘッダー */
        .lp-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 2rem 1.5rem 1.8rem;
        }
        .lp-header .area-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.82rem;
            background: rgba(255,255,255,.22);
            border-radius: 20px;
            padding: 0.25rem 0.85rem;
            margin-bottom: 0.85rem;
        }
        .lp-header h1 { font-size: 1.5rem; font-weight: 800; line-height: 1.55; margin-bottom: 0.8rem; }
        .lp-header .meta-badges { display: flex; flex-wrap: wrap; gap: 0.45rem; }
        .meta-badge { font-size: 0.8rem; font-weight: 600; padding: 0.28rem 0.8rem; border-radius: 4px; }
        .badge-job-type  { background: rgba(255,255,255,.9); color: var(--primary); }
        .badge-emp-type  { background: #ffd54f; color: #5d4037; }

        /* コンテンツ */
        .lp-content { padding: 1rem 1rem 180px; }

        /* セクションカード */
        .section-card {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid var(--border);
            box-shadow: 0 1px 6px rgba(26,115,232,.05);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--soft);
        }

        /* 情報行 */
        .info-row { display: flex; align-items: flex-start; padding: 0.55rem 0; border-bottom: 1px solid #f2f2f2; font-size: 0.93rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--muted); width: 100px; flex-shrink: 0; font-weight: 500; }
        .info-value  { flex: 1; font-weight: 600; color: #222; }

        /* タグ */
        .tag-list { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .tag-item { font-size: 0.83rem; font-weight: 600; border-radius: 20px; padding: 0.3rem 0.9rem; }
        .tag-condition { background: var(--soft); color: var(--primary); }
        .tag-appeal    { background: #e6f4ea; color: #137333; }

        /* 本文 */
        .seo-body-text { font-size: 0.92rem; line-height: 1.85; color: #444; white-space: pre-line; }

        /* 写真 */
        .job-photo {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: block;
            background: #f0f0f0;
        }

        /* 応募方法 */
        .apply-method-item { display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.7rem 0; border-bottom: 1px solid #f2f2f2; font-size: 0.9rem; }
        .apply-method-item:last-child { border-bottom: none; }
        .apply-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; }
        .apply-icon-line { background: #e8faf0; color: #06C755; }
        .apply-icon-form { background: var(--soft); color: var(--primary); }
        .apply-method-title { font-weight: 700; color: #222; margin-bottom: 2px; }
        .apply-method-desc  { color: #666; font-size: 0.82rem; }

        /* 固定CTAバー */
        .cta-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--border);
            padding: 0.85rem 1rem;
            z-index: 200;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
        }
        .cta-inner { max-width: 640px; margin: 0 auto; }
        .btn-line-apply {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; background: #06C755; color: #fff; border: none;
            border-radius: 30px; font-size: 1rem; font-weight: 800;
            padding: 0.78rem 1rem; margin-bottom: 0.5rem; text-decoration: none;
        }
        .btn-line-apply:hover { background: #05a847; color: #fff; }
        .btn-form-apply {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; background: #fff; color: var(--primary);
            border: 2px solid var(--primary); border-radius: 30px;
            font-size: 1rem; font-weight: 800; padding: 0.7rem 1rem; text-decoration: none;
        }
        .btn-form-apply:hover { background: var(--primary); color: #fff; }
        .cta-note { text-align: center; font-size: 0.72rem; color: #aaa; margin-top: 0.45rem; }
        .line-icon { width: 22px; height: 22px; flex-shrink: 0; }

        /* フッター */
        .site-footer { background: #1a1a2e; color: #aaa; text-align: center; padding: 24px 0; font-size: 0.82rem; }
        .site-footer a { color: #aaa; text-decoration: none; }
        .site-footer a:hover { color: #fff; }
    </style>
</head>
<body>

{{-- ナビ --}}
<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="brand">
            Care Entry <small>ケア・エントリー</small>
        </a>
    </div>
</nav>

{{-- LPヘッダー --}}
<div class="lp-header">
    <div class="lp-wrap">
        <div class="area-badge"><i class="bi bi-geo-alt-fill"></i>{{ $job->jobAreas->first()?->area?->name ?? '未設定' }}</div>
        <h1>{{ $job->title }}</h1>
        @if($job->subtitle)
        <p class="mb-2 opacity-90" style="font-size:0.9rem;">{{ $job->subtitle }}</p>
        @endif
        <div class="meta-badges">
            @if($job->lp_tags)
                @foreach(array_slice($job->lp_tags, 0, 4) as $tag)
                    <span class="meta-badge badge-job-type">{{ $tag }}</span>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- コンテンツ --}}
<div class="lp-content">
    <div class="lp-wrap">

        @if($job->photo_path)
        <img src="{{ asset('storage/' . $job->photo_path) }}" alt="{{ $job->company_name }}" class="job-photo">
        @endif

        <div class="section-card">
            <div class="section-title"><i class="bi bi-briefcase-fill"></i> 求人情報</div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-geo-alt me-1 text-primary"></i>勤務地</span>
                <span class="info-value">{{ $job->jobAreas->map(fn($a) => $a->area->name)->implode('・') ?: '未設定' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-person-badge me-1 text-primary"></i>職種</span>
                <span class="info-value">{{ $job->jobJobTypes->map(fn($j) => $j->jobType->name)->implode('・') ?: '未設定' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-building me-1 text-primary"></i>雇用形態</span>
                <span class="info-value">{{ $job->jobEmploymentTypes->map(fn($e) => $e->employmentType->name)->implode('・') ?: '未設定' }}</span>
            </div>
        </div>

        @if($job->jobConditions->isNotEmpty())
        <div class="section-card">
            <div class="section-title"><i class="bi bi-check2-circle"></i> 勤務条件</div>
            <div class="tag-list">
                @foreach($job->jobConditions as $jc)
                    <span class="tag-item tag-condition">{{ $jc->condition->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($job->lp_tags && count($job->lp_tags) > 0)
        <div class="section-card">
            <div class="section-title"><i class="bi bi-star-fill"></i> アピールポイント</div>
            <div class="tag-list">
                @foreach($job->lp_tags as $tag)
                    <span class="tag-item tag-appeal">{{ $tag }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($job->description_generated)
        <div class="section-card">
            <div class="section-title"><i class="bi bi-file-text"></i> 仕事内容・詳細</div>
            <div class="seo-body-text">{{ $job->description_generated }}</div>
        </div>
        @endif

        <div class="section-card">
            <div class="section-title"><i class="bi bi-send-fill"></i> 応募方法</div>
            <div class="apply-method-item">
                <div class="apply-icon apply-icon-line">
                    <svg class="line-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                </div>
                <div>
                    <div class="apply-method-title">LINEで応募する</div>
                    <div class="apply-method-desc">LINEアプリを使って簡単に応募できます。担当者と直接やり取りが可能です。</div>
                </div>
            </div>
            <div class="apply-method-item">
                <div class="apply-icon apply-icon-form"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <div class="apply-method-title">フォームで応募する</div>
                    <div class="apply-method-desc">お名前・電話番号を入力するだけで応募完了。LINEを使わない方にも対応しています。</div>
                </div>
            </div>
            <p class="mb-0 mt-3" style="font-size:0.78rem;color:#aaa;text-align:center;">
                <i class="bi bi-shield-check me-1"></i>応募は無料です。個人情報は適切に管理されます。
            </p>
        </div>

    </div>
</div>

{{-- フッター --}}
<footer class="site-footer" style="margin-bottom:160px;">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry</strong>
            <span class="ms-2">介護・福祉専門の成果報酬型求人サービス</span>
        </div>
        <div class="mb-3 d-flex justify-content-center gap-3 flex-wrap" style="font-size:0.8rem;">
            <a href="/company">運営者情報</a>
            <a href="/privacy-policy">プライバシーポリシー</a>
            <a href="/terms">利用規約</a>
            <a href="/legal">特定商取引法に基づく表記</a>
        </div>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

{{-- 固定CTAバー --}}
<div class="cta-bar">
    <div class="cta-inner">
        <a href="/liff/{{ $job->token }}" class="btn-line-apply">
            <svg class="line-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
            LINEで応募する
        </a>
        <a href="/lp/{{ $job->token }}/apply" class="btn-form-apply">
            <i class="bi bi-pencil-square"></i>フォームで応募する
        </a>
        <p class="cta-note"><i class="bi bi-lock-fill me-1"></i>個人情報は安全に管理されます</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
