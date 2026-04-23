<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><style>
body{font-family:'Hiragino Kaku Gothic ProN',sans-serif;background:#f5f7fa;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:28px 32px;}
.header h1{margin:0;font-size:1.2rem;}
.body{padding:28px 32px;}
.alert-box{background:#fff8e1;border:1.5px solid #f59e0b;border-radius:8px;padding:16px 20px;margin-bottom:20px;}
.btn{display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;margin-top:8px;}
.footer{background:#f5f7fa;padding:16px 32px;font-size:0.82rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>⚠️ 求人掲載期限のお知らせ</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <div class="alert-box">
            <strong>現在掲載中の求人の掲載期限が、7日以内に終了します。</strong><br>
            <span style="font-size:0.9rem;">掲載期限：{{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }}</span>
        </div>

        <p>Care Entry（ケアエントリー）をご利用いただきありがとうございます。</p>

        <p>
            現在掲載中の求人の掲載期限が <strong>{{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }}</strong> に終了します。<br>
            引き続き掲載を続けるには、管理ページより更新手続きをお願いします。
        </p>

        <p>
            掲載を停止される場合は、そのまま期限をお迎えください。
        </p>

        <a href="{{ url('/') }}" class="btn">Care Entry 管理ページ</a>
    </div>
    <div class="footer">
        Care Entry（ケアエントリー）｜沖縄の介護・福祉専門求人サービス
    </div>
</div>
</body>
</html>
