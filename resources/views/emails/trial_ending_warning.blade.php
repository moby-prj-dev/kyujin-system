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
        <h1>モニター無料期間終了のお知らせ</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <div class="alert-box">
            <strong>モニター無料期間が3日後に終了します。</strong><br>
            <span style="font-size:0.9rem;">無料期間終了日：{{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }}</span>
        </div>

        <p>Care Entry（ケアエントリー）をモニターとしてご利用いただきありがとうございます。</p>

        <p>
            {{ \Carbon\Carbon::parse($trialEndsAt)->format('Y年m月d日') }} をもちまして、モニター無料期間が終了します。<br>
            終了後は、有効応募1件につき <strong>3,000円（税別）</strong> が発生する成果報酬型に移行します。
        </p>

        <p>
            引き続き掲載をご希望の場合は、管理ページより求人内容をご確認ください。<br>
            掲載を停止される場合は、管理ページより「掲載を停止する」をお選びください。
        </p>

        <a href="{{ url('/') }}" class="btn">Care Entry 管理ページ</a>

        <p style="margin-top:24px; font-size:0.88rem; color:#666;">
            ご不明な点がございましたら、下記までお問い合わせください。<br>
            サービス名：Care Entry（ケアエントリー）<br>
            運営：沖縄デジタルワークス 代表者 岸本 安史<br>
            メール：<a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a><br>
            電話：070-6401-9492（平日10:00〜18:00）
        </p>
    </div>
    <div class="footer">
        Care Entry（ケアエントリー）｜介護・福祉専門求人サービス【対応エリア:沖縄県】
    </div>
</div>
</body>
</html>
