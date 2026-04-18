@extends('layouts.app')

@section('title', '求人登録')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<h2 class="mb-4 fw-bold">求人登録</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('jobs.store') }}">
@csrf

{{-- エリア --}}
<div class="form-section">
    <h5>勤務エリア <span class="text-danger">*</span></h5>
    <select name="area_id" class="form-select @error('area_id') is-invalid @enderror" required>
        <option value="">選択してください</option>
        @foreach($areas as $prefecture => $areaList)
            <optgroup label="{{ $prefecture }}">
                @foreach($areaList as $area)
                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->region }} / {{ $area->name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @error('area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- 職種 --}}
<div class="form-section">
    <h5>職種 <span class="text-danger">*</span></h5>
    <select name="job_type_id" class="form-select @error('job_type_id') is-invalid @enderror" required>
        <option value="">選択してください</option>
        @foreach($jobTypes as $category => $typeList)
            <optgroup label="{{ $category }}">
                @foreach($typeList as $type)
                    <option value="{{ $type->id }}" {{ old('job_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @error('job_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

{{-- 雇用形態 --}}
<div class="form-section">
    <h5>雇用形態 <span class="text-danger">*</span></h5>
    <div class="d-flex flex-wrap gap-3">
        @foreach($employmentTypes as $et)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="employment_type_id"
                   id="et_{{ $et->id }}" value="{{ $et->id }}"
                   {{ old('employment_type_id') == $et->id ? 'checked' : '' }} required>
            <label class="form-check-label" for="et_{{ $et->id }}">{{ $et->name }}</label>
        </div>
        @endforeach
    </div>
    @error('employment_type_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- 勤務条件 --}}
<div class="form-section">
    <h5>勤務条件 <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    @foreach($conditions as $category => $condList)
        <p class="text-muted small mb-1 mt-3"><strong>{{ $category }}</strong></p>
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
    @endforeach
    @error('conditions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- アピールポイント --}}
<div class="form-section">
    <h5>アピールポイント <span class="text-danger">*</span> <small class="text-muted fw-normal">（複数選択可）</small></h5>
    @foreach($appeals as $category => $appealList)
        <p class="text-muted small mb-1 mt-3"><strong>{{ $category }}</strong></p>
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
    @endforeach
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
               value="{{ old('contact_phone') }}" placeholder="03-0000-0000" required>
        @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
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
        <label class="form-check-label fw-bold" for="agreement_flag">
            上記の内容に同意します
        </label>
        @error('agreement_flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary btn-lg px-5">求人を登録する</button>
</div>

</form>
</div>
</div>
@endsection
