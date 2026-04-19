#!/bin/bash
set -e
cd ~/kyujin-system

echo "========================================"
echo "  ③ SEO求人LP表示画面 実装開始"
echo "========================================"

# =============================================
# 1. LpController.php の作成
# =============================================
echo "[1/5] LpController.php を作成中..."

cat > app/Http/Controllers/LpController.php << 'CONTROLLER_EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\LpView;
use Illuminate\Http\Request;

class LpController extends Controller
{
    public function show(Request $request, string $token)
    {
        $job = Job::with(['area', 'jobType', 'employmentType', 'conditions', 'appeals'])
            ->where('token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        try {
            LpView::create([
                'job_listing_id' => $job->id,
                'ip_address'     => $request->ip(),
                'user_agent'     => substr($request->userAgent() ?? '', 0, 500),
            ]);
        } catch (\Exception $e) {
            \Log::warning('lp_view記録失敗: ' . $e->getMessage());
        }

        return view('lp.show', compact('job'));
    }
}
CONTROLLER_EOF
echo "  ✓ LpController.php 作成完了"

# =============================================
# 2. LpView モデル（未存在時のみ）
# =============================================
echo "[2/5] LpView モデルを確認中..."
if [ ! -f app/Models/LpView.php ]; then
cat > app/Models/LpView.php << 'MODEL_EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpView extends Model
{
    protected $table = 'lp_views';

    protected $fillable = [
        'job_listing_id',
        'ip_address',
        'user_agent',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_listing_id');
    }
}
MODEL_EOF
    echo "  ✓ LpView.php 作成完了"
else
    echo "  ✓ LpView.php 既存（スキップ）"
fi

# =============================================
# 3. Bladeビュー作成
# =============================================
echo "[3/5] Bladeビューを作成中..."
mkdir -p resources/views/lp

cat > resources/views/lp/show.blade.php << 'BLADE_EOF'
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->seo_title }}</title>
    <meta name="description" content="{{ $job->seo_description }}">
    <meta property="og:title" content="{{ $job->seo_title }}">
    <meta property="og:description" content="{{ $job->seo_description }}">
    <meta property="og:type" content="website">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color:#f5f7fa; color:#333; font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif; }
        .lp-wrap { max-width:640px; margin:0 auto; }
        .lp-header { background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%); color:#fff; padding:2rem 1.5rem 1.8rem; }
        .lp-header .area-badge { display:inline-flex; align-items:center; gap:4px; font-size:0.82rem; background:rgba(255,255,255,0.22); border-radius:20px; padding:0.25rem 0.85rem; margin-bottom:0.85rem; }
        .lp-header h1 { font-size:1.5rem; font-weight:800; line-height:1.55; margin-bottom:0.8rem; }
        .lp-header .meta-badges { display:flex; flex-wrap:wrap; gap:0.45rem; }
        .meta-badge { font-size:0.8rem; font-weight:600; padding:0.28rem 0.8rem; border-radius:4px; }
        .badge-job-type { background:rgba(255,255,255,0.9); color:#1a73e8; }
        .badge-emp-type { background:#ffd54f; color:#5d4037; }
        .lp-content { padding:1rem 1rem 180px; }
        .section-card { background:#fff; border-radius:12px; box-shadow:0 1px 6px rgba(0,0,0,0.07); padding:1.25rem 1.5rem; margin-bottom:1rem; }
        .section-title { font-size:0.95rem; font-weight:800; color:#1a73e8; display:flex; align-items:center; gap:6px; margin-bottom:1rem; padding-bottom:0.6rem; border-bottom:2px solid #e8f0fe; }
        .info-row { display:flex; align-items:flex-start; padding:0.55rem 0; border-bottom:1px solid #f2f2f2; font-size:0.93rem; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#777; width:100px; flex-shrink:0; font-weight:500; }
        .info-value { flex:1; font-weight:600; color:#222; }
        .tag-list { display:flex; flex-wrap:wrap; gap:0.5rem; }
        .tag-item { font-size:0.83rem; font-weight:600; border-radius:20px; padding:0.3rem 0.9rem; }
        .tag-condition { background:#e8f0fe; color:#1a73e8; }
        .tag-appeal { background:#e6f4ea; color:#137333; }
        .seo-body-text { font-size:0.92rem; line-height:1.85; color:#444; white-space:pre-line; }
        .apply-method-item { display:flex; gap:0.75rem; align-items:flex-start; padding:0.7rem 0; border-bottom:1px solid #f2f2f2; font-size:0.9rem; }
        .apply-method-item:last-child { border-bottom:none; }
        .apply-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; }
        .apply-icon-line { background:#e8faf0; color:#06C755; }
        .apply-icon-form { background:#e8f0fe; color:#1a73e8; }
        .apply-method-title { font-weight:700; color:#222; margin-bottom:2px; }
        .apply-method-desc { color:#666; font-size:0.82rem; }
        .cta-bar { position:fixed; bottom:0; left:0; right:0; background:rgba(255,255,255,0.97); backdrop-filter:blur(8px); border-top:1px solid #e0e0e0; padding:0.85rem 1rem; z-index:200; box-shadow:0 -4px 16px rgba(0,0,0,0.08); }
        .cta-inner { max-width:640px; margin:0 auto; }
        .btn-line-apply { display:flex; align-items:center; justify-content:center; gap:0.5rem; width:100%; background:#06C755; color:#fff; border:none; border-radius:30px; font-size:1rem; font-weight:800; padding:0.78rem 1rem; margin-bottom:0.5rem; text-decoration:none; transition:background 0.15s; }
        .btn-line-apply:hover { background:#05a847; color:#fff; }
        .btn-form-apply { display:flex; align-items:center; justify-content:center; gap:0.5rem; width:100%; background:#fff; color:#1a73e8; border:2px solid #1a73e8; border-radius:30px; font-size:1rem; font-weight:800; padding:0.7rem 1rem; text-decoration:none; transition:all 0.15s; }
        .btn-form-apply:hover { background:#1a73e8; color:#fff; }
        .cta-note { text-align:center; font-size:0.72rem; color:#aaa; margin-top:0.45rem; }
        .line-icon { width:22px; height:22px; flex-shrink:0; }
    </style>
</head>
<body>

<div class="lp-header">
    <div class="lp-wrap">
        <div class="area-badge"><i class="bi bi-geo-alt-fill"></i>{{ $job->area->name ?? '未設定' }}</div>
        <h1>{{ $job->seo_title }}</h1>
        <div class="meta-badges">
            @if($job->jobType)
                <span class="meta-badge badge-job-type">{{ $job->jobType->name }}</span>
            @endif
            @if($job->employmentType)
                <span class="meta-badge badge-emp-type">{{ $job->employmentType->name }}</span>
            @endif
        </div>
    </div>
</div>

<div class="lp-content">
    <div class="lp-wrap">

        <div class="section-card">
            <div class="section-title"><i class="bi bi-briefcase-fill"></i> 求人情報</div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-geo-alt me-1 text-primary"></i>勤務地</span>
                <span class="info-value">{{ $job->area->name ?? '未設定' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-person-badge me-1 text-primary"></i>職種</span>
                <span class="info-value">{{ $job->jobType->name ?? '未設定' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-building me-1 text-primary"></i>雇用形態</span>
                <span class="info-value">{{ $job->employmentType->name ?? '未設定' }}</span>
            </div>
        </div>

        @if($job->conditions && $job->conditions->count() > 0)
        <div class="section-card">
            <div class="section-title"><i class="bi bi-check2-circle"></i> 勤務条件</div>
            <div class="tag-list">
                @foreach($job->conditions as $condition)
                    <span class="tag-item tag-condition">{{ $condition->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($job->appeals && $job->appeals->count() > 0)
        <div class="section-card">
            <div class="section-title"><i class="bi bi-star-fill"></i> アピールポイント</div>
            <div class="tag-list">
                @foreach($job->appeals as $appeal)
                    <span class="tag-item tag-appeal">{{ $appeal->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($job->seo_body))
        <div class="section-card">
            <div class="section-title"><i class="bi bi-file-text"></i> 仕事内容・詳細</div>
            <div class="seo-body-text">{{ $job->seo_body }}</div>
        </div>
        @endif

        <div class="section-card">
            <div class="section-title"><i class="bi bi-send-fill"></i> 応募方法</div>
            <div class="apply-method-item">
                <div class="apply-icon apply-icon-line">
                    <svg class="line-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                </div>
                <div>
                    <div class="apply-method-title">LINEで応募する</div>
                    <div class="apply-method-desc">LINEアプリを使って簡単に応募できます。担当者と直接やり取りが可能です。</div>
                </div>
            </div>
            <div class="apply-method-item">
                <div class="apply-icon apply-icon-form"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <div class="apply-method-title">フォームで応募する</div>
                    <div class="apply-method-desc">お名前・電話番号を入力するだけで応募完了。LINEを使わない方にも対応しています。</div>
                </div>
            </div>
            <p class="mb-0 mt-3" style="font-size:0.78rem;color:#aaa;text-align:center;">
                <i class="bi bi-shield-check me-1"></i>応募は無料です。個人情報は適切に管理されます。
            </p>
        </div>

    </div>
</div>

<div class="cta-bar">
    <div class="cta-inner">
        <a href="/liff/{{ $job->token }}" class="btn-line-apply">
            <svg class="line-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
            LINEで応募する
        </a>
        <a href="/lp/{{ $job->token }}/apply" class="btn-form-apply">
            <i class="bi bi-pencil-square"></i>フォームで応募する
        </a>
        <p class="cta-note"><i class="bi bi-lock-fill me-1"></i>個人情報は安全に管理されます</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
BLADE_EOF
echo "  ✓ resources/views/lp/show.blade.php 作成完了"

# =============================================
# 4. routes/web.php にルート追加
# =============================================
echo "[4/5] routes/web.php にルート追加中..."
if grep -q "lp/{token}" routes/web.php 2>/dev/null; then
    echo "  ✓ /lp/{token} ルート既存（スキップ）"
else
python3 - << 'PYEOF'
with open('routes/web.php', 'r') as f:
    content = f.read()

new_routes = """
// ③ SEO求人LP表示画面
Route::get('/lp/{token}', [App\\Http\\Controllers\\LpController::class, 'show'])
    ->name('lp.show');

// ④ フォーム応募画面（プレースホルダー）
Route::get('/lp/{token}/apply', function () {
    return response('④フォーム応募画面（実装予定）', 200);
})->name('apply.form');
"""

last_idx = content.rfind('});')
if last_idx != -1:
    content = content[:last_idx] + new_routes + '\n' + content[last_idx:]
else:
    content = content.rstrip() + '\n' + new_routes + '\n'

with open('routes/web.php', 'w') as f:
    f.write(content)
print("  ✓ routes/web.php 更新完了")
PYEOF
fi

# =============================================
# 5. キャッシュクリア
# =============================================
echo "[5/5] キャッシュクリア中..."
./vendor/bin/sail artisan route:clear 2>/dev/null && echo "  ✓ route:clear 完了" || echo "  ⚠ sail未起動（後で手動実行）"
./vendor/bin/sail artisan view:clear  2>/dev/null && echo "  ✓ view:clear 完了"  || true
./vendor/bin/sail artisan config:clear 2>/dev/null && echo "  ✓ config:clear 完了" || true

echo ""
echo "========================================"
echo "  ③ 実装完了！"
echo "========================================"
echo ""
echo "【確認手順】"
echo "  1. ./vendor/bin/sail up -d"
echo "  2. http://localhost/jobs/create で求人登録"
echo "  3. 完了画面のトークンで http://localhost/lp/{token} を確認"
echo ""
echo "【SEOフィールド名の確認】"
echo "  ./vendor/bin/sail artisan tinker"
echo "  >>> App\Models\Job::first()->toArray();"
echo "  ※ seo_description/seo_body が実際のカラム名と違う場合は"
echo "    resources/views/lp/show.blade.php を修正してください"
