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
        <h1>⚠️ 無料トライアル期間終了のお知らせ</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <div class="alert-box">
            <strong>Care Entry の無料トライアル期間が、7日以内に終了します。</strong><br>
            <span style="font-size:0.9rem;">終了日：{{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }}</span>
        </div>

        <p>Care Entry（ケア・エントリー）をご利用いただきありがとうございます。</p>

        <p>
            現在ご利用中の無料トライアル期間が <strong>{{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }}</strong> に終了します。<br>
            トライアル終了後も引き続き求人掲載を続ける場合、以降に発生した有効応募は<strong>成果報酬（1件あたり3,000円）</strong>の対象となります。
        </p>

        <p>
            継続のお手続きは不要です。トライアル終了後も自動的に求人掲載は継続されます。<br>
            掲載を停止される場合は、管理ページより操作をお願いします。
        </p>

        <a href="{{ url('/') }}" class="btn">Care Entry 管理ページ</a>

        <p style="margin-top:24px;font-size:0.88rem;color:#666;">
            ご不明な点がございましたら、下記までお問い合わせください。<br>
            Care Entry 運営事務局：<a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a>
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Care Entry. All rights reserved.
    </div>
</div>
</body>
</html>
