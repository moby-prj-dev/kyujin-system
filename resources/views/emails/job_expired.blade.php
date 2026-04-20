<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><style>
body{font-family:'Hiragino Kaku Gothic ProN',sans-serif;background:#f5f7fa;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:linear-gradient(135deg,#555,#333);color:#fff;padding:28px 32px;}
.header h1{margin:0;font-size:1.2rem;}
.body{padding:28px 32px;}
.info-box{background:#f5f5f5;border-radius:8px;padding:16px 20px;margin:16px 0;font-size:0.93rem;}
.btn{display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;margin-top:8px;}
.footer{background:#f5f7fa;padding:16px 32px;font-size:0.82rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>📋 求人掲載期間が終了しました</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <p>
            Care Entry（ケア・エントリー）をご利用いただきありがとうございます。<br>
            以下の求人の掲載期間が終了しました。
        </p>

        <div class="info-box">
            <strong>求人タイトル：</strong>{{ $jobTitle }}
        </div>

        <p>
            引き続き求人を掲載される場合は、新たに求人登録をお願いいたします。<br>
            なお、再掲載後の応募は<strong>成果報酬（1件あたり¥3,000・税別）</strong>の対象となります。
        </p>

        <a href="{{ url('/jobs/create') }}" class="btn">新しく求人を登録する</a>

        <p style="margin-top:24px;font-size:0.88rem;color:#666;">
            サービス名：Care Entry（ケア・エントリー）<br>
            運営者名：岸本　安史<br>
            メール：<a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a><br>
            電話：070-6401-9492（平日10:00〜18:00）
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Care Entry. All rights reserved.
    </div>
</div>
</body>
</html>
