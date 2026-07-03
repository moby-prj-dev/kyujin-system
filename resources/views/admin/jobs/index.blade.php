@extends('admin.layouts.app')
@section('title', '企業・求人一覧')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-building me-2"></i>企業・求人一覧</h1>
</div>

{{-- タブ --}}
<ul class="nav nav-tabs mb-3" id="adminJobTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jobs">
            <i class="bi bi-list-ul me-1"></i>求人一覧
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-companies">
            <i class="bi bi-building me-1"></i>企業一覧
        </button>
    </li>
</ul>

<div class="tab-content">

{{-- ===== 求人一覧タブ ===== --}}
<div class="tab-pane fade show active" id="tab-jobs">

    {{-- 絞り込みフォーム --}}
    <form method="GET" action="{{ route('admin.jobs.index') }}" class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">会社名・タイトル</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           value="{{ request('search') }}" placeholder="キーワード検索">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">公開ステータス</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">すべて</option>
                        <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>公開中</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>審査待ち</option>
                        <option value="paused"  {{ request('status') === 'paused'  ? 'selected' : '' }}>一時停止</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>期限切れ</option>
                        <option value="closed"  {{ request('status') === 'closed'  ? 'selected' : '' }}>終了</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">トライアル状態</label>
                    <select name="trial_status" class="form-select form-select-sm">
                        <option value="">すべて</option>
                        <option value="active"       {{ request('trial_status') === 'active'       ? 'selected' : '' }}>無料掲載中</option>
                        <option value="ending_soon"  {{ request('trial_status') === 'ending_soon'  ? 'selected' : '' }}>終了間近</option>
                        <option value="ended"        {{ request('trial_status') === 'ended'        ? 'selected' : '' }}>終了済み</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">継続状態</label>
                    <select name="continued" class="form-select form-select-sm">
                        <option value="">すべて</option>
                        <option value="0" {{ request('continued') === '0' ? 'selected' : '' }}>未継続</option>
                        <option value="1" {{ request('continued') === '1' ? 'selected' : '' }}>継続済み</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-search me-1"></i>絞り込み
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        リセット
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- 求人テーブル --}}
    <div class="card">
        <div class="card-header px-4 py-3 d-flex justify-content-between align-items-center">
            <span>求人一覧</span>
            <span class="text-muted fw-normal" style="font-size:0.82rem;">{{ $jobs->total() }}件</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">ID</th>
                        <th>会社名</th>
                        <th>求人タイトル</th>
                        <th style="width:160px;">メール</th>
                        <th class="text-center" style="width:70px;">ソース</th>
                        <th class="text-center" style="width:90px;">公開状態</th>
                        <th class="text-center" style="width:80px;">トライアル</th>
                        <th class="text-center" style="width:60px;">応募数</th>
                        <th class="text-center" style="width:85px;">登録日</th>
                        <th class="text-center" style="width:85px;">掲載終了</th>
                        <th class="text-center" style="width:200px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    @php
                        $statusMap = [
                            'active'  => ['公開中',   'bg-success'],
                            'pending' => ['審査待ち', 'bg-warning text-dark'],
                            'paused'  => ['一時停止', 'bg-warning text-dark'],
                            'expired' => ['期限切れ', 'bg-danger'],
                            'closed'  => ['終了',     'bg-secondary'],
                            'draft'   => ['下書き',   'bg-light text-dark'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$job->status] ?? ['不明', 'bg-secondary'];

                        $validSub      = (int) ($job->valid_count_sub ?? 0);
                        $expiresAt     = $job->expires_at;
                        $monitorEndsAt = $job->monitor_ends_at;
                        if (!$monitorEndsAt) {
                            $trialLabel = '—'; $trialClass = 'bg-secondary';
                        } elseif (now()->greaterThan($monitorEndsAt) || $validSub >= 3) {
                            $trialLabel = '終了済み'; $trialClass = 'bg-secondary';
                        } elseif ($monitorEndsAt->diffInDays(now(), false) >= -7) {
                            $trialLabel = '終了間近'; $trialClass = 'bg-warning text-dark';
                        } else {
                            $trialLabel = '無料期間内'; $trialClass = 'bg-success';
                        }
                    @endphp
                    <tr class="{{ $job->is_admin_hidden ? 'table-secondary opacity-75' : '' }}">
                        <td class="text-muted" style="font-size:0.8rem;">{{ $job->id }}</td>
                        <td class="fw-bold" style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                            title="{{ $job->company_name }}">
                            {{ $job->company_name }}
                            @if($job->is_permanently_free)
                            <span class="badge bg-danger ms-1" style="font-size:0.68rem;"><i class="bi bi-infinity"></i> 永久無料</span>
                            @elseif($job->is_monitor)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.68rem;">モニター</span>
                            @endif
                        </td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                            title="{{ $job->title }}">
                            <a href="{{ route('lp.show', $job->token) }}" target="_blank"
                               class="text-decoration-none text-dark fw-bold">
                                {{ $job->title ?: '（タイトル未設定）' }}
                            </a>
                            @if($job->admin_memo)
                            <i class="bi bi-sticky-fill text-warning ms-1"
                               style="cursor:pointer;" data-bs-toggle="collapse"
                               data-bs-target="#memo-{{ $job->id }}"></i>
                            @endif
                        </td>
                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8rem;"
                            title="{{ $job->contact_email }}">
                            {{ $job->contact_email ?: '—' }}
                        </td>
                        <td class="text-center">
                            @if($job->source === 'hellowork')
                                <span class="badge" style="background:#fff3e0;color:#e65100;font-size:0.72rem;">HW</span>
                            @else
                                <span class="badge bg-primary" style="font-size:0.72rem;">CE</span>
                                @if($job->isStandard())
                                    <br><span class="badge bg-info text-dark mt-1" style="font-size:0.66rem;"><i class="bi bi-star-fill"></i> Std</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $trialClass }}" style="font-size:0.75rem;">{{ $trialLabel }}</span>
                        </td>
                        <td class="text-center">
                            @php $total = (int)$job->applications_count; @endphp
                            <a href="{{ route('admin.applications.index', ['job_id' => $job->id]) }}"
                               class="fw-bold text-decoration-none {{ $total > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ $total }}
                            </a>
                            @if($total > 0)
                            <div style="font-size:0.72rem; line-height:1.5; white-space:nowrap;">
                                <span class="text-success">有効 {{ (int)$job->valid_count_sub }}</span>
                                @if((int)$job->invalid_count_sub > 0)
                                <span class="text-muted"> / 無効 {{ (int)$job->invalid_count_sub }}</span>
                                @endif
                                @if((int)$job->billable_count_sub > 0)
                                <br><span class="text-danger">課金 {{ (int)$job->billable_count_sub }}</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="text-center text-nowrap" style="font-size:0.82rem;">
                            {{ $job->created_at->format('Y/m/d') }}
                        </td>
                        <td class="text-center text-nowrap" style="font-size:0.82rem;">
                            @if($expiresAt)
                                @if($job->is_monitor)
                                <div style="font-size:0.7rem;" class="text-warning fw-bold">モニター期限</div>
                                @endif
                                <span class="{{ now()->greaterThan($expiresAt) ? 'text-danger fw-bold' : '' }}">
                                    {{ $expiresAt->format('Y/m/d') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                {{-- 公開/非公開切替 --}}
                                <form method="POST"
                                      action="{{ route('admin.jobs.toggle_hidden', $job) }}">
                                    @csrf @method('PATCH')
                                    @if($job->is_admin_hidden)
                                        <button type="submit" class="btn btn-xs btn-success"
                                                onclick="return confirm('この求人を再公開しますか？')">
                                            <i class="bi bi-eye me-1"></i>再公開
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-warning text-dark"
                                                onclick="return confirm('この求人を非公開にしますか？')">
                                            <i class="bi bi-eye-slash me-1"></i>非公開
                                        </button>
                                    @endif
                                </form>
                                {{-- モニター切替 --}}
                                <form method="POST"
                                      action="{{ route('admin.jobs.toggle_monitor', $job) }}">
                                    @csrf @method('PATCH')
                                    @if($job->is_monitor)
                                        <button type="submit" class="btn btn-xs btn-outline-secondary"
                                                onclick="return confirm('モニターを解除しますか？')">
                                            <i class="bi bi-star-fill text-warning me-1"></i>解除
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-warning"
                                                onclick="return confirm('無料モニターに設定しますか？（3ヶ月間無料）')">
                                            <i class="bi bi-star me-1"></i>モニター
                                        </button>
                                    @endif
                                </form>
                                {{-- 永久無料切替 --}}
                                <form method="POST"
                                      action="{{ route('admin.jobs.toggle_permanently_free', $job) }}">
                                    @csrf @method('PATCH')
                                    @if($job->is_permanently_free)
                                        <button type="submit" class="btn btn-xs btn-danger"
                                                onclick="return confirm('永久無料を解除しますか？')">
                                            <i class="bi bi-infinity me-1"></i>解除
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-danger"
                                                onclick="return confirm('永久無料に設定しますか？（この会社の全応募が無料になります）')">
                                            <i class="bi bi-infinity me-1"></i>永久無料
                                        </button>
                                    @endif
                                </form>
                                {{-- プラン切替 (basic <-> standard) --}}
                                @if($job->source === 'care_entry')
                                <form method="POST"
                                      action="{{ route('admin.jobs.toggle_plan', $job) }}">
                                    @csrf @method('PATCH')
                                    @if($job->isStandard())
                                        <button type="submit" class="btn btn-xs btn-primary"
                                                onclick="return confirm('スタンダードプランを解除してベーシックに戻しますか？(同じ連絡先の全求人が対象)')">
                                            <i class="bi bi-star-fill me-1"></i>Std
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-primary"
                                                onclick="return confirm('スタンダードプランに切替しますか?(同じ連絡先の全求人が対象・LINE応募と分析画面が有効になります)')">
                                            <i class="bi bi-star me-1"></i>Std
                                        </button>
                                    @endif
                                </form>
                                @endif
                                {{-- 編集 --}}
                                <a href="{{ route('jobs.manage', ['token' => $job->token]) }}"
                                   class="btn btn-xs btn-outline-primary" target="_blank">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                {{-- 削除 --}}
                                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}"
                                      onsubmit="return confirm('「{{ $job->title ?: $job->company_name }}」を完全に削除しますか？この操作は取り消せません。')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
{{-- メモ --}}
                                <button type="button" class="btn btn-xs btn-outline-warning"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#memo-{{ $job->id }}"
                                        title="メモ">
                                    <i class="bi bi-sticky{{ $job->admin_memo ? '-fill' : '' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    {{-- メモ展開行 --}}
                    <tr class="collapse {{ $job->admin_memo ? 'show' : '' }}" id="memo-{{ $job->id }}">
                        <td colspan="11" class="py-2 px-4 bg-light">
                            <form method="POST"
                                  action="{{ route('admin.jobs.memo', $job) }}"
                                  class="d-flex align-items-start gap-2">
                                @csrf @method('PATCH')
                                <textarea name="admin_memo" class="form-control form-control-sm"
                                          rows="2" maxlength="2000"
                                          placeholder="管理者メモ（企業・掲載状況などの内部メモ）">{{ $job->admin_memo }}</textarea>
                                <div class="d-flex flex-column gap-1">
                                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">保存</button>
                                    @if($job->admin_memo_updated_at)
                                    <span class="text-muted" style="font-size:0.72rem;white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($job->admin_memo_updated_at)->format('m/d H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-inbox me-1"></i>条件に一致する求人はありません
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
        <div class="card-footer py-3 d-flex justify-content-center">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>{{-- /tab-jobs --}}

{{-- ===== 企業一覧タブ ===== --}}
<div class="tab-pane fade" id="tab-companies">
    <div class="card">
        <div class="card-header px-4 py-3">
            企業一覧（メールアドレス単位）
            <span class="text-muted fw-normal ms-2" style="font-size:0.82rem;">{{ count($companies) }}社</span>
            <div class="text-muted fw-normal mt-1" style="font-size:0.75rem;">
                <i class="bi bi-info-circle me-1"></i>企業データは求人を削除しても残ります。完全削除したい場合は、求人数が 0 になった後に「削除」ボタンを押してください。
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>会社名</th>
                        <th>メール</th>
                        <th>掲載開始</th>
                        <th>トライアル状態</th>
                        <th>トライアル終了</th>
                        <th class="text-center">有効応募</th>
                        <th class="text-center">無効応募</th>
                        <th class="text-center">請求対象</th>
                        <th class="text-end">請求額</th>
                        <th class="text-center">求人数</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $c)
                    @php
                        $stMap = [
                            'permanently_free' => ['永久無料',         'bg-danger'],
                            'active'           => ['無料期間内',       'trial-active'],
                            'ending_soon'      => ['終了まで7日以内',  'trial-ending-soon'],
                            'ended'            => ['無料期間終了',     'trial-ended'],
                            'billing'          => ['請求対象あり',     'trial-billing'],
                        ];
                        [$stLabel, $stClass] = $stMap[$c->trial_status] ?? ['—', 'trial-active'];
                        $billingAmount = ($c->billable_count ?? 0) * 3000;
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $c->company_name }}</td>
                        <td class="text-muted" style="font-size:0.82rem;">{{ $c->contact_email }}</td>
                        <td class="text-nowrap" style="font-size:0.82rem;">
                            {{ $c->first_activated_at ? \Carbon\Carbon::parse($c->first_activated_at)->format('Y/m/d') : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $stClass }}" style="font-size:0.78rem; padding:4px 8px;">
                                {{ $stLabel }}
                            </span>
                        </td>
                        <td class="text-nowrap" style="font-size:0.82rem;">
                            {{ $c->trial_ends_at ? \Carbon\Carbon::parse($c->trial_ends_at)->format('Y/m/d') : '—' }}
                        </td>
                        <td class="text-center fw-bold text-success">{{ $c->valid_count }}</td>
                        <td class="text-center text-danger">{{ $c->invalid_count }}</td>
                        <td class="text-center fw-bold {{ $c->billable_count > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $c->billable_count }}
                        </td>
                        <td class="text-end fw-bold {{ $billingAmount > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $billingAmount > 0 ? '¥' . number_format($billingAmount) : '—' }}
                        </td>
                        <td class="text-center text-muted">{{ $c->listing_count }}</td>
                        <td class="text-center">
                            @if((int) $c->listing_count === 0)
                                <form method="POST" action="{{ route('admin.companies.destroy') }}" class="d-inline"
                                      onsubmit="return prompt('「{{ $c->company_name }}」の企業データを完全削除します。よろしければ「削除」と入力してください。') === '削除';">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="email" value="{{ $c->contact_email }}">
                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                        <i class="bi bi-trash"></i>削除
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small" title="求人を全て削除すると操作可能になります">
                                    <i class="bi bi-lock"></i>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">データがありません</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>{{-- /tab-companies --}}

</div>{{-- /tab-content --}}

@endsection
