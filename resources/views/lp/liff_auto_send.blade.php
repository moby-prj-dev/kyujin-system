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
            background: #f5f7fa; padding: 4em 1em; text-align: center; color: #2d2d2d;
        }
        .spinner {
            display: inline-block; width: 48px; height: 48px;
            border: 4px solid #06C755; border-top-color: transparent;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .message { margin-top: 20px; font-size: 0.95rem; color: #444; }
        .error-detail { margin-top: 12px; font-size: 0.8rem; color: #888; }
        .fallback-link {
            display: inline-block; margin-top: 24px; padding: 12px 28px;
            background: #06C755; color: #fff; border-radius: 30px;
            text-decoration: none; font-weight: 700; font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="spinner"></div>
    <p class="message" id="msg">LINEに接続しています...</p>
    <p class="error-detail" id="errDetail"></p>
    <a class="fallback-link" id="fallbackLink" href="{{ $fallbackUrl }}" style="display:none;">LINEで送信する</a>

<script>
const LIFF_ID      = @json($liffId);
const START_URL    = @json(route('liff.auto_send.start', ['token' => $entryToken->token]));
const FALLBACK_URL = @json($fallbackUrl);
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').content;

function showError(msg, detail) {
    document.getElementById('msg').textContent = msg;
    document.querySelector('.spinner').style.display = 'none';
    document.getElementById('fallbackLink').style.display = 'inline-block';
    if (detail) document.getElementById('errDetail').textContent = detail;
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

            // 友だち未追加でPushが失敗した場合は、手動送信URLにフォールバック
            // (oaMessage URLでチャットに apply:xxx が事前入力されるので、ユーザーは送信ボタンを1回タップするだけ)
            if (code === 'push_failed') {
                location.href = FALLBACK_URL;
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
