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
