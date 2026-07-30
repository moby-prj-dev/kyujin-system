<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, follow">
<title>この求人は募集を終了しました｜Care Entry</title>
<meta name="description" content="{{ $job->company_name }}の求人は募集を終了しました。同じエリア・職種の別の求人はこちら。">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    :root {
        --color-primary: #1a73e8;
        --color-primary-dark: #0d47a1;
        --color-muted: #6c757d;
    }
    body {
        font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
        color: #2d2d2d; margin: 0; background: #f5f7fa;
    }
    .nav {
        background: #fff; border-bottom: 1px solid #dee2e6;
        padding: 12px 0; position: sticky; top: 0; z-index: 100;
    }
    .nav__logo img { height: 40px; }
    .expired-hero {
        background: #fff; border-radius: 14px;
        border: 1.5px solid #dee2e6;
        padding: 32px 24px; text-align: center;
        margin: 32px 0 24px;
    }
    .expired-hero__icon {
        font-size: 3rem; color: #f57c00; margin-bottom: 12px;
    }
    .expired-hero__title {
        font-size: 1.4rem; font-weight: 900;
        color: #0d1a2e; margin-bottom: 10px;
    }
    .expired-hero__company {
        font-size: 0.95rem; color: var(--color-muted); margin-bottom: 8px;
    }
    .expired-hero__note {
        font-size: 0.9rem; color: var(--color-muted); line-height: 1.7;
    }
    .similar-title {
        font-size: 1.1rem; font-weight: 800;
        margin: 32px 0 16px; color: #0d1a2e;
        border-left: 4px solid var(--color-primary);
        padding-left: 12px;
    }
    .job-card {
        background: #fff; border-radius: 10px;
        border: 1.5px solid #dee2e6; padding: 16px;
        margin-bottom: 12px; text-decoration: none;
        color: #2d2d2d; display: block; transition: .2s;
    }
    .job-card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 2px 10px rgba(26,115,232,.08);
        color: #2d2d2d;
    }
    .job-card__company {
        font-size: 0.82rem; color: var(--color-muted); margin-bottom: 4px;
    }
    .job-card__title {
        font-size: 1rem; font-weight: 800; margin-bottom: 8px;
        color: #0d1a2e;
    }
    .job-card__tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 6px; }
    .job-card__tag {
        font-size: 0.75rem; padding: 3px 8px; border-radius: 4px;
    }
    .job-card__tag--area {
        background: #fff3e0; color: #e65100;
    }
    .job-card__tag--job-type {
        background: #e8f5e9; color: #2e7d32;
    }
    .cta-box {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        color: #fff; border-radius: 14px; padding: 24px 20px;
        margin-top: 32px; text-align: center;
    }
    .cta-box__title { font-size: 1rem; font-weight: 800; margin-bottom: 12px; }
    .btn-cta {
        display: inline-flex; align-items: center; gap: 7px;
        background: #fff; color: var(--color-primary); border-radius: 30px;
        font-size: 0.95rem; font-weight: 800; padding: 11px 24px;
        text-decoration: none; transition: .2s;
    }
    .btn-cta:hover { background: #f0f6ff; color: var(--color-primary); }
</style>
</head>
<body>

<nav class="nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="nav__logo">
            <img src="/images/logo.svg" alt="Care Entry ケアエントリー">
        </a>
    </div>
</nav>

<div class="container" style="max-width: 800px;">

    <div class="expired-hero">
        <div class="expired-hero__icon">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <h1 class="expired-hero__title">この求人は募集を終了しました</h1>
        <p class="expired-hero__company">{{ $job->company_name }}</p>
        <p class="expired-hero__note">
            ご覧いただきありがとうございます。<br>
            こちらの求人は募集を終了しております。<br>
            以下に類似の求人をご紹介いたしますので、ぜひご覧ください。
        </p>
    </div>

    @if($similarJobs->isNotEmpty())
        <h2 class="similar-title">
            <i class="bi bi-search me-1"></i>類似の求人({{ $similarJobs->count() }}件)
        </h2>

        @foreach($similarJobs as $s)
            <a href="{{ route('lp.show', $s->token) }}" class="job-card">
                <p class="job-card__company">{{ $s->company_name }}</p>
                <p class="job-card__title">{{ $s->seo_title ?: $s->title }}</p>
                <div class="job-card__tags">
                    @foreach($s->jobAreas->take(2) as $ja)
                        @if($ja->area)
                            <span class="job-card__tag job-card__tag--area">
                                <i class="bi bi-geo-alt-fill"></i> {{ $ja->area->name }}
                            </span>
                        @endif
                    @endforeach
                    @foreach($s->jobJobTypes->take(2) as $jt)
                        @if($jt->jobType)
                            <span class="job-card__tag job-card__tag--job-type">{{ $jt->jobType->name }}</span>
                        @endif
                    @endforeach
                </div>
            </a>
        @endforeach
    @else
        <p class="text-muted text-center py-4">現在、類似の求人はございません。</p>
    @endif

    <div class="cta-box">
        <div class="cta-box__title">
            <i class="bi bi-search me-1"></i>沖縄の介護・福祉求人をもっと見る
        </div>
        <a href="{{ route('seo.jobs.okinawa') }}" class="btn-cta">
            <i class="bi bi-arrow-right-circle"></i>すべての求人を見る
        </a>
    </div>

</div>

</body>
</html>
