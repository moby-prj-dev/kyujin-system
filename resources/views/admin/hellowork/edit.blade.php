@extends('admin.layouts.app')
@section('title', 'ハローワーク求人編集')
@section('content')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.hellowork.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="page-title mb-0">ハローワーク求人編集</h1>
</div>

{{-- 読み取り専用情報 --}}
<div class="card mb-3">
    <div class="card-header text-muted small">ハローワーク情報（読み取り専用）</div>
    <div class="card-body">
        <div class="row g-2 small">
            <div class="col-sm-6">
                <span class="text-muted">事業所名：</span>
                <strong>{{ $job->company_name }}</strong>
            </div>
            <div class="col-sm-6">
                <span class="text-muted">求人番号：</span>
                <code>{{ $job->hw_job_no }}</code>
            </div>
            <div class="col-sm-6">
                <span class="text-muted">賃金：</span>{{ $job->salary_note }}
            </div>
            <div class="col-sm-6">
                <span class="text-muted">紹介期限：</span>
                <span class="{{ $job->expires_at?->isPast() ? 'text-danger' : '' }}">
                    {{ $job->expires_at?->format('Y年m月d日') ?? '—' }}
                </span>
            </div>
            <div class="col-12">
                <a href="{{ $job->hw_job_url }}" target="_blank" rel="noopener" class="small">
                    <i class="bi bi-box-arrow-up-right me-1"></i>ハローワークで確認
                </a>
            </div>
        </div>
    </div>
</div>

{{-- 編集フォーム --}}
<div class="card">
    <div class="card-header">AI生成コンテンツを編集</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.hellowork.update', $job) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">LPタイトル <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $job->title) }}" maxlength="100">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">SEOタイトル</label>
                <input type="text" name="seo_title" class="form-control"
                       value="{{ old('seo_title', $job->seo_title) }}" maxlength="100">
                <div class="form-text">検索結果に表示されるタイトル（60文字推奨）</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">メタディスクリプション</label>
                <input type="text" name="meta_description" class="form-control"
                       value="{{ old('meta_description', $job->meta_description) }}" maxlength="320">
                <div class="form-text">検索結果の説明文（120文字推奨）</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">LP本文</label>
                <textarea name="description_generated" class="form-control" rows="12"
                          >{{ old('description_generated', $job->description_generated) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>保存する
                </button>
                <a href="{{ route('admin.hellowork.index') }}" class="btn btn-outline-secondary">キャンセル</a>
            </div>
        </form>
    </div>
</div>

@endsection
