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
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募フォーム｜{{ $job->seo_title }}</title>
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
            --line:         #06C755;
        }
        body {
            background: #f5f7fa;
            color: var(--text);
            font-family: 'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;
        }
        .site-nav {
            background: #fff; border-bottom: 1px solid var(--border);
            padding: 13px 0; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
        }
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff; padding: 28px 0 24px; margin-bottom: 32px;
        }
        .page-header h1 { font-size: 1.3rem; font-weight: 800; margin: 0 0 4px; }
        .page-header p  { font-size: 0.85rem; opacity: .82; margin: 0; }

        .card-section {
            background: #fff; border-radius: 12px; padding: 24px 28px;
            margin-bottom: 18px; border: 1.5px solid var(--border);
            box-shadow: 0 1px 6px rgba(26,115,232,.05);
        }
        .section-title {
            font-size: 0.92rem; font-weight: 800; color: var(--primary);
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 1rem; padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--soft);
        }

        /* 条件バッジ */
        .cond-badge {
            display: inline-block; background: var(--soft); border: 1.5px solid var(--border);
            border-radius: 20px; font-size: 0.82rem; font-weight: 600;
            padding: 4px 12px; margin: 3px 3px 3px 0; color: var(--text);
        }
        .cond-badge--match { background: #e8f5e9; border-color: #a5d6a7; color: #2e7d32; }

        /* チェックタグ */
        .check-tag {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--soft); border: 1.5px solid var(--border);
            border-radius: 20px; padding: 6px 14px; margin: 3px;
            cursor: pointer; font-size: 0.84rem; font-weight: 600; color: var(--text);
            transition: all .15s;
        }
        .check-tag input { display: none; }
        .check-tag.selected { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* セレクト */
        .form-select-styled {
            width: 100%; border: 1.5px solid #c8d9f0; border-radius: 8px;
            font-size: 0.93rem; padding: 10px 12px; color: var(--text);
            background-color: #fff; appearance: auto;
        }
        .form-select-styled:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }

        /* フォーム */
        .form-label  { font-weight: 700; font-size: 0.9rem; }
        .form-control { border: 1.5px solid #c8d9f0; border-radius: 8px; font-size: 0.95rem; }
        .form-control:focus {
            border-color: var(--primary); border-width: 2px;
            box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }
        .required-badge {
            font-size: 0.72rem; background: #dc3545; color: #fff;
            border-radius: 3px; padding: 1px 5px; margin-left: 6px; vertical-align: middle;
        }

        /* ボタン */
        .btn-primary-round {
            width: 100%; background: var(--primary); color: #fff; border: none;
            border-radius: 30px; font-size: 1.05rem; font-weight: 800;
            padding: 0.85rem; transition: background .15s;
        }
        .btn-primary-round:hover { background: var(--primary-dark); color: #fff; }

        /* ミスマッチ */
        .mismatch-box {
            background: #fff8f8; border: 1.5px solid #ffcdd2;
            border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;
        }
        .mismatch-box__icon { font-size: 2rem; color: #e53935; margin-bottom: 10px; }
        .mismatch-box__title { font-size: 1rem; font-weight: 800; color: #b71c1c; margin-bottom: 8px; }
        .mismatch-box__text  { font-size: 0.88rem; color: #555; line-height: 1.7; }

        /* 代替求人カード */
        .alt-card {
            background: #fff; border-radius: 10px; border: 1.5px solid var(--border);
            padding: 16px; margin-bottom: 10px; text-decoration: none;
            color: var(--text); display: block; transition: .2s;
        }
        .alt-card:hover {
            border-color: var(--primary);
            box-shadow: 0 3px 12px rgba(26,115,232,.1);
            color: var(--text); transform: translateY(-1px);
        }
        .alt-card__title { font-size: 0.92rem; font-weight: 800; margin-bottom: 6px; }
        .alt-card__tags  { display: flex; flex-wrap: wrap; gap: 5px; }
        .alt-card__tag   { font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 4px; }
        .alt-card__tag--area     { background: var(--soft); color: var(--primary); }
        .alt-card__tag--job-type { background: #e8f5e9; color: #2e7d32; }

        /* ステップインジケーター */
        .step-indicator {
            display: flex; align-items: center; justify-content: center;
            gap: 0; margin-bottom: 24px;
        }
        .step-indicator__item {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            font-size: 0.72rem; color: var(--muted); font-weight: 700;
        }
        .step-indicator__circle {
            width: 28px; height: 28px; border-radius: 50%;
            border: 2px solid #ddd; background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 800; color: #bbb;
        }
        .step-indicator__item.active .step-indicator__circle {
            background: var(--primary); border-color: var(--primary); color: #fff;
        }
        .step-indicator__item.done .step-indicator__circle {
            background: #e8f5e9; border-color: #66bb6a; color: #2e7d32;
        }
        .step-indicator__line { width: 40px; height: 2px; background: #ddd; margin-bottom: 16px; }
        .step-indicator__line.done { background: #66bb6a; }

        .site-footer {
            background: #1a1a2e; color: #aaa; text-align: center;
            padding: 24px 0; font-size: 0.82rem; margin-top: auto;
        }
        .site-footer a { color: #aaa; text-decoration: none; }
        .site-footer a:hover { color: #fff; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/"><img src="/images/logo.svg" alt="Care Entry" height="40"></a>
        <a href="{{ route('lp.show', $job->token) }}"
           class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:.82rem;">
            <i class="bi bi-arrow-left me-1"></i>求人に戻る
        </a>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-pencil-square me-2"></i>
            @if($step === 'confirm') 応募内容の確認
            @elseif($step === 'select') 希望条件の入力
            @elseif($step === 'form') 応募者情報の入力
            @else 近い求人のご案内
            @endif
        </h1>
        <p>{{ $job->seo_title }}</p>
    </div>
</div>

<main class="flex-grow-1" style="padding-bottom: 48px;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6">

    {{-- ステップインジケーター（mismatch以外） --}}
    @if($step !== 'mismatch')
    <div class="step-indicator">
        <div class="step-indicator__item {{ in_array($step, ['confirm','select']) ? 'active' : 'done' }}">
            <div class="step-indicator__circle">1</div>
            <span>条件確認</span>
        </div>
        <div class="step-indicator__line {{ in_array($step, ['form']) ? 'done' : '' }}"></div>
        <div class="step-indicator__item {{ $step === 'form' ? 'active' : '' }}">
            <div class="step-indicator__circle">2</div>
            <span>情報入力</span>
        </div>
        <div class="step-indicator__line"></div>
        <div class="step-indicator__item">
            <div class="step-indicator__circle">3</div>
            <span>応募完了</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px;font-size:.9rem;">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ==============================
         STEP: confirm（条件あり確認）
    ============================== --}}
    @if($step === 'confirm')

    <div class="card-section">
        <div class="section-title"><i class="bi bi-building"></i> この求人への応募</div>
        <div class="mb-2">
            @foreach($job->jobAreas->take(3) as $ja)
                @if($ja->area)
                    <span class="cond-badge"><i class="bi bi-geo-alt-fill me-1 text-primary"></i>{{ $ja->area->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobJobTypes->take(3) as $jt)
                @if($jt->jobType)
                    <span class="cond-badge">{{ $jt->jobType->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobEmploymentTypes->take(2) as $et)
                @if($et->employmentType)
                    <span class="cond-badge">{{ $et->employmentType->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobConditions->take(4) as $jc)
                @if($jc->condition)
                    <span class="cond-badge">{{ $jc->condition->name }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <div class="card-section">
        <div class="section-title"><i class="bi bi-person-check"></i> あなたの希望条件</div>
        <div class="mb-0">
            @foreach($conditionLabels['areas'] ?? [] as $name)
                <span class="cond-badge cond-badge--match"><i class="bi bi-geo-alt-fill me-1"></i>{{ $name }}</span>
            @endforeach
            @foreach($conditionLabels['jobTypes'] ?? [] as $name)
                <span class="cond-badge cond-badge--match">{{ $name }}</span>
            @endforeach
            @foreach($conditionLabels['employmentTypes'] ?? [] as $name)
                <span class="cond-badge cond-badge--match">{{ $name }}</span>
            @endforeach
            @foreach($conditionLabels['conditions'] ?? [] as $name)
                <span class="cond-badge cond-badge--match">{{ $name }}</span>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('lp.apply.judge', $job->token) }}">
        @csrf
        @foreach($conditions as $key => $ids)
            @foreach($ids as $id)
                <input type="hidden" name="{{ $key }}[]" value="{{ $id }}">
            @endforeach
        @endforeach
        <button type="submit" class="btn-primary-round">
            <i class="bi bi-arrow-right-circle me-2"></i>この内容で応募する
        </button>
    </form>

    {{-- ==============================
         STEP: select（条件なし・ミニフォーム）
    ============================== --}}
    @elseif($step === 'select')

    <div class="card-section">
        <div class="section-title"><i class="bi bi-building"></i> この求人の募集要項</div>
        <div class="mb-0">
            @foreach($job->jobAreas->take(3) as $ja)
                @if($ja->area)
                    <span class="cond-badge"><i class="bi bi-geo-alt-fill me-1 text-primary"></i>{{ $ja->area->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobJobTypes as $jt)
                @if($jt->jobType)
                    <span class="cond-badge">{{ $jt->jobType->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobEmploymentTypes as $et)
                @if($et->employmentType)
                    <span class="cond-badge">{{ $et->employmentType->name }}</span>
                @endif
            @endforeach
            @foreach($job->jobConditions as $jc)
                @if($jc->condition)
                    <span class="cond-badge">{{ $jc->condition->name }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('lp.apply.judge', $job->token) }}">
        @csrf
        @if(($via ?? null) === 'line')
            <input type="hidden" name="via" value="line">
        @endif

        <div class="card-section">
            <div class="section-title"><i class="bi bi-geo-alt-fill"></i> 希望エリア<span class="required-badge">必須</span></div>
            <select name="area_ids[]" class="form-select-styled" required>
                <option value="">エリアを選択してください</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}（{{ $area->region }}）</option>
                @endforeach
            </select>
        </div>

        <div class="card-section">
            <div class="section-title"><i class="bi bi-briefcase-fill"></i> 希望職種</div>
            <div>
                @foreach($jobTypes as $category => $types)
                    <p class="text-muted fw-bold mb-1 mt-2" style="font-size:0.78rem;">{{ $category }}</p>
                    @foreach($types as $type)
                        <label class="check-tag">
                            <input type="checkbox" name="job_type_ids[]" value="{{ $type->id }}">
                            {{ $type->name }}
                        </label>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="card-section">
            <div class="section-title"><i class="bi bi-person-badge"></i> 希望雇用形態</div>
            <div>
                @foreach($employmentTypes as $et)
                    <label class="check-tag">
                        <input type="checkbox" name="employment_type_ids[]" value="{{ $et->id }}">
                        {{ $et->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn-primary-round" @if(($via ?? null) === 'line') style="background:#06C755;" @endif>
            @if(($via ?? null) === 'line')
                <svg class="line-icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align:-3px;margin-right:6px;"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                LINEで応募に進む
            @else
                <i class="bi bi-arrow-right-circle me-2"></i>条件を確認して次へ
            @endif
        </button>
    </form>

    {{-- ==============================
         STEP: form（判定通過・入力）
    ============================== --}}
    @elseif($step === 'form')

    <div class="card-section" style="background:#f0f9f0;border-color:#a5d6a7;">
        <div style="display:flex;align-items:center;gap:8px;font-size:0.9rem;font-weight:700;color:#2e7d32;">
            <i class="bi bi-check-circle-fill"></i>この求人はご希望の条件と一致しています
        </div>
    </div>

    <form method="POST" action="{{ route('lp.apply.store', $job->token) }}" autocomplete="off">
        @csrf
        @foreach($conditions as $key => $ids)
            @foreach($ids as $id)
                <input type="hidden" name="{{ $key }}[]" value="{{ $id }}">
            @endforeach
        @endforeach

        <div class="card-section">
            <div class="section-title"><i class="bi bi-person-fill"></i> 応募者情報</div>

            <div class="mb-3">
                <label class="form-label">お名前<span class="required-badge">必須</span></label>
                <input type="text" name="applicant_name"
                       class="form-control @error('applicant_name') is-invalid @enderror"
                       value="{{ old('applicant_name') }}" placeholder="山田 太郎">
                @error('applicant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label class="form-label">電話番号<span class="required-badge">必須</span></label>
                <input type="tel" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" placeholder="09012345678">
                <div class="form-text small text-muted">ハイフンなしで入力してください</div>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn-primary-round">
            <i class="bi bi-send-fill me-2"></i>この内容で応募する
        </button>
    </form>

    {{-- ==============================
         STEP: mismatch（条件不一致）
    ============================== --}}
    @elseif($step === 'mismatch')

    <div class="mismatch-box">
        <div class="mismatch-box__icon"><i class="bi bi-exclamation-circle"></i></div>
        <p class="mismatch-box__title">ご希望の条件と異なる可能性があります</p>
        <p class="mismatch-box__text">
            この求人はご希望の条件と一致しない項目があるため、<br>
            この求人からの応募はご案内が難しい状況です。<br>
            ご希望に近い求人をご案内します。
        </p>
    </div>

    @if($alternatives->isNotEmpty())
        <p style="font-size:0.9rem;font-weight:800;color:#1a1a2e;margin-bottom:12px;">
            <i class="bi bi-search text-primary me-1"></i>ご希望に近い求人
        </p>
        @foreach($alternatives as $alt)
            <a href="{{ route('lp.show', $alt->token) }}" class="alt-card">
                <p class="alt-card__title">{{ $alt->seo_title ?: $alt->title }}</p>
                <div class="alt-card__tags">
                    @foreach($alt->jobAreas->take(2) as $ja)
                        @if($ja->area)
                            <span class="alt-card__tag alt-card__tag--area">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $ja->area->name }}
                            </span>
                        @endif
                    @endforeach
                    @foreach($alt->jobJobTypes->take(2) as $jt)
                        @if($jt->jobType)
                            <span class="alt-card__tag alt-card__tag--job-type">{{ $jt->jobType->name }}</span>
                        @endif
                    @endforeach
                </div>
            </a>
        @endforeach
    @endif

    <div class="text-center mt-3">
        <a href="{{ route('seo.jobs.okinawa') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:var(--primary);color:#fff;border-radius:30px;padding:10px 24px;font-size:0.9rem;font-weight:800;text-decoration:none;">
            <i class="bi bi-search"></i>他の求人を探す
        </a>
    </div>

    @endif

    <div class="text-center mt-3">
        <a href="{{ route('lp.show', $job->token) }}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>求人詳細に戻る
        </a>
    </div>

    @if($step !== 'mismatch')
    <p class="text-center mt-2" style="font-size:0.78rem;color:#aaa;">
        <i class="bi bi-shield-check me-1"></i>入力いただいた個人情報は求人への応募目的にのみ使用します。
    </p>
    @endif

</div>
</div>
</div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="mb-2">
            <strong style="color:#fff;">Care Entry</strong>
            <span class="ms-2">介護・福祉専門の求人サービス【対応エリア:沖縄県】</span>
        </div>
        <div class="mb-3 d-flex justify-content-center gap-3 flex-wrap" style="font-size:0.8rem;">
            <a href="/company">運営者情報</a>
            <a href="/privacy-policy">プライバシーポリシー</a>
            <a href="/terms">利用規約</a>
        </div>
        <p class="mb-0">&copy; {{ date('Y') }} Care Entry. All rights reserved.</p>
    </div>
</footer>

<script>
document.querySelectorAll('.check-tag').forEach(label => {
    label.addEventListener('click', () => {
        const cb = label.querySelector('input');
        setTimeout(() => label.classList.toggle('selected', cb.checked), 0);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
