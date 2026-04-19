<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->seo_title }}</title>
    <meta name="description" content="{{ $job->meta_description }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .lp-header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: #fff; padding: 48px 0 36px; }
        .info-card { background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .info-card h5 { font-weight: 700; border-left: 4px solid #0d6efd; padding-left: 10px; margin-bottom: 16px; }
        .badge-condition { background: #e7f0ff; color: #0d6efd; border-radius: 20px; padding: 4px 12px; font-size: .85rem; display: inline-block; margin: 3px; }
        .badge-appeal { background: #e6f9f0; color: #198754; border-radius: 20px; padding: 4px 12px; font-size: .85rem; display: inline-block; margin: 3px; }
        .apply-section { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #dee2e6; padding: 16px 0; }
    </style>
</head>
<body>

<div class="lp-header">
    <div class="container">
        <p class="mb-1 opacity-75">{{ $job->jobAreas->map(fn($a) => $a->area->name)->implode('・') }} ／ {{ $job->jobJobTypes->map(fn($j) => $j->jobType->name)->implode('・') }}</p>
        <h1 class="h3 fw-bold">{{ $job->title }}</h1>
    </div>
</div>

<div class="container py-4" style="padding-bottom: 100px !important;">

    <div class="info-card">
        <h5>募集情報</h5>
        <table class="table table-borderless mb-0">
            <tr><th style="width:120px">エリア</th><td>{{ $job->jobAreas->map(fn($a) => $a->area->name)->implode('・') }}</td></tr>
            <tr><th>職種</th><td>{{ $job->jobJobTypes->map(fn($j) => $j->jobType->name)->implode('・') }}</td></tr>
            <tr><th>雇用形態</th><td>{{ $job->jobEmploymentTypes->map(fn($e) => $e->employmentType->name)->implode('・') }}</td></tr>
        </table>
    </div>

    @if($job->jobConditions->isNotEmpty())
    <div class="info-card">
        <h5>勤務条件</h5>
        @foreach($job->jobConditions as $jc)
            <span class="badge-condition">{{ $jc->condition->name }}</span>
        @endforeach
    </div>
    @endif

    @if($job->jobAppeals->isNotEmpty())
    <div class="info-card">
        <h5>アピールポイント</h5>
        @foreach($job->jobAppeals as $ja)
            <span class="badge-appeal">{{ $ja->appeal->name }}</span>
        @endforeach
    </div>
    @endif

    @if($job->photo_path)
    <div class="info-card">
        <img src="{{ asset('storage/' . $job->photo_path) }}" class="img-fluid rounded" alt="求人写真">
    </div>
    @endif

    @if($job->free_text)
    <div class="info-card">
        <h5>求人からのメッセージ</h5>
        <div>{!! nl2br(e($job->free_text)) !!}</div>
    </div>
    @endif

    @if($job->description_generated)
    <div class="info-card">
        <h5>仕事の詳細</h5>
        <div>{!! nl2br(e($job->description_generated)) !!}</div>
    </div>
    @endif

</div>

<div class="apply-section">
    <div class="container">
        <div class="row g-2">
            <div class="col-6">
                <a href="/liff/{{ $job->token }}" class="btn btn-success w-100 btn-lg">
                    LINEで応募する
                </a>
            </div>
            <div class="col-6">
                <a href="{{ url('/lp/' . $job->token . '/apply') }}" class="btn btn-primary w-100 btn-lg">
                    フォームで応募する
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
