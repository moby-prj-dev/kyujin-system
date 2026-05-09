<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LINE応募｜送信中</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Meiryo', sans-serif;
            background: #f5f7fa; padding: 3em 1em; color: #2d2d2d;
            margin: 0; min-height: 100vh; box-sizing: border-box;
        }
        .center { text-align: center; }
        .spinner {
            display: inline-block; width: 48px; height: 48px;
            border: 4px solid #06C755; border-top-color: transparent;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .message { margin-top: 20px; font-size: 0.95rem; color: #444; }
        .error-detail { margin-top: 12px; font-size: 0.8rem; color: #888; }
        #manualGuide { display: none; max-width: 480px; margin: 0 auto; }
        .guide-card {
            background: #fff; border-radius: 14px; padding: 1.5em 1.25em;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06); text-align: left;
        }
        .guide-card h2 {
            font-size: 1.1rem; font-weight: 800; color: #06C755;
            margin: 0 0 0.6em; display: flex; align-items: center; gap: 0.35em;
        }
        .guide-card h2 svg { width: 22px; height: 22px; }
        .guide-step {
            display: flex; gap: 0.7em; margin: 0.8em 0; align-items: flex-start;
            font-size: 0.9rem; line-height: 1.5;
        }
        .step-num {
            flex-shrink: 0; width: 26px; height: 26px; background: #06C755;
            color: #fff; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 0.82rem; font-weight: 800;
        }
        .send-icon-demo {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; background: #06C755; border-radius: 50%;
            color: #fff; font-size: 0.85rem; vertical-align: -7px; margin: 0 2px;
        }
        .start-btn {
            display: block; width: 100%; margin-top: 1.2em; padding: 14px;
            background: #06C755; color: #fff; border: none; border-radius: 30px;
            text-decoration: none; font-weight: 800; font-size: 1rem; text-align: center;
        }
        .start-btn:hover { background: #05a847; }
    </style>
</head>
<body>
    <div class="center" id="loading">
        <div class="spinner"></div>
        <p class="message" id="msg">LINEに接続しています...</p>
        <p class="error-detail" id="errDetail"></p>
    </div>

    <div id="manualGuide">
        <div class="guide-card">
            <h2>
                <svg viewBox="0 0 24 24" fill="#06C755"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
                LINEで応募を開始するには
            </h2>
            <div class="guide-step">
                <div class="step-num">1</div>
                <div>下の<strong>「LINEのトーク画面を開く」</strong>をタップ</div>
            </div>
            <div class="guide-step">
                <div class="step-num">2</div>
                <div>トーク画面の入力欄に <code>apply:...</code> が自動で入力されます</div>
            </div>
            <div class="guide-step">
                <div class="step-num">3</div>
                <div>
                    <strong>送信ボタン<span class="send-icon-demo">▶</span>をタップ</strong>すると応募が始まります
                </div>
            </div>
            <a class="start-btn" id="fallbackLink" href="{{ $fallbackUrl }}">LINEのトーク画面を開く</a>
            <p style="margin-top:1em;font-size:0.78rem;color:#888;text-align:center;">
                送信後、ボットから希望条件の確認メッセージが届きます
            </p>
        </div>
    </div>

<script>
const LIFF_ID      = @json($liffId);
const START_URL    = @json(route('liff.auto_send.start', ['token' => $entryToken->token]));
const FALLBACK_URL = @json($fallbackUrl);
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').content;

function showError(msg, detail) {
    document.getElementById('msg').textContent = msg;
    document.querySelector('.spinner').style.display = 'none';
    showManualGuide();
    if (detail) document.getElementById('errDetail').textContent = detail;
}

function showManualGuide() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('manualGuide').style.display = 'block';
}

(async () => {
    if (!LIFF_ID) {
        location.href = FALLBACK_URL;
        return;
    }
    try {
        await liff.init({ liffId: LIFF_ID });
        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: location.href });
            return;
        }

        document.getElementById('msg').textContent = '応募情報を準備しています...';
        const profile = await liff.getProfile();

        const res = await fetch(START_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                line_user_id:      profile.userId,
                line_display_name: profile.displayName,
            }),
        });

        if (!res.ok) {
            let msg = '応募開始に失敗しました。';
            let detail = 'HTTP ' + res.status;
            let code = '';
            try {
                const j = await res.json();
                if (j && j.message) msg = j.message;
                if (j && j.error)   { detail = j.error; code = j.error; }
            } catch (_) {}

            // 友だち未追加でPushが失敗した場合は、手動送信を案内
            if (code === 'push_failed') {
                showManualGuide();
                return;
            }

            showError(msg, detail);
            return;
        }

        if (liff.isInClient()) {
            liff.closeWindow();
        } else {
            location.href = FALLBACK_URL;
        }
    } catch (e) {
        console.error(e);
        showError('LINE連携に失敗しました。下のボタンからLINEで送信してください。', (e && e.message) ? e.message : '');
    }
})();
</script>
</body>
</html>
