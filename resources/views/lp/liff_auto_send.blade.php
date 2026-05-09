<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
const LIFF_ID     = @json($liffId);
const APPLY_TEXT  = 'apply:' + @json($entryToken->token);
const FALLBACK_URL = @json($fallbackUrl);

function showError(msg) {
    document.getElementById('msg').textContent = msg;
    document.querySelector('.spinner').style.display = 'none';
    document.getElementById('fallbackLink').style.display = 'inline-block';
}

async function isPermissionGranted() {
    try {
        const perm = await liff.permission.query('chat_message.write');
        return !!(perm && perm.state === 'granted');
    } catch (e) {
        return false;
    }
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
        if (!liff.isInClient()) {
            location.href = FALLBACK_URL;
            return;
        }

        let granted = await isPermissionGranted();

        if (!granted) {
            // 1段目: requestAll で同意ダイアログ
            try {
                await liff.permission.requestAll();
                granted = await isPermissionGranted();
            } catch (e) {
                // 環境によっては失敗するので2段目に進む
            }
        }

        if (!granted) {
            // 2段目: ログアウト→再ログインで OAuth 同意フローを強制発動
            const params = new URLSearchParams(location.search);
            if (!params.has('reauth')) {
                params.set('reauth', '1');
                const sep = location.pathname + '?' + params.toString();
                document.getElementById('msg').textContent = '権限を再取得しています...';
                liff.logout();
                liff.login({ redirectUri: location.origin + sep });
                return;
            }
            // 再ログイン後もダメ → フォールバック(手動送信)
            location.href = FALLBACK_URL;
            return;
        }

        document.getElementById('msg').textContent = '応募情報を送信中...';
        await liff.sendMessages([{ type: 'text', text: APPLY_TEXT }]);
        liff.closeWindow();
    } catch (e) {
        console.error(e);
        showError('自動送信に失敗しました。下のボタンからLINEで送信してください。');
        document.getElementById('errDetail').textContent = (e && e.message) ? e.message : '';
    }
})();
</script>
</body>
</html>
