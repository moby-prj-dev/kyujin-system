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
        .liff-wrap{max-width:480px;margin:0 auto;padding:2rem 1.25rem 3rem;}
        .liff-header{text-align:center;margin-bottom:2rem;}
        .line-logo{width:56px;height:56px;background:#06C755;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;}
        .liff-header h1{font-size:1.2rem;font-weight:800;}
        .liff-header .job-name{font-size:0.85rem;color:#666;margin-top:4px;}
        .form-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.07);padding:1.5rem;margin-bottom:1rem;}
        .form-label{font-weight:700;font-size:0.9rem;}
        .required-badge{font-size:0.72rem;background:#dc3545;color:#fff;border-radius:3px;padding:1px 5px;margin-left:6px;}
        .form-control{border:1.5px solid #ccc;border-radius:8px;}
        .form-control:focus{border-color:#06C755;box-shadow:0 0 0 3px rgba(6,199,85,0.15);}
        .line-profile{display:flex;align-items:center;gap:0.75rem;background:#e8faf0;border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;}
        .line-avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;}
        .line-avatar-placeholder{width:44px;height:44px;border-radius:50%;background:#06C755;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;flex-shrink:0;}
        .line-name{font-weight:700;font-size:0.95rem;}
        .line-verified{font-size:0.78rem;color:#137333;}
        .btn-line-submit{width:100%;background:#06C755;color:#fff;border:none;border-radius:30px;font-size:1rem;font-weight:800;padding:0.85rem;margin-top:0.5rem;}
        .btn-line-submit:hover{background:#05a847;color:#fff;}
        .btn-line-submit:disabled{background:#ccc;}
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
    <p class="mb-0 text-muted">LINEログイン中...</p>
</div>

<div class="liff-wrap">
    <div class="liff-header">
        <div class="line-logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
        </div>
        <h1>LINEで応募する</h1>
        <p class="job-name">{{ $job->seo_title }}</p>
    </div>

    <div id="errorBox" class="alert alert-danger mb-3"></div>

    <div class="form-card">
        <div id="profileBox" class="line-profile" style="display:none!important">
            <img id="profileImg" class="line-avatar" src="" alt="">
            <div>
                <div class="line-name" id="profileName"></div>
                <div class="line-verified"><i class="bi bi-check-circle-fill me-1"></i>LINEログイン済み</div>
            </div>
        </div>
        <div id="profilePlaceholder" class="line-profile">
            <div class="line-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <div>
                <div class="line-name">LINEプロフィール取得中...</div>
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

            <div class="mb-0">
                <label class="form-label">電話番号<span class="required-badge">必須</span></label>
                <input type="tel" name="phone" id="phone" class="form-control"
                       placeholder="09012345678" required>
                <div class="form-text">ハイフンなしで入力してください</div>
            </div>
        </form>
    </div>

    <button type="button" id="submitBtn" class="btn-line-submit" disabled>
        <i class="bi bi-send-fill me-2"></i>この内容で応募する
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('lp.show', ['token' => $job->token]) }}"
           style="color:#888;font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>求人詳細に戻る
        </a>
    </div>
</div>

<script>
const LIFF_ID    = '{{ $liffId }}';
const STORE_URL  = '{{ route('liff.apply.store', ['token' => $job->token]) }}';
const THANKS_URL = '{{ route('liff.thanks', ['token' => $job->token]) }}';

async function initLiff() {
    try {
        await liff.init({ liffId: LIFF_ID });

        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: location.href });
            return;
        }

        const profile = await liff.getProfile();
        const context = liff.getContext();

        document.getElementById('lineUserId').value   = profile.userId;
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

document.getElementById('submitBtn').addEventListener('click', async () => {
    const name  = document.getElementById('applicantName').value.trim();
    const phone = document.getElementById('phone').value.trim();

    if (!name)  { showError('お名前を入力してください。'); return; }
    if (!/^[0-9]{10,11}$/.test(phone)) { showError('電話番号はハイフンなしの数字10〜11桁で入力してください。'); return; }

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

initLiff();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
