<!DOCTYPE html>
<html lang="ja">
<head>
@if(app()->environment('production'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINE応募｜{{ $job->seo_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        body{background:#f5f7fa;font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;}
        .liff-wrap{max-width:480px;margin:0 auto;padding:1.5rem 1.25rem 3rem;}
        .liff-header{text-align:center;margin-bottom:1.5rem;}
        .line-logo{width:48px;height:48px;background:#06C755;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:0.5rem;}
        .liff-header h1{font-size:1.1rem;font-weight:800;margin:0;}
        .job-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.07);padding:1.25rem;margin-bottom:1rem;}
        .job-title{font-size:1.05rem;font-weight:800;color:#1a1a2e;margin:0 0 4px;line-height:1.4;}
        .job-company{font-size:0.85rem;color:#666;margin:0 0 12px;}
        .job-section{margin-top:12px;padding-top:12px;border-top:1px solid #eef1f5;}
        .job-section-title{font-size:0.75rem;font-weight:800;color:#888;margin-bottom:6px;letter-spacing:.04em;}
        .job-badges{display:flex;flex-wrap:wrap;gap:6px;}
        .job-badge{background:#e8f0fe;color:#1967d2;font-size:0.78rem;font-weight:700;padding:3px 8px;border-radius:12px;}
        .job-badge-area{background:#e6f4ea;color:#137333;}
        .job-badge-emp{background:#fef7e0;color:#8a6d00;}
        .job-salary{font-size:1rem;font-weight:800;color:#e65100;}
        .job-text{font-size:0.85rem;color:#333;white-space:pre-wrap;line-height:1.7;}
        .form-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.07);padding:1.25rem;margin-bottom:1rem;}
        .form-label{font-weight:700;font-size:0.88rem;}
        .required-badge{font-size:0.7rem;background:#dc3545;color:#fff;border-radius:3px;padding:1px 5px;margin-left:6px;}
        .optional-badge{font-size:0.7rem;background:#888;color:#fff;border-radius:3px;padding:1px 5px;margin-left:6px;}
        .form-control{border:1.5px solid #ccc;border-radius:8px;}
        .form-control:focus{border-color:#06C755;box-shadow:0 0 0 3px rgba(6,199,85,0.15);}
        .line-profile{display:flex;align-items:center;gap:0.75rem;background:#e8faf0;border-radius:10px;padding:0.65rem 1rem;margin-bottom:1rem;font-size:0.85rem;}
        .line-avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;}
        .line-avatar-placeholder{width:36px;height:36px;border-radius:50%;background:#06C755;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;}
        .line-name{font-weight:700;font-size:0.9rem;}
        .line-verified{font-size:0.72rem;color:#137333;}
        .btn-line-primary{width:100%;background:#06C755;color:#fff;border:none;border-radius:30px;font-size:1rem;font-weight:800;padding:0.85rem;}
        .btn-line-primary:hover{background:#05a847;color:#fff;}
        .btn-line-primary:disabled{background:#ccc;}
        .btn-outline-secondary{width:100%;border:1.5px solid #ccc;background:#fff;color:#555;border-radius:30px;font-size:0.95rem;font-weight:700;padding:0.75rem;margin-top:8px;}
        .loading-overlay{display:none;position:fixed;inset:0;background:rgba(255,255,255,0.85);z-index:999;align-items:center;justify-content:center;flex-direction:column;gap:1rem;}
        .loading-overlay.show{display:flex;}
        #errorBox{display:none;}
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-success" role="status"></div>
    <p class="mb-0 text-muted">送信中...</p>
</div>

<div class="liff-wrap">
    <div class="liff-header">
        <div class="line-logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
        </div>
        <h1>LINEで応募</h1>
    </div>

    <div id="errorBox" class="alert alert-danger mb-3"></div>

    {{-- 求人情報カード(募集要項) --}}
    <div class="job-card">
        <p class="job-title">{{ $job->seo_title ?: $job->title }}</p>
        <p class="job-company"><i class="bi bi-building me-1"></i>{{ $job->company_name }}</p>

        @if($job->jobAreas->isNotEmpty() || $job->jobJobTypes->isNotEmpty() || $job->jobEmploymentTypes->isNotEmpty())
        <div class="job-section">
            <p class="job-section-title">募集要項</p>
            <div class="job-badges">
                @foreach($job->jobAreas->take(3) as $ja)
                    @if($ja->area)
                        <span class="job-badge job-badge-area"><i class="bi bi-geo-alt-fill"></i> {{ $ja->area->name }}</span>
                    @endif
                @endforeach
                @foreach($job->jobJobTypes->take(3) as $jt)
                    @if($jt->jobType)
                        <span class="job-badge">{{ $jt->jobType->name }}</span>
                    @endif
                @endforeach
                @foreach($job->jobEmploymentTypes->take(3) as $et)
                    @if($et->employmentType)
                        <span class="job-badge job-badge-emp">{{ $et->employmentType->name }}</span>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($job->salaryText())
        <div class="job-section">
            <p class="job-section-title">給与</p>
            <p class="job-salary mb-0">{{ $job->salaryText() }}</p>
            @if($job->salary_note)
                <p class="job-text mt-1" style="font-size:0.78rem;color:#666;">{{ $job->salary_note }}</p>
            @endif
        </div>
        @endif

        @if($job->free_text)
        <div class="job-section">
            <p class="job-section-title">仕事内容</p>
            <p class="job-text mb-0">{{ mb_strimwidth($job->free_text, 0, 240, '…') }}</p>
        </div>
        @endif
    </div>

    {{-- ステップ1: OK/NG 選択 --}}
    <div id="step1">
        <p class="text-center text-muted mb-3" style="font-size:0.85rem;">
            上記の求人にご応募されますか?
        </p>
        <button type="button" id="applyBtn" class="btn-line-primary">
            <i class="bi bi-check-circle-fill me-2"></i>この求人に応募する
        </button>
        <button type="button" id="declineBtn" class="btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>見送る(求人一覧へ戻る)
        </button>
    </div>

    {{-- ステップ2: 応募者情報入力 --}}
    <div id="step2" style="display:none;">
        <div class="form-card">
            <div id="profileBox" class="line-profile" style="display:none;">
                <img id="profileImg" class="line-avatar" src="" alt="">
                <div>
                    <div class="line-name" id="profileName"></div>
                    <div class="line-verified"><i class="bi bi-check-circle-fill me-1"></i>LINEログイン済み</div>
                </div>
            </div>
            <div id="profilePlaceholder" class="line-profile">
                <div class="line-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                <div>
                    <div class="line-name">LINEログイン中...</div>
                </div>
            </div>

            <form id="liffForm">
                @csrf
                <input type="hidden" id="lineUserId" name="line_user_id">
                <input type="hidden" id="lineSessionId" name="line_session_id">
                <input type="hidden" id="lineDisplayName" name="line_display_name">

                <div class="mb-3">
                    <label class="form-label">お名前<span class="required-badge">必須</span></label>
                    <input type="text" name="applicant_name" id="applicantName" class="form-control"
                           placeholder="山田 太郎" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">電話番号<span class="required-badge">必須</span></label>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           placeholder="09012345678" required>
                    <div class="form-text small">ハイフンなしで入力してください</div>
                </div>

                <div class="mb-0">
                    <label class="form-label">メールアドレス<span class="optional-badge">任意</span></label>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="you@example.com">
                    <div class="form-text small">ご入力いただくと応募控えを送信します</div>
                </div>
            </form>
        </div>

        <button type="button" id="submitBtn" class="btn-line-primary" disabled>
            <i class="bi bi-send-fill me-2"></i>この内容で応募する
        </button>

        <button type="button" id="backBtn" class="btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>戻る
        </button>
    </div>
</div>

<script>
const LIFF_ID    = '{{ $liffId }}';
const STORE_URL  = '{{ route('liff.apply.store', ['token' => $job->token]) }}';
const THANKS_URL = '{{ route('liff.thanks', ['token' => $job->token]) }}';
const LP_URL     = '{{ route('lp.show', ['token' => $job->token]) }}';

async function initLiff() {
    try {
        await liff.init({ liffId: LIFF_ID });

        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: location.href });
            return;
        }

        const profile = await liff.getProfile();
        const context = liff.getContext();

        document.getElementById('lineUserId').value      = profile.userId;
        document.getElementById('lineDisplayName').value = profile.displayName;
        if (context?.utouId || context?.roomId || context?.groupId) {
            document.getElementById('lineSessionId').value =
                context.utouId ?? context.roomId ?? context.groupId ?? '';
        }

        document.getElementById('profilePlaceholder').style.display = 'none';
        const profileBox = document.getElementById('profileBox');
        profileBox.style.removeProperty('display');
        document.getElementById('profileName').textContent = profile.displayName;
        if (profile.pictureUrl) {
            document.getElementById('profileImg').src = profile.pictureUrl;
        }
        document.getElementById('applicantName').value = profile.displayName;
        document.getElementById('submitBtn').disabled = false;

    } catch (e) {
        showError('LINEログインに失敗しました。ブラウザを閉じて再度お試しください。');
        console.error(e);
    }
}

document.getElementById('applyBtn').addEventListener('click', () => {
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    initLiff();
});

document.getElementById('declineBtn').addEventListener('click', () => {
    if (liff.isInClient()) {
        liff.closeWindow();
    } else {
        location.href = LP_URL;
    }
});

document.getElementById('backBtn').addEventListener('click', () => {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
});

document.getElementById('submitBtn').addEventListener('click', async () => {
    const name  = document.getElementById('applicantName').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!name)  { showError('お名前を入力してください。'); return; }
    if (!/^[0-9]{10,11}$/.test(phone)) { showError('電話番号はハイフンなしの数字10〜11桁で入力してください。'); return; }
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('メールアドレスの形式が正しくありません。'); return; }

    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loadingOverlay').classList.add('show');
    hideError();

    const form = document.getElementById('liffForm');
    const body = new URLSearchParams(new FormData(form)).toString();

    try {
        const res = await fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body,
        });
        const json = await res.json();
        if (json.redirect) {
            location.href = json.redirect;
        } else {
            showError('送信に失敗しました。もう一度お試しください。');
            document.getElementById('submitBtn').disabled = false;
        }
    } catch (e) {
        showError('通信エラーが発生しました。もう一度お試しください。');
        document.getElementById('submitBtn').disabled = false;
    } finally {
        document.getElementById('loadingOverlay').classList.remove('show');
    }
});

function showError(msg) {
    const box = document.getElementById('errorBox');
    box.textContent = msg;
    box.style.display = 'block';
}
function hideError() {
    document.getElementById('errorBox').style.display = 'none';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
