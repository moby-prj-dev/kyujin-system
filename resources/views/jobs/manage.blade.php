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

@if(session('updated'))
<div class="alert alert-success">求人情報を更新しました。</div>
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
            <span class="text-muted small">無料掲載期限</span><br>
            @if($job->expires_at)
                @if($job->expires_at->isPast())
                    <span class="fw-bold text-danger">{{ $job->expires_at->format('Y/m/d') }}（期限切れ）</span>
                @elseif($job->expires_at->diffInDays(now()) <= 14)
                    <span class="fw-bold text-warning">{{ $job->expires_at->format('Y/m/d') }}（残り{{ (int)now()->diffInDays($job->expires_at) }}日）</span>
                @else
                    <span class="fw-bold">{{ $job->expires_at->format('Y/m/d') }}</span>
                @endif
            @else
                <span class="text-muted">—</span>
            @endif
        </div>
        <div>
            <span class="text-muted small">応募件数</span><br>
            <span class="fw-bold fs-5">{{ $applications->total() }} 件</span>
        </div>
    </div>
</div>

{{-- 応募者一覧 --}}
<div class="form-section mb-4">
    <h5>応募者一覧</h5>
    @if($applications->isEmpty())
        <p class="text-muted mb-0">まだ応募はありません。</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>応募日時</th>
                        <th>氏名</th>
                        <th>連絡先</th>
                        <th>種別</th>
                        <th>ステータス</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="text-nowrap">{{ $app->applied_at?->format('Y/m/d H:i') ?? '—' }}</td>
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
                        <td>
                            @if($app->status === 'received')
                                <span class="badge bg-warning text-dark">受付済</span>
                            @elseif($app->status === 'confirmed')
                                <span class="badge bg-success">対応済</span>
                            @else
                                <span class="badge bg-secondary">クローズ</span>
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
        @elseif($job->status === 'closed' || $job->status === 'paused')
        <form method="POST" action="{{ route('jobs.reopen', ['token' => $job->token]) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success"
                    onclick="return confirm('掲載を再開しますか？')">
                掲載再開
            </button>
        </form>
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

<form method="POST" action="{{ route('jobs.update', ['token' => $job->token]) }}" enctype="multipart/form-data">
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
@endsection
