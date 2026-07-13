@extends('layouts.app')

@section('title', '求人管理')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-gear me-2"></i>求人管理</h1>
        <p>このURLを保管してください。求人の編集・応募確認はここから行えます。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-9">

{{-- モニター期間中バナー --}}
@if(now()->lte($monitorCutoff))
<div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-2"
     style="background:#fff8e1;border:1.5px solid #f9a825;font-size:0.85rem;">
    <i class="bi bi-star-fill flex-shrink-0" style="color:#f9a825;"></i>
    <span>
        <strong style="color:#f57f17;">無料モニター期間中</strong>
        &nbsp;—&nbsp;{{ $monitorCutoff->format('Y年m月d日') }}まで掲載費・成果報酬が無料です
    </span>
</div>
@endif

@if(session('updated'))
<div class="alert alert-success">求人情報を更新しました。</div>
@endif

{{-- 継続完了メッセージ --}}
@if(session('continued'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
    <div><strong>掲載継続を受け付けました。</strong> 掲載は継続中です。</div>
</div>
@endif

{{-- 無料掲載終了間近バナー --}}
@if(!$trialEnded && $job->status === 'active' && $job->expires_at && !$job->continued_at)
    @php $daysLeft = (int)now()->diffInDays($job->expires_at, false); @endphp
    @if($daysLeft <= config('billing.continue_warning_days', 7) && $daysLeft >= 0)
    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 flex-shrink-0"></i>
        <div class="flex-grow-1">
            <strong>無料掲載期間がまもなく終了します（残り{{ $daysLeft }}日）</strong><br>
            <span style="font-size:0.93rem;">掲載を継続する場合は、下の継続ボタンを押してください。</span>
            <div class="mt-2">
                <form method="POST" action="{{ route('jobs.continue', ['token' => $job->token]) }}" id="continueForm">
                    @csrf
                    <button type="button" class="btn btn-warning btn-sm fw-bold" id="continueBtn">
                        <i class="bi bi-arrow-repeat me-1"></i>継続する
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
@endif
@if($job->continued_at)
<div class="alert alert-info d-flex align-items-center gap-2 mb-3" style="font-size:0.92rem;">
    <i class="bi bi-check2-circle fs-5 flex-shrink-0"></i>
    <div>掲載継続済み（{{ $job->continued_at->format('Y/m/d H:i') }}）</div>
</div>
@endif

{{-- 未払い・期限超過バナー --}}
@if($hasOverdue)
<div class="alert alert-danger d-flex align-items-start gap-2 mb-3">
    <i class="bi bi-exclamation-octagon-fill fs-5 mt-1 flex-shrink-0"></i>
    <div>
        <strong>お支払い期限を過ぎた未払い請求があります。</strong><br>
        <span style="font-size:0.93rem;">未払い解消まで新規掲載・再掲載はできません。下記「請求情報」をご確認のうえ、お振込みください。</span>
    </div>
</div>
@elseif($hasUnpaid)
<div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 flex-shrink-0"></i>
    <div>
        <strong>未払いの請求があります。</strong><br>
        <span style="font-size:0.93rem;">請求情報をご確認ください。</span>
    </div>
</div>
@endif

{{-- ステータス・公開URL --}}
<div class="form-section mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div>
            <span class="text-muted small">掲載ステータス</span><br>
            @if($job->status === 'active')
                <span class="badge bg-success fs-6">公開中</span>
            @elseif($job->status === 'paused')
                <span class="badge bg-warning text-dark fs-6">一時停止</span>
            @elseif($job->status === 'expired')
                <span class="badge bg-danger fs-6">期限切れ</span>
            @elseif($job->status === 'closed')
                <span class="badge bg-secondary fs-6">終了</span>
            @else
                <span class="badge bg-light text-dark fs-6">下書き</span>
            @endif
        </div>
        @if($job->status === 'active')
        <div class="flex-grow-1">
            <span class="text-muted small">公開中の求人URL</span><br>
            <div class="input-group input-group-sm" style="max-width:480px">
                <input type="text" class="form-control" id="lpUrl"
                       value="{{ url('/lp/' . $job->token) }}" readonly>
                <button class="btn btn-outline-secondary" type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('lpUrl').value).then(()=>this.textContent='コピー済み')">
                    コピー
                </button>
            </div>
        </div>
        @endif
        <div>
            <span class="text-muted small">掲載開始日</span><br>
            <span class="fw-bold">{{ $job->created_at->format('Y/m/d') }}</span>
        </div>
        <div>
            <span class="text-muted small">無料掲載</span><br>
            @if($trialEnded)
                <span class="fw-bold text-danger">終了（応募時課金）</span>
                <br><span class="text-muted" style="font-size:0.78rem;">※応募が発生した場合のみ課金されます</span>
            @elseif($job->expires_at)
                @php $daysLeft = (int)now()->diffInDays($job->expires_at); @endphp
                @if($job->expires_at->diffInDays(now()) <= 14)
                    <span class="fw-bold text-warning">{{ $job->expires_at->format('Y/m/d') }}まで（残り{{ $daysLeft }}日）</span>
                @else
                    <span class="fw-bold text-success">{{ $job->expires_at->format('Y/m/d') }}まで（残り{{ $daysLeft }}日）</span>
                @endif
            @else
                <span class="text-muted">—</span>
            @endif
        </div>
        <div>
            <span class="text-muted small d-flex align-items-center gap-1">
                応募件数
                <span tabindex="0" data-bs-toggle="tooltip"
                      title="有効応募数をもとに無料掲載・課金判定を行っています"
                      style="cursor:default; color:#adb5bd; font-size:0.8rem;">
                    <i class="bi bi-question-circle"></i>
                </span>
            </span>
            <div class="mt-1" style="line-height:1.9;">
                <div>
                    <span class="fw-bold fs-5">{{ $applications->total() }}</span>
                    <span class="text-muted" style="font-size:0.82rem;">件（総数）</span>
                </div>
                <div style="font-size:0.85rem;">
                    <span class="fw-bold text-success">
                        <i class="bi bi-check-circle-fill me-1" style="font-size:0.75rem;"></i>有効：{{ $jobValidCount }}件
                    </span>
                    @if($jobInvalidCount > 0)
                    　<span class="text-muted">
                        無効：{{ $jobInvalidCount }}件
                    </span>
                    @endif
                </div>
                <div style="font-size:0.82rem;">
                    @if($trialEnded)
                        <span class="{{ $jobBillableCount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            課金対象：{{ $jobBillableCount }}件
                        </span>
                    @else
                        @if($freeQuotaRemaining <= 1)
                            <span class="text-warning fw-bold">無料枠残り：{{ $freeQuotaRemaining }}件</span>
                        @else
                            <span class="text-success">無料枠内（残り{{ $freeQuotaRemaining }}件）</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 現在のプラン & プラン変更 --}}
<div class="form-section mb-4">
    <h5><i class="bi bi-award me-1"></i> ご契約プラン</h5>
    @if(session('plan_updated'))
    <div class="alert alert-success py-2 mb-3" style="font-size:0.9rem;">
        <i class="bi bi-check-circle-fill me-1"></i>{{ session('plan_updated') }}
    </div>
    @endif
    <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
        <div>
            <span class="text-muted small">現在のプラン</span><br>
            @if($job->isStandard())
                <span class="badge bg-primary fs-6"><i class="bi bi-star-fill me-1"></i>スタンダード</span>
                <span class="text-muted small ms-2">月額 3,000円 + 応募 3,000円/件(税別)</span>
            @else
                <span class="badge bg-secondary fs-6">ベーシック</span>
                <span class="text-muted small ms-2">月額 0円 + 応募 3,000円/件(税別)</span>
            @endif
        </div>
        @if($job->isStandard() && $job->nextBillingDate())
        <div>
            <span class="text-muted small">月額料金の初回請求日</span><br>
            <span class="fw-bold" style="color:#1a73e8;">
                <i class="bi bi-calendar-event me-1"></i>{{ $job->nextBillingDate()->format('Y年n月j日') }}
            </span>
            <span class="text-muted small ms-1" style="font-size:0.75rem;">
                (スタンダード開始: {{ $job->plan_started_at->format('Y/n/j') }})
            </span>
        </div>
        @endif
    </div>
    @if($job->isStandard() && $job->nextBillingDate())
    <div class="alert alert-info py-2 mb-3" style="font-size:0.85rem;">
        <i class="bi bi-info-circle me-1"></i>
        月額 3,000円 は<strong>{{ $job->nextBillingDate()->format('Y年n月j日') }}(翌月1日)から</strong>発生します。それまでは無料でスタンダード機能をご利用いただけます。
    </div>
    @endif

    @if($job->isPlanLocked())
    {{-- プラン変更ロック中 --}}
    <div class="alert alert-warning mb-3" style="font-size:0.9rem;">
        <i class="bi bi-lock-fill me-1"></i>
        <strong>プラン変更ロック中</strong>({{ $job->plan_locked_until->format('Y年n月j日') }}まで)<br>
        <span class="small">プラン変更後30日間は再変更いただけません。頻繁な切替による課金の混乱を防ぐための措置となります。</span>
    </div>
    @endif

    @if(!$job->isStandard())
    {{-- ベーシック → スタンダード アップグレード --}}
    <div class="p-3 rounded" style="background:#f0f7ff;border:1px solid #cfe0f5;">
        <div class="fw-bold mb-2" style="color:#1a73e8;"><i class="bi bi-arrow-up-circle me-1"></i>スタンダードプランにアップグレード</div>
        <p class="small text-muted mb-3">月額 3,000円で以下の機能が使えます:</p>
        <ul class="small mb-3" style="line-height:1.8;">
            <li>複数求人掲載(最大3件)</li>
            <li>求人一覧での<strong>優先上位表示</strong></li>
            <li><strong>LINE応募機能</strong>(他社にない独自機能・応募ハードルが下がります)</li>
            <li>応募通知の追加宛先(採用担当複数へ)</li>
            <li>応募データ分析画面</li>
        </ul>
        <p class="small text-muted mb-3">
            <i class="bi bi-info-circle me-1"></i>月3,000円は応募課金1件と同じ額です。応募が1件でも多く発生すれば元が取れる計算になります。<br>
            <i class="bi bi-info-circle me-1"></i>プラン変更後30日間は再変更できません(悪用防止のため)。
        </p>
        <form method="POST" action="{{ route('jobs.plan', ['token' => $job->token]) }}"
              onsubmit="return confirm('スタンダードプランに切替します。よろしいですか?\n\n・月額 3,000円が翌月1日から請求対象になります\n・プラン変更後30日間は再変更できません')">
            @csrf @method('PATCH')
            <input type="hidden" name="plan" value="standard">
            <button type="submit" class="btn btn-primary" @if($job->isPlanLocked()) disabled @endif>
                <i class="bi bi-star-fill me-1"></i>スタンダードプランに切替する
            </button>
        </form>
    </div>
    @else
    {{-- スタンダード → ベーシック ダウングレード --}}
    <div class="p-3 rounded" style="background:#fff5f5;border:1px solid #f5c6cb;">
        <div class="fw-bold mb-2" style="color:#8a3a3a;"><i class="bi bi-arrow-down-circle me-1"></i>ベーシックプランに変更</div>
        <p class="small text-muted mb-3">
            LINE応募機能・優先上位表示・分析画面などが使えなくなります。<br>
            複数求人掲載中の場合、2件目以降は自動的に掲載停止となります。<br>
            <i class="bi bi-info-circle me-1"></i>プラン変更後30日間は再変更できません(悪用防止のため)。
        </p>
        <form method="POST" action="{{ route('jobs.plan', ['token' => $job->token]) }}"
              onsubmit="return confirm('ベーシックプランに変更します。よろしいですか?\n\n・スタンダード限定機能が使えなくなります\n・プラン変更後30日間は再変更できません')">
            @csrf @method('PATCH')
            <input type="hidden" name="plan" value="basic">
            <button type="submit" class="btn btn-outline-secondary btn-sm" @if($job->isPlanLocked()) disabled @endif>
                <i class="bi bi-arrow-down me-1"></i>ベーシックに戻す
            </button>
        </form>
    </div>
    @endif
</div>

{{-- 応募データ分析(スタンダードプラン限定) --}}
@if($analytics)
<div class="form-section mb-4">
    <h5><i class="bi bi-bar-chart-line me-1"></i> 応募データ分析 <span class="badge bg-primary ms-1">スタンダード</span></h5>
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded" style="background:#f0f7ff;border:1px solid #cfe0f5;">
                <div class="text-muted small">直近6ヶ月 応募数</div>
                <div class="fs-4 fw-bold">{{ $analytics['total'] }}<span class="small text-muted ms-1">件</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded" style="background:#e6f4ea;border:1px solid #b7dfc1;">
                <div class="text-muted small">有効応募</div>
                <div class="fs-4 fw-bold" style="color:#137333;">{{ $analytics['valid_total'] }}<span class="small text-muted ms-1">件</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded" style="background:#fef7e0;border:1px solid #f6d97a;">
                <div class="text-muted small">LINE応募</div>
                <div class="fs-4 fw-bold" style="color:#8a6d00;">{{ $analytics['line_count'] }}<span class="small text-muted ms-1">件</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded" style="background:#f3e8fd;border:1px solid #d5b3f5;">
                <div class="text-muted small">フォーム応募</div>
                <div class="fs-4 fw-bold" style="color:#5b2d95;">{{ $analytics['web_count'] }}<span class="small text-muted ms-1">件</span></div>
            </div>
        </div>
    </div>
    <div style="height:220px;">
        <canvas id="applicationsChart"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    var ctx = document.getElementById('applicationsChart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($analytics['labels']) !!},
            datasets: [
                {
                    label: '全応募',
                    data: {!! json_encode($analytics['counts']) !!},
                    backgroundColor: '#a3c3ec',
                    borderColor: '#1a73e8',
                    borderWidth: 1,
                },
                {
                    label: '有効応募',
                    data: {!! json_encode($analytics['valid_counts']) !!},
                    backgroundColor: '#8ecfa2',
                    borderColor: '#137333',
                    borderWidth: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
})();
</script>
@endif

{{-- 応募者一覧 --}}
<div class="form-section mb-4 p-0 overflow-hidden">
    <button class="btn w-100 text-start px-4 py-3 d-flex justify-content-between align-items-center"
            type="button" data-bs-toggle="collapse" data-bs-target="#applicantList"
            aria-expanded="false" aria-controls="applicantList"
            style="background:#f8fafc; border:none; border-radius:0;">
        <span class="fw-bold" style="font-size:1rem;">
            <i class="bi bi-people me-2"></i>応募者一覧
            <span class="text-muted fw-normal ms-1" style="font-size:0.88rem;">
                （有効 {{ $jobValidCount }}件
                @if($jobInvalidCount > 0)
                    / 無効 {{ $jobInvalidCount }}件
                @endif
                / 計 {{ $applications->total() }}件）
            </span>
        </span>
        <i class="bi bi-chevron-down" style="transition:transform .2s;" id="applicantChevron"></i>
    </button>
    <div class="collapse" id="applicantList">
        <div class="px-4 pb-4 pt-2">
            @if($applications->isEmpty())
                <p class="text-muted mb-0">まだ応募はありません。</p>
            @else
                @php
                $invalidLabels = \App\Models\Application::INVALID_REASON_LABELS;
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>応募日時</th>
                                <th>応募先求人</th>
                                <th>氏名</th>
                                <th>連絡先</th>
                                <th>種別</th>
                                <th class="text-center">有効性</th>
                                <th class="text-center">課金</th>
                                <th class="text-center">申告</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                            <tr class="{{ $app->is_valid ? '' : 'table-secondary' }}"
                                style="{{ $app->is_valid ? '' : 'opacity:0.65;' }}">
                                <td class="text-nowrap">{{ $app->applied_at?->format('Y/m/d H:i') ?? '—' }}</td>
                                <td style="max-width:180px;">
                                    <div class="fw-bold text-truncate" title="{{ $app->job->title ?? '' }}">
                                        {{ $app->job->title ?: '（タイトル未設定）' }}
                                    </div>
                                    <div class="text-muted small text-truncate" title="{{ $app->job->company_name ?? '' }}">
                                        {{ $app->job->company_name ?? '—' }}
                                    </div>
                                </td>
                                <td>{{ $app->applicant_name ?? '—' }}</td>
                                <td>
                                    @if($app->phone)<div>{{ $app->phone }}</div>@endif
                                    @if($app->email)<div class="text-muted small">{{ $app->email }}</div>@endif
                                </td>
                                <td>
                                    @if($app->application_type === 'line')
                                        <span class="badge bg-success">LINE</span>
                                    @else
                                        <span class="badge bg-primary">フォーム</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($app->is_valid)
                                        <span class="badge" style="background:#e6f4ea;color:#137333;">有効</span>
                                    @else
                                        <span class="badge" style="background:#fce8e6;color:#c62828;">無効</span>
                                        @if($app->invalid_reason)
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ $invalidLabels[$app->invalid_reason] ?? $app->invalid_reason }}
                                        </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($app->is_billable)
                                        <span class="badge bg-danger">課金対象</span>
                                    @elseif($app->is_valid)
                                        <span class="text-success small">無料枠内</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($app->is_valid && !$app->disputes()->where('status', '!=', 'rejected')->exists())
                                        <a href="{{ route('disputes.create', [$job->token, $app]) }}"
                                           class="btn btn-outline-danger btn-sm" style="font-size:0.75rem;">
                                            <i class="bi bi-flag me-1"></i>無効申告
                                        </a>
                                    @elseif($app->disputes()->where('status', 'pending')->exists())
                                        <span class="text-warning small"><i class="bi bi-clock me-1"></i>審査中</span>
                                    @elseif($app->disputes()->where('status', 'approved')->exists())
                                        <span class="text-muted small">申告承認済</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<script>
document.getElementById('applicantList')?.addEventListener('show.bs.collapse', function() {
    document.getElementById('applicantChevron').style.transform = 'rotate(180deg)';
});
document.getElementById('applicantList')?.addEventListener('hide.bs.collapse', function() {
    document.getElementById('applicantChevron').style.transform = 'rotate(0deg)';
});
</script>

{{-- 請求情報 --}}
@if($billingSummaries->isNotEmpty())
<div class="form-section mb-4">
    <h5>請求情報</h5>
    @php
        $statusLabels = ['unbilled' => '未払い', 'sent' => '未払い', 'paid' => '支払済', 'on_hold' => '保留'];
        $statusBadges = ['unbilled' => 'bg-danger', 'sent' => 'bg-warning text-dark', 'paid' => 'bg-success', 'on_hold' => 'bg-secondary'];
    @endphp
    <div class="accordion" id="billingAccordion">
        @foreach($billingSummaries as $i => $bs)
        <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} py-3"
                        type="button" data-bs-toggle="collapse"
                        data-bs-target="#billing{{ $i }}">
                    <span class="me-3 fw-bold">{{ $bs->billing_month }}</span>
                    <span class="me-3 text-muted small">有効応募：{{ $bs->valid_count }}件</span>
                    <span class="me-3 fw-bold {{ $bs->total_amount > 0 ? 'text-danger' : '' }}">
                        ¥{{ number_format($bs->total_amount) }}
                    </span>
                    <span class="badge {{ $statusBadges[$bs->status] ?? 'bg-secondary' }}">
                        {{ $statusLabels[$bs->status] ?? $bs->status }}
                    </span>
                </button>
            </h2>
            <div id="billing{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                 data-bs-parent="#billingAccordion">
                <div class="accordion-body pt-2 pb-3" style="font-size:0.9rem;">
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">請求月</div>
                            <div class="fw-bold">{{ $bs->billing_month }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">有効応募件数</div>
                            <div class="fw-bold">{{ $bs->valid_count }} 件</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">請求金額（税別）</div>
                            <div class="fw-bold {{ $bs->total_amount > 0 ? 'text-danger' : '' }}">
                                ¥{{ number_format($bs->total_amount) }}
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">ステータス</div>
                            <span class="badge {{ $statusBadges[$bs->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$bs->status] ?? $bs->status }}
                            </span>
                        </div>
                    </div>
                    {{-- 応募明細 --}}
                    @php
                        $contactEmail = $job->contact_email;
                        $billingApps = \App\Models\Application::whereHas('job', fn($q) => $q->where('contact_email', $contactEmail))
                            ->whereMonth('applied_at', \Carbon\Carbon::parse($bs->billing_month . '-01')->month)
                            ->whereYear('applied_at', \Carbon\Carbon::parse($bs->billing_month . '-01')->year)
                            ->orderByDesc('applied_at')
                            ->get();
                    @endphp
                    @if($billingApps->isNotEmpty())
                    <div class="text-muted small mb-1">応募明細</div>
                    <table class="table table-sm mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>応募日時</th>
                                <th>応募者名</th>
                                <th class="text-center">有効/無効</th>
                                <th class="text-center">課金対象</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($billingApps as $app)
                            <tr>
                                <td>{{ $app->applied_at?->format('Y/m/d H:i') ?? '—' }}</td>
                                <td>{{ $app->applicant_name }}</td>
                                <td class="text-center">
                                    @if($app->is_valid)
                                        <span class="badge" style="background:#e6f4ea;color:#137333;">有効</span>
                                    @else
                                        <span class="badge" style="background:#fce8e6;color:#c62828;">無効</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($app->is_billable)
                                        <span class="badge bg-danger">対象</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <p class="text-muted small mt-2 mb-0">※応募が発生した分のみ課金されます</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <p class="text-muted small mt-2 mb-0">
        ご不明な点は <a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a> までお問い合わせください。
    </p>
</div>
@endif

{{-- 掲載操作ボタン --}}
<div class="form-section mb-4">
    <h5>掲載操作</h5>
    <div class="d-flex gap-3 flex-wrap align-items-center">
        @if($job->status === 'active')
        <form method="POST" action="{{ route('jobs.close', ['token' => $job->token]) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-warning"
                    onclick="return confirm('掲載を停止しますか？求職者からのLP表示ができなくなります。')">
                掲載停止
            </button>
        </form>
        @elseif(in_array($job->status, ['closed', 'paused', 'expired']))
        @if($hasOverdue)
            <button type="button" class="btn btn-success" disabled
                    title="未払い請求を解消してから再掲載できます">
                掲載再開
            </button>
        @else
        <form method="POST" action="{{ route('jobs.reopen', ['token' => $job->token]) }}" id="reopenForm">
            @csrf @method('PATCH')
            <button type="button" class="btn btn-success" id="reopenBtn">
                掲載再開
            </button>
        </form>
        @endif
        @endif

        <form method="POST" action="{{ route('jobs.destroy', ['token' => $job->token]) }}"
              class="ms-auto">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"
                    onclick="return confirm('この求人を完全削除しますか？\n\n削除後は復元できません。本当によろしいですか？')">
                完全削除
            </button>
        </form>
    </div>
</div>

{{-- 応募通知の追加宛先(スタンダードプラン限定) --}}
@if($job->isStandard())
<div class="form-section">
    <h5><i class="bi bi-envelope-plus me-1"></i> 応募通知の追加宛先 <span class="badge bg-primary ms-1">スタンダード</span></h5>
    <p class="text-muted small mb-3">
        応募通知を採用担当者複数名に届けたい場合は、追加のメールアドレスを登録できます(最大5件)。<br>
        主連絡先 <strong>{{ $job->contact_email }}</strong> にも引き続き通知が届きます。
    </p>
    <form method="POST" action="{{ route('jobs.notifications', ['token' => $job->token]) }}">
        @csrf
        @method('PATCH')
        @php $secondaryEmails = $job->secondary_emails ?? []; @endphp
        @for($i = 0; $i < 5; $i++)
            <div class="mb-2">
                <input type="email" name="secondary_emails[]" class="form-control"
                       value="{{ old('secondary_emails.' . $i, $secondaryEmails[$i] ?? '') }}"
                       placeholder="追加宛先 {{ $i + 1 }} (任意)" maxlength="200">
            </div>
        @endfor
        @error('secondary_emails.*') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-save me-1"></i>通知宛先を保存
        </button>
    </form>
</div>
@endif

{{-- 選考質問(スクリーナー) --}}
<div class="form-section">
    <h5><i class="bi bi-question-circle me-1"></i> 応募前の選考質問 <span class="badge bg-warning text-dark ms-1" style="font-size:0.68rem;">無駄応募対策</span></h5>
    <p class="text-muted small mb-3">
        応募者は求人応募前に以下の質問に必ず回答します。回答内容は応募通知メールに含まれるため、事前に応募者の適性を確認できます。<br>
        <strong>質問は最大3個まで</strong>設定できます。冷やかしや条件不一致の応募を減らす効果があります。
    </p>
    <form method="POST" action="{{ route('jobs.screener', ['token' => $job->token]) }}">
        @csrf
        @method('PATCH')
        @php $existingQuestions = $job->screener_questions ?? []; @endphp
        @php
            $placeholderExamples = [
                '例:介護福祉士の資格をお持ちですか?',
                '例:夜勤対応は可能ですか?',
                '例:希望入社時期を教えてください',
            ];
        @endphp
        <p class="small mb-2" style="color:#8a6d00;">
            <i class="bi bi-info-circle-fill me-1"></i><strong>回答形式</strong>を選べます:「はい/いいえ」形式 or 「自由記入」形式
        </p>
        @for($i = 0; $i < 3; $i++)
            @php
                $eq = $existingQuestions[$i]['q'] ?? '';
                $et = $existingQuestions[$i]['type'] ?? 'yesno';
            @endphp
            <div class="mb-3 p-2 rounded" style="background:#fff;border:1px solid #f6d97a;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark" style="min-width:32px;">Q{{ $i + 1 }}</span>
                    <input type="text" name="questions[{{ $i }}][q]" class="form-control form-control-sm"
                           value="{{ old('questions.' . $i . '.q', $eq) }}"
                           placeholder="{{ $placeholderExamples[$i] }} (任意)" maxlength="200">
                </div>
                <div class="d-flex align-items-center gap-2" style="padding-left:44px;">
                    <span class="small text-muted">回答形式:</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="questions[{{ $i }}][type]"
                               id="q{{ $i }}_yesno" value="yesno"
                               @if(old('questions.' . $i . '.type', $et) === 'yesno') checked @endif>
                        <label class="btn btn-outline-primary" for="q{{ $i }}_yesno" style="font-size:0.78rem;">
                            <i class="bi bi-check-circle me-1"></i>はい/いいえ
                        </label>

                        <input type="radio" class="btn-check" name="questions[{{ $i }}][type]"
                               id="q{{ $i }}_text" value="text"
                               @if(old('questions.' . $i . '.type', $et) === 'text') checked @endif>
                        <label class="btn btn-outline-primary" for="q{{ $i }}_text" style="font-size:0.78rem;">
                            <i class="bi bi-pencil me-1"></i>自由記入
                        </label>
                    </div>
                </div>
            </div>
        @endfor
        <div class="small text-muted mt-2 mb-2">
            <i class="bi bi-lightbulb me-1"></i>他の例:「未経験でも応募可能ですか?」「通勤手段を教えてください」「勤務可能な曜日は?」「希望勤務時間は?」など、事業所様が事前に確認したい項目を自由に設定できます。
        </div>
        <button type="submit" class="btn btn-warning btn-sm">
            <i class="bi bi-save me-1"></i>選考質問を保存
        </button>
    </form>
</div>

{{-- 求人編集フォーム --}}
<div class="form-section">
<h5>求人情報を編集</h5>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('jobs.update', ['token' => $job->token]) }}" enctype="multipart/form-data" autocomplete="off">
@csrf
@method('PUT')

{{-- 会社名 --}}
<div class="mb-3">
    <label class="form-label fw-bold">会社名 <span class="text-danger">*</span></label>
    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
           value="{{ old('company_name', $job->company_name) }}" required>
    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- タイトル --}}
<div class="mb-3">
    <label class="form-label fw-bold">求人タイトル <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $job->title) }}" required maxlength="60">
    <div class="form-text">LP上部に表示されるタイトルです（60文字以内）</div>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- エリア --}}
<div class="mb-3">
    <label class="form-label fw-bold">勤務エリア（沖縄） <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></label>
    <ul class="nav nav-tabs mb-2">
        @foreach($areas as $region => $areaList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#m-area-tab-{{ $loop->index }}">{{ $region }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($areas as $region => $areaList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="m-area-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($areaList as $area)
                <div class="col-md-3 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="areas[]"
                               id="m-area_{{ $area->id }}" value="{{ $area->id }}"
                               {{ in_array($area->id, old('areas', $selectedAreas)) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="m-area_{{ $area->id }}">{{ $area->name }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @error('areas') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- 職種 --}}
<div class="mb-3">
    <label class="form-label fw-bold">職種 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></label>
    <ul class="nav nav-tabs mb-2">
        @foreach($jobTypes as $category => $typeList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#m-jt-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($jobTypes as $category => $typeList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="m-jt-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($typeList as $type)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="job_types[]"
                               id="m-jt_{{ $type->id }}" value="{{ $type->id }}"
                               {{ in_array($type->id, old('job_types', $selectedJobTypes)) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="m-jt_{{ $type->id }}">{{ $type->name }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @error('job_types') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- 雇用形態 --}}
<div class="mb-3">
    <label class="form-label fw-bold">雇用形態 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></label>
    <div class="d-flex flex-wrap gap-2">
        @foreach($employmentTypes as $et)
        <div>
            <input class="btn-check" type="checkbox" name="employment_types[]"
                   id="m-et_{{ $et->id }}" value="{{ $et->id }}"
                   {{ in_array($et->id, old('employment_types', $selectedEmploymentTypes)) ? 'checked' : '' }}>
            <label class="btn btn-outline-primary" for="m-et_{{ $et->id }}">{{ $et->name }}</label>
        </div>
        @endforeach
    </div>
    @error('employment_types') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
</div>

{{-- 勤務条件 --}}
<div class="mb-3">
    <label class="form-label fw-bold">勤務条件 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></label>
    <ul class="nav nav-tabs mb-2">
        @foreach($conditions as $category => $condList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#m-cond-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($conditions as $category => $condList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="m-cond-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($condList as $cond)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="conditions[]"
                               id="m-cond_{{ $cond->id }}" value="{{ $cond->id }}"
                               {{ in_array($cond->id, old('conditions', $selectedConditions)) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="m-cond_{{ $cond->id }}">{{ $cond->name }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @error('conditions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- アピールポイント --}}
<div class="mb-3">
    <label class="form-label fw-bold">アピールポイント <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></label>
    <ul class="nav nav-tabs mb-2">
        @foreach($appeals as $category => $appealList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#m-appeal-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($appeals as $category => $appealList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="m-appeal-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($appealList as $appeal)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="appeals[]"
                               id="m-appeal_{{ $appeal->id }}" value="{{ $appeal->id }}"
                               {{ in_array($appeal->id, old('appeals', $selectedAppeals)) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="m-appeal_{{ $appeal->id }}">{{ $appeal->name }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @error('appeals') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- 連絡先 --}}
<div class="mb-3">
    <label class="form-label fw-bold">メールアドレス <i class="bi bi-lock-fill text-secondary ms-1" style="font-size:.8rem;"></i></label>
    <input type="email" name="contact_email" class="form-control bg-light text-muted"
           value="{{ $job->contact_email }}" readonly style="cursor:not-allowed;">
</div>
<div class="mb-3">
    <label class="form-label fw-bold">電話番号 <i class="bi bi-lock-fill text-secondary ms-1" style="font-size:.8rem;"></i></label>
    <input type="tel" name="contact_phone" class="form-control bg-light text-muted"
           value="{{ $job->contact_phone }}" readonly style="cursor:not-allowed;">
</div>

{{-- 給与 --}}
<div class="mb-3">
    <label class="form-label fw-bold">給与 <span class="text-danger">*</span></label>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label small">給与種別 <span class="text-danger">*</span></label>
            <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror" required>
                <option value="">選択してください</option>
                <option value="monthly" {{ old('salary_type', $job->salary_type) === 'monthly' ? 'selected' : '' }}>月給</option>
                <option value="hourly"  {{ old('salary_type', $job->salary_type) === 'hourly'  ? 'selected' : '' }}>時給</option>
                <option value="daily"   {{ old('salary_type', $job->salary_type) === 'daily'   ? 'selected' : '' }}>日給</option>
                <option value="yearly"  {{ old('salary_type', $job->salary_type) === 'yearly'  ? 'selected' : '' }}>年収</option>
                <option value="other"   {{ old('salary_type', $job->salary_type) === 'other'   ? 'selected' : '' }}>その他</option>
            </select>
            @error('salary_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small">最低給与額（円） <span class="text-danger">*</span></label>
            <input type="number" name="salary_min" class="form-control @error('salary_min') is-invalid @enderror"
                   value="{{ old('salary_min', $job->salary_min) }}" placeholder="例：200000" min="1" required>
            @error('salary_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small">最高給与額（円） <small class="text-muted">任意</small></label>
            <input type="number" name="salary_max" class="form-control @error('salary_max') is-invalid @enderror"
                   value="{{ old('salary_max', $job->salary_max) }}" placeholder="例：250000" min="1">
            @error('salary_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="mt-2">
        <label class="form-label small">給与補足 <small class="text-muted">任意・500文字以内</small></label>
        <textarea name="salary_note" class="form-control @error('salary_note') is-invalid @enderror"
                  rows="2" maxlength="500"
                  placeholder="例：夜勤手当あり、経験・資格により優遇、処遇改善加算含む">{{ old('salary_note', $job->salary_note) }}</textarea>
        @error('salary_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">自由記述 <small class="text-muted fw-normal">（任意）</small></label>
    <textarea name="free_text" class="form-control @error('free_text') is-invalid @enderror"
              rows="6" placeholder="求人に関する補足情報など、自由にご記入ください。">{{ old('free_text', $job->free_text ?? $job->description_generated) }}</textarea>
    @error('free_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label class="form-label fw-bold">写真 <small class="text-muted fw-normal">（任意・1枚・5MB以内）</small></label>
    @if($job->photo_path)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $job->photo_path) }}" class="img-thumbnail" style="max-height:200px">
        </div>
    @endif
    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="text-center">
    <button type="submit" class="btn btn-primary btn-lg px-5">求人を更新する</button>
</div>

</form>
</div>

</div>
</div>
</div>

{{-- 再開時トライアル終了確認モーダル --}}
<div class="modal fade" id="reopenTrialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>無料掲載期間の終了
                </h5>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-3">この求人は無料掲載期間が終了しています。</p>
                <p class="mb-3">掲載は可能ですが、<br>応募が発生した場合に課金が発生します。</p>
                <div class="alert alert-warning py-2 mb-2" style="font-size:0.93rem;">
                    <strong>・1応募：¥3,000（税別）</strong>
                </div>
                <p class="text-muted small mb-0">※応募がない場合は料金は発生しません</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="reopenConfirmBtn">掲載する</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

document.getElementById('continueBtn')?.addEventListener('click', function() {
    if (confirm('掲載を継続しますか？\n\n無料掲載期間終了後は、有効応募1件につき3,000円（税別）が発生します。\n応募がない場合は料金は発生しません。\n\nOKを押すと掲載継続として反映されます。')) {
        this.disabled = true;
        document.getElementById('continueForm').submit();
    }
});

@if(isset($job) && in_array($job->status, ['closed', 'paused', 'expired']))
const reopenTrialModal = new bootstrap.Modal(document.getElementById('reopenTrialModal'));

document.getElementById('reopenBtn')?.addEventListener('click', async function() {
    const email = '{{ $job->contact_email }}';
    try {
        const res = await fetch(`{{ route('jobs.check_trial') }}?email=${encodeURIComponent(email)}`);
        const data = await res.json();
        if (data.trial_ended) {
            reopenTrialModal.show();
            return;
        }
    } catch (err) {}
    if (confirm('掲載を再開しますか？')) {
        document.getElementById('reopenForm').submit();
    }
});

document.getElementById('reopenConfirmBtn')?.addEventListener('click', function() {
    reopenTrialModal.hide();
    document.getElementById('reopenForm').submit();
});
@endif
</script>
@endpush
