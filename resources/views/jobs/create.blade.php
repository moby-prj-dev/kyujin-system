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

@if(now()->lte($monitorCutoff))
<div style="position:sticky;top:78px;z-index:90;background:linear-gradient(90deg,#fff8e1,#fff3cd);border-top:3px solid #f9a825;border-bottom:3px solid #f9a825;padding:12px 0;text-align:center;">
    <span style="font-size:0.85rem;font-weight:800;color:#f57f17;">
        <i class="bi bi-star-fill me-1"></i>無料モニター募集中
        &nbsp;〜&nbsp;{{ $monitorCutoff->format('Y年m月d日') }}まで
        &nbsp;｜&nbsp;3か月間または有効応募3件まで成果報酬0円
    </span>
</div>
@endif

<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8">

@if(now()->lte($monitorCutoff))
<div style="background:#fff3cd;border:2px solid #f9a825;border-radius:10px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
    <i class="bi bi-star-fill" style="color:#f57f17;font-size:1.3rem;flex-shrink:0;"></i>
    <div>
        <div style="font-size:0.8rem;font-weight:800;color:#f57f17;margin-bottom:2px;">期間限定・無料モニター募集中</div>
        <div style="font-size:0.88rem;color:#1a1a2e;">
            <strong>{{ $monitorCutoff->format('Y年m月d日') }}までに登録</strong>された企業は、
            掲載開始から<strong>3か月間または有効応募3件</strong>まで成果報酬が無料です。
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form id="jobCreateForm" method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data" autocomplete="off">
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

{{-- 給与 --}}
<div class="form-section">
    <h5>給与 <span class="text-danger">*</span></h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-bold">給与種別 <span class="text-danger">*</span></label>
            <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror" required>
                <option value="">選択してください</option>
                <option value="monthly" {{ old('salary_type') === 'monthly' ? 'selected' : '' }}>月給</option>
                <option value="hourly"  {{ old('salary_type') === 'hourly'  ? 'selected' : '' }}>時給</option>
                <option value="daily"   {{ old('salary_type') === 'daily'   ? 'selected' : '' }}>日給</option>
                <option value="yearly"  {{ old('salary_type') === 'yearly'  ? 'selected' : '' }}>年収</option>
                <option value="other"   {{ old('salary_type') === 'other'   ? 'selected' : '' }}>その他</option>
            </select>
            @error('salary_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">最低給与額（円） <span class="text-danger">*</span></label>
            <input type="number" name="salary_min" class="form-control @error('salary_min') is-invalid @enderror"
                   value="{{ old('salary_min') }}" placeholder="例：200000" min="1" required>
            @error('salary_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">最高給与額（円） <small class="text-muted fw-normal">任意</small></label>
            <input type="number" name="salary_max" class="form-control @error('salary_max') is-invalid @enderror"
                   value="{{ old('salary_max') }}" placeholder="例：250000" min="1">
            @error('salary_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="mt-3">
        <label class="form-label small fw-bold">給与補足 <small class="text-muted fw-normal">任意・500文字以内</small></label>
        <textarea name="salary_note" class="form-control @error('salary_note') is-invalid @enderror"
                  rows="2" maxlength="500"
                  placeholder="例：夜勤手当あり、経験・資格により優遇、処遇改善加算含む">{{ old('salary_note') }}</textarea>
        @error('salary_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

{{-- 掲載プラン選択 --}}
<div class="form-section">
    <h5>掲載プラン <span class="text-danger">*</span></h5>
    <p class="text-muted small mb-3">どちらのプランで掲載するかお選びください。掲載開始後もいつでも変更可能です。</p>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="d-block h-100" style="cursor:pointer;">
                <input type="radio" name="plan" value="basic" class="btn-check"
                       {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }} required>
                <div class="p-3 border rounded h-100 plan-card" style="border-color:#dee2e6;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong style="font-size:1.05rem;">ベーシック</strong>
                        <span class="text-success fw-bold">¥0/月</span>
                    </div>
                    <ul class="small text-muted mb-0" style="padding-left:1.1rem;line-height:1.7;">
                        <li>掲載無料 + 有効応募 3,000円/件</li>
                        <li>1事業所につき1求人</li>
                        <li>Webフォーム応募</li>
                        <li>応募通知メール(1宛先)</li>
                    </ul>
                </div>
            </label>
        </div>
        <div class="col-md-6">
            <label class="d-block h-100" style="cursor:pointer;">
                <input type="radio" name="plan" value="standard" class="btn-check"
                       {{ old('plan') === 'standard' ? 'checked' : '' }} required>
                <div class="p-3 border rounded h-100 plan-card position-relative" style="border-color:#dee2e6;">
                    <span class="position-absolute" style="top:-10px;right:12px;background:#1a73e8;color:#fff;font-size:0.68rem;font-weight:800;padding:2px 8px;border-radius:3px;">おすすめ</span>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong style="font-size:1.05rem;">スタンダード</strong>
                        <span class="text-primary fw-bold">¥3,000/月</span>
                    </div>
                    <ul class="small text-muted mb-0" style="padding-left:1.1rem;line-height:1.7;">
                        <li>ベーシックの内容に加えて:</li>
                        <li>複数求人掲載(最大3件)</li>
                        <li>LINE応募機能・優先上位表示</li>
                        <li>応募通知の追加宛先・分析画面</li>
                    </ul>
                </div>
            </label>
        </div>
    </div>
    @error('plan') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
    <style>
        .btn-check:checked + .plan-card {
            border-color: #1a73e8 !important;
            background: #f0f7ff;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
        }
        .plan-card { transition: all .15s; }
        .plan-card:hover { border-color: #1a73e8 !important; }
    </style>
</div>

{{-- 同意 --}}
<div class="form-section">
    <h5>応募時課金への同意 <span class="text-danger">*</span></h5>
    <div class="agreement-box mb-3">
        <p class="mb-0 small">{{ $agreementText }}</p>
    </div>
    <div class="form-check">
        <input class="form-check-input @error('agreement_flag') is-invalid @enderror"
               type="checkbox" name="agreement_flag" id="agreement_flag" value="1"
               {{ old('agreement_flag') ? 'checked' : '' }} required>
        <label class="form-check-label fw-bold" for="agreement_flag">上記の内容に同意します</label>
        @error('agreement_flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

{{-- 利用規約・プライバシーポリシー同意 --}}
<div class="form-section">
    <h5>利用規約・プライバシーポリシーへの同意 <span class="text-danger">*</span></h5>
    <div class="form-check">
        <input class="form-check-input @error('terms_agreed') is-invalid @enderror"
               type="checkbox" name="terms_agreed" id="terms_agreed" value="1"
               {{ old('terms_agreed') ? 'checked' : '' }} required>
        <label class="form-check-label" for="terms_agreed">
            <a href="{{ route('terms') }}" target="_blank" rel="noopener">利用規約</a>
            および
            <a href="{{ route('privacy-policy') }}" target="_blank" rel="noopener">プライバシーポリシー</a>
            に同意します
        </label>
        @error('terms_agreed') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<input type="hidden" name="trial_confirmed" id="trialConfirmed" value="0">

<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary btn-lg px-5">求人を登録する</button>
</div>

</form>

{{-- トライアル終了確認モーダル --}}
<div class="modal fade" id="trialEndedModal" tabindex="-1" aria-hidden="true">
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
                <button type="button" class="btn btn-primary" id="trialConfirmBtn">掲載する</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<script>
const trialEndedModal = new bootstrap.Modal(document.getElementById('trialEndedModal'));

document.getElementById('jobCreateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;

    // 確認済みならそのまま送信
    if (document.getElementById('trialConfirmed').value === '1') {
        doSubmit(form);
        return;
    }

    const email = (document.querySelector('[name="contact_email"]')?.value || '').trim();
    if (email) {
        try {
            const res = await fetch(`{{ route('jobs.check_trial') }}?email=${encodeURIComponent(email)}`);
            const data = await res.json();
            if (data.trial_ended) {
                trialEndedModal.show();
                return;
            }
        } catch (err) {}
    }

    doSubmit(form);
});

document.getElementById('trialConfirmBtn').addEventListener('click', function() {
    trialEndedModal.hide();
    document.getElementById('trialConfirmed').value = '1';
    document.getElementById('jobCreateForm').dispatchEvent(new Event('submit'));
});

function doSubmit(form) {
    @if(config('services.recaptcha.site_key'))
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'job_store'}).then(function(token) {
            document.getElementById('recaptchaToken').value = token;
            form.submit();
        });
    });
    @else
    form.submit();
    @endif
}
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
