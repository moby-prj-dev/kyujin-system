@extends('admin.layouts.app')
@section('title', '設定')
@section('content')

<div class="mb-3">
    <h1 class="page-title mb-0"><i class="bi bi-gear me-2"></i>設定</h1>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:520px;">
    <div class="card-header px-4 py-3 fw-bold">モニター無料期間</div>
    <div class="card-body px-4 py-4">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">開始日</label>
                <input type="date" name="monitor_start_date" class="form-control"
                       value="{{ $startDate }}" required>
                @error('monitor_start_date')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">期間（ヶ月）</label>
                <div class="input-group" style="max-width:160px;">
                    <input type="number" name="monitor_months" class="form-control"
                           value="{{ $months }}" min="1" max="24" required>
                    <span class="input-group-text">ヶ月</span>
                </div>
                @error('monitor_months')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 p-3 rounded" style="background:#f8faff;border:1.5px solid #c5d8f8;">
                <div class="text-muted small fw-bold mb-1">締切日（自動計算）</div>
                <div class="fw-bold fs-5" id="computed-cutoff">
                    {{ $cutoff->format('Y年m月d日') }}
                </div>
            </div>

            <button type="submit" class="btn btn-primary">保存する</button>
        </form>

        <hr class="my-4">

        {{-- モニター解除/再開ボタン --}}
        <div class="p-3 rounded" style="background:{{ $disabled ? '#fff3cd' : '#f0f7ff' }};border:1.5px solid {{ $disabled ? '#f9a825' : '#c5d8f8' }};">
            <div class="fw-bold mb-2">
                @if($disabled)
                    <i class="bi bi-pause-circle-fill me-1" style="color:#e65100;"></i>モニター募集は現在【解除中】です
                @else
                    <i class="bi bi-play-circle-fill me-1" style="color:#1a73e8;"></i>モニター募集は現在【有効】です
                @endif
            </div>
            <p class="text-muted small mb-3">
                @if($disabled)
                    全ての「無料モニター募集」告知UIが非表示になっています。<br>
                    再開すると、上記の期間設定に基づいて再度表示されます。
                @else
                    上記の期間中は「無料モニター募集中」バーが各ページに表示されます。<br>
                    <strong>「解除」ボタンを押すと即座に全告知UIが非表示</strong>になります(期間設定は保持)。
                @endif
            </p>
            <form method="POST" action="{{ route('admin.settings.toggle_monitor') }}"
                  onsubmit="return confirm('{{ $disabled ? "モニター募集を再開しますか?" : "モニター募集を解除しますか?(全告知UIが非表示になります)" }}');">
                @csrf
                <input type="hidden" name="disable" value="{{ $disabled ? '0' : '1' }}">
                <button type="submit" class="btn {{ $disabled ? 'btn-success' : 'btn-warning' }}">
                    @if($disabled)
                        <i class="bi bi-play-fill me-1"></i>モニター募集を再開する
                    @else
                        <i class="bi bi-pause-fill me-1"></i>モニター募集を解除する
                    @endif
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput  = document.querySelector('[name="monitor_start_date"]');
    const monthsInput = document.querySelector('[name="monitor_months"]');
    const display     = document.getElementById('computed-cutoff');

    function update() {
        const start  = new Date(startInput.value);
        const months = parseInt(monthsInput.value, 10);
        if (!startInput.value || isNaN(months) || months < 1) return;
        const cutoff = new Date(start);
        cutoff.setMonth(cutoff.getMonth() + months);
        display.textContent = cutoff.getFullYear() + '年'
            + (cutoff.getMonth() + 1) + '月'
            + cutoff.getDate() + '日';
    }

    startInput.addEventListener('change', update);
    monthsInput.addEventListener('input', update);
});
</script>

@endsection
