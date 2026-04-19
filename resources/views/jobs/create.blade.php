@extends('layouts.app')

@section('title', '求人登録')

@if(config('services.recaptcha.site_key'))
@push('head')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endpush
@endif

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="bi bi-building me-2"></i>求人登録</h1>
        <p>フォームに入力するだけで求人を無料掲載。AIがタイトルと本文を自動生成します。</p>
    </div>
</div>

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8">

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form id="jobCreateForm" method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="recaptcha_token" id="recaptchaToken">

{{-- 会社名 --}}
<div class="form-section">
    <h5>会社名 <span class="text-danger">*</span></h5>
    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
           value="{{ old('company_name') }}" placeholder="株式会社〇〇" required>
    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- タイトル --}}
<div class="form-section">
    <h5>求人タイトル <small class="text-muted fw-normal">（任意・60文字以内）</small></h5>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title') }}" placeholder="例：那覇市｜未経験OK！介護求人（ホームヘルパー）" maxlength="60">
    <div class="form-text d-flex align-items-center gap-1 text-info">
        <i class="bi bi-stars"></i> 未入力の場合、AIが自動でタイトルを生成します
    </div>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- エリア --}}
<div class="form-section">
    <h5>勤務エリア（沖縄） <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    <ul class="nav nav-tabs mb-3">
        @foreach($areas as $region => $areaList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#area-tab-{{ $loop->index }}">{{ $region }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($areas as $region => $areaList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="area-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($areaList as $area)
                <div class="col-md-3 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="areas[]"
                               id="area_{{ $area->id }}" value="{{ $area->id }}"
                               {{ in_array($area->id, old('areas', [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="area_{{ $area->id }}">{{ $area->name }}</label>
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
<div class="form-section">
    <h5>職種 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    <ul class="nav nav-tabs mb-3">
        @foreach($jobTypes as $category => $typeList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#jt-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($jobTypes as $category => $typeList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="jt-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($typeList as $type)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="job_types[]"
                               id="jt_{{ $type->id }}" value="{{ $type->id }}"
                               {{ in_array($type->id, old('job_types', [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="jt_{{ $type->id }}">{{ $type->name }}</label>
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
<div class="form-section">
    <h5>雇用形態 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    <div class="d-flex flex-wrap gap-2">
        @foreach($employmentTypes as $et)
        <div>
            <input class="btn-check" type="checkbox" name="employment_types[]"
                   id="et_{{ $et->id }}" value="{{ $et->id }}"
                   {{ in_array($et->id, old('employment_types', [])) ? 'checked' : '' }}>
            <label class="btn btn-outline-primary" for="et_{{ $et->id }}">{{ $et->name }}</label>
        </div>
        @endforeach
    </div>
    @error('employment_types') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
</div>

{{-- 勤務条件 --}}
<div class="form-section">
    <h5>勤務条件 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    <ul class="nav nav-tabs mb-3">
        @foreach($conditions as $category => $condList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#cond-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($conditions as $category => $condList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="cond-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($condList as $cond)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="conditions[]"
                               id="cond_{{ $cond->id }}" value="{{ $cond->id }}"
                               {{ in_array($cond->id, old('conditions', [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="cond_{{ $cond->id }}">{{ $cond->name }}</label>
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
<div class="form-section">
    <h5>アピールポイント <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    <ul class="nav nav-tabs mb-3">
        @foreach($appeals as $category => $appealList)
        <li class="nav-item">
            <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}"
                    data-bs-toggle="tab" data-bs-target="#appeal-tab-{{ $loop->index }}">{{ $category }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($appeals as $category => $appealList)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="appeal-tab-{{ $loop->index }}">
            <div class="row check-group">
                @foreach($appealList as $appeal)
                <div class="col-md-4 col-6 mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="appeals[]"
                               id="appeal_{{ $appeal->id }}" value="{{ $appeal->id }}"
                               {{ in_array($appeal->id, old('appeals', [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="appeal_{{ $appeal->id }}">{{ $appeal->name }}</label>
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
<div class="form-section">
    <h5>連絡先情報 <span class="text-danger">*</span></h5>
    <div class="mb-3">
        <label class="form-label">メールアドレス</label>
        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
               value="{{ old('contact_email') }}" placeholder="example@company.com" required>
        @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-0">
        <label class="form-label">電話番号</label>
        <input type="tel" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
               value="{{ old('contact_phone') }}" placeholder="09012345678" required>
        <div class="form-text">ハイフンなしで入力してください（例：09012345678）</div>
        @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

{{-- 自由記述 --}}
<div class="form-section">
    <h5>求人本文 <small class="text-muted fw-normal">（任意・2000文字以内）</small></h5>
    <textarea name="free_text" class="form-control @error('free_text') is-invalid @enderror"
              rows="6" placeholder="仕事内容・職場環境・求める人物像など、自由にご記入ください。&#10;&#10;例：&#10;〇〇市にある小規模デイサービスです。スタッフ同士の仲が良く、&#10;未経験の方でも安心して働ける環境が整っています。">{{ old('free_text') }}</textarea>
    <div class="form-text d-flex align-items-center gap-1 text-info">
        <i class="bi bi-stars"></i> 未入力の場合、AIが自動で求人本文を生成します
    </div>
    @error('free_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- 写真 --}}
<div class="form-section">
    <h5>写真 <small class="text-muted fw-normal">（任意・1枚・5MB以内）</small></h5>
    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- 同意 --}}
<div class="form-section">
    <h5>応募時課金への同意 <span class="text-danger">*</span></h5>
    <div class="agreement-box mb-3">
        <p class="mb-0 small">{{ $agreementText }}</p>
        <p class="mb-0 text-muted small mt-1">（同意文言バージョン：{{ $agreementVersion }}）</p>
    </div>
    <div class="form-check">
        <input class="form-check-input @error('agreement_flag') is-invalid @enderror"
               type="checkbox" name="agreement_flag" id="agreement_flag" value="1"
               {{ old('agreement_flag') ? 'checked' : '' }} required>
        <label class="form-check-label fw-bold" for="agreement_flag">上記の内容に同意します</label>
        @error('agreement_flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary btn-lg px-5">求人を登録する</button>
</div>

</form>
</div>
</div>
</div>

<script>
@if(config('services.recaptcha.site_key'))
document.getElementById('jobCreateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'job_store'}).then(function(token) {
            document.getElementById('recaptchaToken').value = token;
            form.submit();
        });
    });
});
@endif
</script>

<script>
const STORAGE_KEY = 'job_create_form_v1';

function saveForm() {
    const form = document.getElementById('jobCreateForm');
    const data = {};
    new FormData(form).forEach((val, key) => {
        if (key === 'photo' || key === '_token') return;
        if (!data[key]) data[key] = [];
        data[key].push(val);
    });
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (e) {}
    updateTabBadges();
}

function restoreForm() {
    let saved;
    try {
        saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) return;
    } catch (e) { return; }

    let data;
    try { data = JSON.parse(saved); } catch (e) { return; }

    Object.entries(data).forEach(([key, values]) => {
        const name = key.replace('[]', '');
        document.querySelectorAll(`[name="${CSS.escape(key)}"], [name="${CSS.escape(name)}"]`).forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = values.includes(el.value);
            } else if (el.tagName === 'SELECT') {
                [...el.options].forEach(o => o.selected = values.includes(o.value));
            } else if (el.tagName === 'TEXTAREA' || el.type === 'text' || el.type === 'email' || el.type === 'tel') {
                el.value = values[0] ?? '';
            }
        });
    });
}

function updateTabBadges() {
    document.querySelectorAll('[data-bs-target]').forEach(btn => {
        const paneId = btn.getAttribute('data-bs-target');
        const pane = document.querySelector(paneId);
        if (!pane) return;
        const count = pane.querySelectorAll('input[type="checkbox"]:checked').length;
        let badge = btn.querySelector('.tab-count-badge');
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'tab-count-badge badge bg-primary ms-1';
                btn.appendChild(badge);
            }
            badge.textContent = count;
        } else if (badge) {
            badge.remove();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    restoreForm();
    updateTabBadges();
    const form = document.getElementById('jobCreateForm');
    form.addEventListener('change', saveForm);
    form.addEventListener('input', saveForm);
    form.addEventListener('submit', () => {
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
    });
});
</script>
@endsection
