@extends('admin.layouts.app')
@section('title', '記事編集')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-pencil me-2"></i>記事編集</h1>
    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>一覧に戻る
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.articles.update', $article) }}">
    @csrf @method('PUT')

    <div class="card mb-3">
        <div class="card-header fw-bold">基本情報</div>
        <div class="card-body row g-3">

            <div class="col-12">
                <label class="form-label fw-bold small">タイトル <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required maxlength="100">
            </div>

            <div class="col-12">
                <label class="form-label fw-bold small">H1 見出し</label>
                <input type="text" name="h1" class="form-control" value="{{ old('h1', $article->h1) }}" maxlength="100">
            </div>

            <div class="col-12">
                <label class="form-label fw-bold small">メタディスクリプション</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $article->meta_description) }}" maxlength="200">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">関連エリア</label>
                <select name="area_id" class="form-select">
                    <option value="">指定なし</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('area_id', $article->area_id) == $area->id ? 'selected' : '' }}>
                        {{ $area->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">関連職種</label>
                <select name="job_type_id" class="form-select">
                    <option value="">指定なし</option>
                    @foreach($jobTypes as $category => $types)
                    <optgroup label="{{ $category }}">
                        @foreach($types as $jt)
                        <option value="{{ $jt->id }}" {{ old('job_type_id', $article->job_type_id) == $jt->id ? 'selected' : '' }}>
                            {{ $jt->name }}
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">アイキャッチ画像URL</label>
                <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $article->image_url) }}" maxlength="500">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">公開日時</label>
                <input type="datetime-local" name="published_at" class="form-control"
                       value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-bold">本文</div>
        <div class="card-body">
            <textarea name="body" class="form-control" rows="24" required
                      style="font-family:monospace;font-size:0.85rem;">{{ old('body', $article->body) }}</textarea>
            <div class="form-text text-muted mt-1" style="font-size:.75rem;">HTML / Markdown で記述できます</div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>保存する
        </button>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">キャンセル</a>
    </div>
</form>

@endsection
