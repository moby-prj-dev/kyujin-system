@extends('admin.layouts.app')
@section('title', '記事管理')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>記事管理</h1>
</div>

@if(session('success'))
    <div class="alert alert-success mb-3">
        <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
    </div>
@endif

@if($errors->has('generate'))
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $errors->first('generate') }}
    </div>
@endif

<div class="row g-4">

    {{-- 記事生成フォーム --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-magic text-primary"></i>記事を生成する
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.articles.generate') }}" id="generateForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold small">キーワード <span class="text-danger">*</span></label>
                        <input type="text" name="keywords" class="form-control form-control-sm @error('keywords') is-invalid @enderror"
                               value="{{ old('keywords') }}"
                               placeholder="例：介護職員, 那覇市, 給与, 夜勤, 施設">
                        <div class="form-text text-muted" style="font-size:.75rem;">カンマ区切りで入力してください</div>
                        @error('keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">カテゴリー <span class="text-danger">*</span></label>
                        <select name="category" class="form-select form-select-sm @error('category') is-invalid @enderror">
                            <option value="">選択してください</option>
                            <option value="industry"      {{ old('category') === 'industry'      ? 'selected' : '' }}>業界情報</option>
                            <option value="job_type"      {{ old('category') === 'job_type'      ? 'selected' : '' }}>職種別情報</option>
                            <option value="area"          {{ old('category') === 'area'          ? 'selected' : '' }}>エリア情報</option>
                            <option value="qualification" {{ old('category') === 'qualification' ? 'selected' : '' }}>資格・研修</option>
                            <option value="beginner"      {{ old('category') === 'beginner'      ? 'selected' : '' }}>未経験・転職</option>
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">関連エリア <span class="text-muted fw-normal">（任意）</span></label>
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">指定なし</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}（{{ $area->region }}）
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">関連職種 <span class="text-muted fw-normal">（任意）</span></label>
                        <select name="job_type_id" class="form-select form-select-sm">
                            <option value="">指定なし</option>
                            @foreach($jobTypes as $category => $types)
                                <optgroup label="{{ $category }}">
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('job_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">スラッグ <span class="text-muted fw-normal">（任意・空欄で自動生成）</span></label>
                        <input type="text" name="slug" class="form-control form-control-sm @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}"
                               placeholder="例：naha-kaigo-staff-jobs">
                        <div class="form-text text-muted" style="font-size:.75rem;">半角英数字・ハイフンのみ。同じスラッグは上書きされます</div>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold" id="generateBtn">
                        <i class="bi bi-magic me-1"></i>Geminiで記事を生成する
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 記事一覧 --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-ul me-1"></i>生成済み記事（{{ $articles->count() }}件）</span>
                <a href="{{ route('articles.index') }}" target="_blank"
                   class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>公開ページを見る
                </a>
            </div>
            @php
            $categoryLabels = [
                'industry'      => ['業界情報',    'primary',   'bi-bar-chart-fill'],
                'job_type'      => ['職種別',      'success',   'bi-briefcase-fill'],
                'area'          => ['エリア',      'warning',   'bi-geo-alt-fill'],
                'qualification' => ['資格・研修',  'secondary', 'bi-award-fill'],
                'beginner'      => ['未経験・転職','info',      'bi-person-plus-fill'],
            ];
            @endphp
            @if($articles->isEmpty())
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-file-earmark" style="font-size:2rem;opacity:.3;display:block;margin-bottom:8px;"></i>
                    <p class="mb-0 small">まだ記事がありません。左のフォームから生成してください。</p>
                </div>
            @else
                <div class="p-3" style="max-height:680px;overflow-y:auto;">
                    @foreach($articles as $article)
                    @php [$label, $color, $icon] = $categoryLabels[$article->category] ?? [$article->category, 'secondary', 'bi-file-text']; @endphp
                    <div class="d-flex align-items-start gap-2 p-2 rounded mb-1"
                         style="border:1px solid #e8f0f8;background:#fafcff;">
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-{{ $color }}" style="font-size:.7rem;">
                                    <i class="{{ $icon }} me-1"></i>{{ $label }}
                                </span>
                                <span class="text-muted" style="font-size:.72rem;">
                                    {{ $article->updated_at->format('m/d H:i') }}
                                </span>
                            </div>
                            <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                               class="text-decoration-none text-dark fw-bold d-block"
                               style="font-size:.84rem;line-height:1.4;">
                                {{ $article->title }}
                            </a>
                            <div class="text-muted" style="font-size:.72rem;">{{ $article->slug }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="flex-shrink-0"
                              onsubmit="return confirm('「{{ addslashes($article->title) }}」を削除しますか？')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger">削除</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<script>
document.getElementById('generateForm').addEventListener('submit', function() {
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>生成中...（30秒ほどかかります）';
});
</script>

@endsection
