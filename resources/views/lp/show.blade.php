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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->seo_title }}</title>
    <meta name="description" content="{{ $job->meta_description }}">
    <meta property="og:title" content="{{ $job->seo_title }}">
    <meta property="og:description" content="{{ $job->meta_description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/images/ogp.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ url('/images/ogp.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/lp.css">
    @php
        $empTypeMap = ['正社員' => 'FULL_TIME', 'パート' => 'PART_TIME', '契約社員' => 'CONTRACTOR', '派遣社員' => 'TEMPORARY'];
        $empTypesSchema = $job->jobEmploymentTypes->map(fn($e) => $empTypeMap[$e->employmentType->name] ?? 'OTHER')->values()->toArray();
        $areaName = $job->jobAreas->first()?->area?->name ?? '沖縄県';
        $salaryUnitMap = ['hourly' => 'HOUR', 'monthly' => 'MONTH'];
        $schema = [
            '@context'    => 'https://schema.org/',
            '@type'       => 'JobPosting',
            'title'       => $job->title,
            'description' => mb_substr(strip_tags($job->description_generated ?? $job->meta_description ?? ''), 0, 500),
            'datePosted'  => $job->created_at->toDateString(),
            'employmentType' => count($empTypesSchema) ? $empTypesSchema : ['OTHER'],
            'hiringOrganization' => ['@type' => 'Organization', 'name' => $job->company_name],
            'jobLocation' => ['@type' => 'Place', 'address' => [
                '@type' => 'PostalAddress',
                'addressRegion'   => '沖縄県',
                'addressLocality' => $areaName,
                'addressCountry'  => 'JP',
            ]],
        ];
        if ($job->expires_at) $schema['validThrough'] = $job->expires_at->toIso8601String();
        if ($job->salary_type && $job->salary_min) {
            $salaryValue = ['@type' => 'QuantitativeValue', 'minValue' => $job->salary_min, 'unitText' => $salaryUnitMap[$job->salary_type] ?? 'MONTH'];
            if ($job->salary_max) $salaryValue['maxValue'] = $job->salary_max;
            $schema['baseSalary'] = ['@type' => 'MonetaryAmount', 'currency' => 'JPY', 'value' => $salaryValue];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ナビ --}}
<nav class="site-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/" class="brand">
            <img src="/images/logo.svg" alt="Care Entry ケア・エントリー" height="52">
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
                <span class="info-label"><i class="bi bi-building me-1 text-primary"></i>会社名</span>
                <span class="info-value">{{ $job->company_name }}</span>
            </div>
            @if($job->salary_type && $job->salary_min)
            <div class="info-row">
                <span class="info-label"><i class="bi bi-cash-stack me-1 text-primary"></i>給与</span>
                <span class="info-value">
                    {{ $job->salaryText() }}
                    @if($job->salary_note)
                    <div class="text-muted" style="font-size:0.85rem;font-weight:400;margin-top:2px;">{{ $job->salary_note }}</div>
                    @endif
                </span>
            </div>
            @endif
            @php $areaNames = $job->jobAreas->map(fn($a) => $a->area->name)->filter()->implode('・'); @endphp
            @if($areaNames)
            <div class="info-row">
                <span class="info-label"><i class="bi bi-geo-alt me-1 text-primary"></i>勤務地</span>
                <span class="info-value">{{ $areaNames }}</span>
            </div>
            @endif
            @php $jobTypeNames = $job->jobJobTypes->map(fn($j) => $j->jobType->name)->filter()->implode('・'); @endphp
            @if($jobTypeNames)
            <div class="info-row">
                <span class="info-label"><i class="bi bi-person-badge me-1 text-primary"></i>職種</span>
                <span class="info-value">{{ $jobTypeNames }}</span>
            </div>
            @endif
            @php $empNames = $job->jobEmploymentTypes->map(fn($e) => $e->employmentType->name)->filter()->implode('・'); @endphp
            @if($empNames)
            <div class="info-row">
                <span class="info-label"><i class="bi bi-building me-1 text-primary"></i>雇用形態</span>
                <span class="info-value">{{ $empNames }}</span>
            </div>
            @endif
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

        @if($job->source !== 'hellowork')
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
        @endif

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
        @php $qs = request()->getQueryString() ? '?' . request()->getQueryString() : ''; @endphp
        @if($job->source === 'hellowork' && $job->hw_job_url)
            <a href="{{ $job->hw_job_url }}" target="_blank" rel="noopener"
               class="btn-line-apply" style="background:#e65100;">
                <i class="bi bi-box-arrow-up-right"></i>
                ハローワークで応募する
            </a>
            <p class="cta-note"><i class="bi bi-info-circle me-1"></i>ハローワークインターネットサービスのページに移動します</p>
        @else
            <a href="{{ route('line.entry', $job->token) }}{{ $qs }}" class="btn-line-apply">
                <svg class="line-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                LINEで応募する
            </a>
            <a href="{{ route('lp.apply', $job->token) }}{{ $qs }}" class="btn-form-apply">
                <i class="bi bi-pencil-square"></i>フォームで応募する
            </a>
            <p class="cta-note"><i class="bi bi-lock-fill me-1"></i>個人情報は安全に管理されます</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
