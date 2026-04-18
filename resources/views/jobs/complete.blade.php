@extends('layouts.app')

@section('title', '求人登録完了')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="form-section text-center py-5">
    <div class="display-1 mb-3">✅</div>
    <h2 class="fw-bold mb-2">求人登録が完了しました</h2>
    <p class="text-muted">求人LPが自動生成されました。</p>
</div>

<div class="form-section">
    <h5>生成された求人情報</h5>
    <table class="table table-borderless mb-0">
        <tr><th class="w-25 text-muted">タイトル</th><td>{{ $job->title }}</td></tr>
        <tr><th class="text-muted">SEOタイトル</th><td>{{ $job->seo_title }}</td></tr>
        <tr><th class="text-muted">メタディスクリプション</th><td>{{ $job->meta_description }}</td></tr>
        <tr><th class="text-muted">ステータス</th><td><span class="badge bg-success">公開中</span></td></tr>
    </table>
</div>

<div class="form-section">
    <h5>編集URL（大切に保管してください）</h5>
    <div class="alert alert-warning">
        <p class="mb-2 small">このURLを紛失すると求人の編集ができなくなります。必ずブックマークまたはメモしておいてください。</p>
        <div class="input-group">
            <input type="text" class="form-control" id="editUrl" value="{{ $editUrl }}" readonly>
            <button class="btn btn-outline-secondary" onclick="copyUrl()">コピー</button>
        </div>
    </div>
</div>

<div class="text-center mt-2">
    <a href="{{ route('jobs.create') }}" class="btn btn-outline-primary">別の求人を登録する</a>
</div>

</div>
</div>

<script>
function copyUrl() {
    const input = document.getElementById('editUrl');
    navigator.clipboard.writeText(input.value).then(() => {
        alert('URLをコピーしました');
    });
}
</script>
@endsection
