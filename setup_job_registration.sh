#!/bin/bash
# ============================================================
# 求人登録画面 実装スクリプト
# 実行場所: ~/kyujin-system
# 実行方法: bash setup_job_registration.sh
# ============================================================

set -e
echo "=========================================="
echo " 求人登録画面の実装を開始します..."
echo "=========================================="

mkdir -p app/Http/Requests
mkdir -p app/Http/Controllers
mkdir -p app/Services
mkdir -p resources/views/jobs
mkdir -p resources/views/layouts

# ============================================================
# 1. Route追加
# ============================================================
echo ""
echo "[1/5] Routeを追加中..."

cat >> routes/web.php << 'EOF'

// -----------------------------------------------
// 求人登録（掲載主向け）
// -----------------------------------------------
Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{token}/complete', [\App\Http\Controllers\JobController::class, 'complete'])->name('jobs.complete');
Route::get('/jobs/{token}/edit', [\App\Http\Controllers\JobController::class, 'edit'])->name('jobs.edit');
Route::put('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
EOF

echo "  ✅ Route追加完了"

# ============================================================
# 2. StoreJobRequest
# ============================================================
echo ""
echo "[2/5] StoreJobRequestを作成中..."

cat > app/Http/Requests/StoreJobRequest.php << 'EOF'
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area_id'             => ['required', 'integer', 'exists:master_areas,id'],
            'job_type_id'         => ['required', 'integer', 'exists:master_job_types,id'],
            'employment_type_id'  => ['required', 'integer', 'exists:master_employment_types,id'],
            'conditions'          => ['required', 'array', 'min:1'],
            'conditions.*'        => ['integer', 'exists:master_conditions,id'],
            'appeals'             => ['required', 'array', 'min:1'],
            'appeals.*'           => ['integer', 'exists:master_appeals,id'],
            'contact_email'       => ['required', 'email', 'max:255'],
            'contact_phone'       => ['required', 'string', 'max:20'],
            'agreement_flag'      => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'area_id.required'            => 'エリアを選択してください。',
            'job_type_id.required'        => '職種を選択してください。',
            'employment_type_id.required' => '雇用形態を選択してください。',
            'conditions.required'         => '勤務条件を1つ以上選択してください。',
            'conditions.min'              => '勤務条件を1つ以上選択してください。',
            'appeals.required'            => 'アピールポイントを1つ以上選択してください。',
            'appeals.min'                 => 'アピールポイントを1つ以上選択してください。',
            'contact_email.required'      => '連絡先メールアドレスを入力してください。',
            'contact_email.email'         => '正しいメールアドレス形式で入力してください。',
            'contact_phone.required'      => '電話番号を入力してください。',
            'agreement_flag.required'     => '応募時課金への同意が必要です。',
            'agreement_flag.accepted'     => '応募時課金への同意にチェックを入れてください。',
        ];
    }
}
EOF

echo "  ✅ StoreJobRequest作成完了"

# ============================================================
# 3. SeoGeneratorService
# ============================================================
echo ""
echo "[3/5] SeoGeneratorServiceを作成中..."

cat > app/Services/SeoGeneratorService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\Job;

class SeoGeneratorService
{
    /**
     * 求人データからSEOテキストを生成してJobに保存する
     */
    public function generate(Job $job): void
    {
        $area           = $job->area->name;
        $jobType        = $job->jobType->name;
        $employmentType = $job->employmentType->name;

        $conditions = $job->jobConditions()
            ->with('condition')
            ->get()
            ->pluck('condition.name')
            ->toArray();

        $appeals = $job->jobAppeals()
            ->with('appeal')
            ->get()
            ->pluck('appeal.name')
            ->toArray();

        $conditionStr = implode('・', array_slice($conditions, 0, 3));
        $appealStr    = implode('・', array_slice($appeals, 0, 3));

        // テンプレートをランダム選択（ページ重複率を下げる）
        $templates = $this->getTitleTemplates($area, $jobType, $employmentType, $conditionStr, $appealStr);
        $templateIndex = crc32($area . $jobType) % count($templates);

        $job->seo_title            = $templates[$templateIndex];
        $job->meta_description     = $this->generateMeta($area, $jobType, $employmentType, $conditionStr, $appealStr);
        $job->description_generated = $this->generateDescription($area, $jobType, $employmentType, $conditions, $appeals);
        $job->title                = "{$area}の{$jobType}求人";
        $job->save();
    }

    private function getTitleTemplates(string $area, string $jobType, string $employment, string $conditions, string $appeals): array
    {
        return [
            "【{$area}】{$jobType}募集｜{$conditions}",
            "{$area}で{$jobType}のお仕事｜{$appeals}",
            "{$jobType}求人【{$area}】{$employment}・{$appeals}",
            "【急募】{$area}の{$jobType}｜{$conditions}・{$appeals}",
        ];
    }

    private function generateMeta(string $area, string $jobType, string $employment, string $conditions, string $appeals): string
    {
        return "{$area}エリアで{$jobType}（{$employment}）を募集中。{$conditions}。{$appeals}。LINE応募OK。お気軽にご応募ください。";
    }

    private function generateDescription(string $area, string $jobType, string $employment, array $conditions, array $appeals): string
    {
        $conditionList = implode("\n", array_map(fn($c) => "・{$c}", $conditions));
        $appealList    = implode("\n", array_map(fn($a) => "・{$a}", $appeals));

        return <<<TEXT
{$area}で{$jobType}のお仕事をお探しの方へ

現在、{$area}エリアで{$jobType}（{$employment}）のスタッフを募集しています。

【勤務条件】
{$conditionList}

【この求人のポイント】
{$appealList}

LINE応募またはフォームからお気軽にご応募ください。
応募後、担当者より折り返しご連絡いたします。
TEXT;
    }
}
EOF

echo "  ✅ SeoGeneratorService作成完了"

# ============================================================
# 4. JobController
# ============================================================
echo ""
echo "[4/5] JobControllerを作成中..."

cat > app/Http/Controllers/JobController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Models\AuditLog;
use App\Models\BillingAgreement;
use App\Models\Job;
use App\Models\JobAppeal;
use App\Models\JobCondition;
use App\Models\MasterAppeal;
use App\Models\MasterArea;
use App\Models\MasterCondition;
use App\Models\MasterEmploymentType;
use App\Models\MasterJobType;
use App\Services\SeoGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function __construct(
        private SeoGeneratorService $seoGenerator
    ) {}

    /**
     * 求人登録フォーム表示
     */
    public function create()
    {
        $areas           = MasterArea::active()->orderBy('prefecture')->orderBy('sort_order')->get()->groupBy('prefecture');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');

        $agreementText    = config('billing.agreement_text');
        $agreementVersion = config('billing.agreement_version');

        return view('jobs.create', compact(
            'areas', 'jobTypes', 'employmentTypes',
            'conditions', 'appeals',
            'agreementText', 'agreementVersion'
        ));
    }

    /**
     * 求人登録処理
     */
    public function store(StoreJobRequest $request)
    {
        DB::transaction(function () use ($request, &$job) {
            // 1. 求人レコード作成（token は Model の boot で自動生成）
            $job = Job::create([
                'area_id'            => $request->area_id,
                'job_type_id'        => $request->job_type_id,
                'employment_type_id' => $request->employment_type_id,
                'status'             => Job::STATUS_DRAFT,
                'contact_email'      => $request->contact_email,
                'contact_phone'      => $request->contact_phone,
                'title'              => '（生成中）',
            ]);

            // 2. 勤務条件を保存
            foreach ($request->conditions as $conditionId) {
                JobCondition::create([
                    'job_id'       => $job->id,
                    'condition_id' => $conditionId,
                ]);
            }

            // 3. アピールポイントを保存
            foreach ($request->appeals as $appealId) {
                JobAppeal::create([
                    'job_id'    => $job->id,
                    'appeal_id' => $appealId,
                ]);
            }

            // 4. 同意情報を保存
            BillingAgreement::create([
                'job_id'                  => $job->id,
                'agreement_flag'          => true,
                'agreement_text'          => config('billing.agreement_text'),
                'agreement_text_version'  => config('billing.agreement_version'),
                'agreed_at'               => now(),
                'agreed_ip'               => $request->ip(),
                'user_agent'              => $request->userAgent(),
            ]);

            // 5. SEOテキスト自動生成
            $job->load(['area', 'jobType', 'employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            $this->seoGenerator->generate($job);

            // 6. ステータスを公開に変更
            $job->update(['status' => Job::STATUS_ACTIVE]);

            // 7. 監査ログ
            AuditLog::record(
                AuditLog::ENTITY_JOB,
                $job->id,
                AuditLog::ACTION_JOB_CREATED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'area_id'            => $job->area_id,
                    'job_type_id'        => $job->job_type_id,
                    'employment_type_id' => $job->employment_type_id,
                    'contact_email'      => $job->contact_email,
                    'status'             => $job->status,
                ]
            );

            AuditLog::record(
                AuditLog::ENTITY_AGREEMENT,
                $job->id,
                AuditLog::ACTION_AGREEMENT_SAVED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'job_id'                 => $job->id,
                    'agreement_text_version' => config('billing.agreement_version'),
                    'agreed_ip'              => $request->ip(),
                ]
            );

            AuditLog::record(
                AuditLog::ENTITY_JOB,
                $job->id,
                AuditLog::ACTION_LP_GENERATED,
                AuditLog::ACTOR_SYSTEM,
                [
                    'seo_title'        => $job->seo_title,
                    'meta_description' => $job->meta_description,
                ]
            );
        });

        return redirect()->route('jobs.complete', ['token' => $job->token]);
    }

    /**
     * 求人登録完了画面
     */
    public function complete(string $token)
    {
        $job = Job::scopeByToken(Job::query(), $token)->firstOrFail();
        $editUrl = route('jobs.edit', ['token' => $token]);

        return view('jobs.complete', compact('job', 'editUrl'));
    }

    /**
     * 求人編集フォーム表示
     */
    public function edit(string $token)
    {
        $job = Job::with(['jobConditions', 'jobAppeals'])
            ->where('token', $token)
            ->firstOrFail();

        $areas           = MasterArea::active()->orderBy('prefecture')->orderBy('sort_order')->get()->groupBy('prefecture');
        $jobTypes        = MasterJobType::active()->orderBy('sort_order')->get()->groupBy('category');
        $employmentTypes = MasterEmploymentType::active()->orderBy('sort_order')->get();
        $conditions      = MasterCondition::active()->orderBy('sort_order')->get()->groupBy('category');
        $appeals         = MasterAppeal::active()->orderBy('sort_order')->get()->groupBy('category');

        $selectedConditions = $job->jobConditions->pluck('condition_id')->toArray();
        $selectedAppeals    = $job->jobAppeals->pluck('appeal_id')->toArray();

        return view('jobs.edit', compact(
            'job', 'areas', 'jobTypes', 'employmentTypes',
            'conditions', 'appeals',
            'selectedConditions', 'selectedAppeals'
        ));
    }

    /**
     * 求人更新処理
     */
    public function update(Request $request, string $token)
    {
        $job = Job::where('token', $token)->firstOrFail();

        $request->validate([
            'area_id'            => ['required', 'integer', 'exists:master_areas,id'],
            'job_type_id'        => ['required', 'integer', 'exists:master_job_types,id'],
            'employment_type_id' => ['required', 'integer', 'exists:master_employment_types,id'],
            'conditions'         => ['required', 'array', 'min:1'],
            'conditions.*'       => ['integer', 'exists:master_conditions,id'],
            'appeals'            => ['required', 'array', 'min:1'],
            'appeals.*'          => ['integer', 'exists:master_appeals,id'],
            'contact_email'      => ['required', 'email'],
            'contact_phone'      => ['required', 'string'],
        ]);

        DB::transaction(function () use ($request, $job) {
            $job->update([
                'area_id'            => $request->area_id,
                'job_type_id'        => $request->job_type_id,
                'employment_type_id' => $request->employment_type_id,
                'contact_email'      => $request->contact_email,
                'contact_phone'      => $request->contact_phone,
            ]);

            // 条件・アピールを一旦削除して再登録
            $job->jobConditions()->delete();
            $job->jobAppeals()->delete();

            foreach ($request->conditions as $conditionId) {
                JobCondition::create(['job_id' => $job->id, 'condition_id' => $conditionId]);
            }
            foreach ($request->appeals as $appealId) {
                JobAppeal::create(['job_id' => $job->id, 'appeal_id' => $appealId]);
            }

            // SEO再生成
            $job->load(['area', 'jobType', 'employmentType', 'jobConditions.condition', 'jobAppeals.appeal']);
            app(SeoGeneratorService::class)->generate($job);
        });

        return redirect()->route('jobs.complete', ['token' => $token])->with('updated', true);
    }
}
EOF

echo "  ✅ JobController作成完了"

# ============================================================
# 5. billing config
# ============================================================
cat > config/billing.php << 'EOF'
<?php

return [
    'agreement_text' => '本求人への応募が発生した時点で、所定の成果報酬料金が発生することに同意します。',
    'agreement_version' => 'v1.0',
    'amount' => 5000, // 1応募あたりの課金金額（円）※後で変更可
];
EOF

echo "  ✅ billing config作成完了"

# ============================================================
# 6. レイアウト
# ============================================================
echo ""
echo "[5/5] Bladeビューを作成中..."

cat > resources/views/layouts/app.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '求人掲載システム')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-section { background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .form-section h5 { font-weight: 700; border-left: 4px solid #0d6efd; padding-left: 10px; margin-bottom: 16px; }
        .check-group label { cursor: pointer; }
        .agreement-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 16px; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold">求人掲載システム</span>
    </div>
</nav>
<main class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('updated'))
        <div class="alert alert-info">求人情報を更新しました。</div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOF

# ============================================================
# 7. 求人登録フォーム
# ============================================================
cat > resources/views/jobs/create.blade.php << 'EOF'
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
EOF

# ============================================================
# 8. 求人登録完了画面
# ============================================================
cat > resources/views/jobs/complete.blade.php << 'EOF'
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
EOF

echo "  ✅ Bladeビュー作成完了"

# ============================================================
# 完了
# ============================================================
echo ""
echo "=========================================="
echo " ✅ 求人登録画面の実装が完了しました！"
echo ""
echo "   ブラウザで以下にアクセスしてください："
echo "   http://localhost/jobs/create"
echo "=========================================="
