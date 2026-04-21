<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><style>
body{font-family:'Hiragino Kaku Gothic ProN',sans-serif;background:#f5f7fa;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:linear-gradient(135deg,#1a73e8,#0d47a1);color:#fff;padding:28px 32px;}
.header h1{margin:0;font-size:1.2rem;}
.body{padding:28px 32px;}
.info-box{background:#f0f7ff;border-radius:8px;padding:16px 20px;margin:16px 0;font-size:0.93rem;border-left:4px solid #1a73e8;}
.info-box table{width:100%;border-collapse:collapse;}
.info-box td{padding:4px 0;vertical-align:top;}
.info-box td:first-child{color:#555;width:120px;}
.btn{display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;margin-top:8px;}
.footer{background:#f5f7fa;padding:16px 32px;font-size:0.82rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>🔔 掲載継続通知</h1>
    </div>
    <div class="body">
        <p>管理者様</p>

        <p>以下の求人について、企業より掲載継続の意思確認が行われました。</p>

        <div class="info-box">
            <table>
                <tr><td>会社名</td><td><strong>{{ $job->company_name }}</strong></td></tr>
                <tr><td>求人タイトル</td><td>{{ $job->title }}</td></tr>
                <tr><td>求人ID</td><td>#{{ $job->id }}</td></tr>
                <tr><td>継続日時</td><td>{{ $job->continued_at?->format('Y/m/d H:i') }}</td></tr>
                <tr><td>メール</td><td>{{ $job->contact_email }}</td></tr>
            </table>
        </div>

        <p style="margin-top:16px;">
            <a href="{{ url('/lp/' . $job->token) }}" class="btn">公開URLを確認</a>
            &nbsp;
            <a href="{{ url('/admin/jobs') }}" class="btn" style="background:#555;">管理画面へ</a>
        </p>

        <p style="margin-top:24px;font-size:0.88rem;color:#666;">
            このメールは Care Entry 管理者通知として自動送信されています。
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Care Entry. All rights reserved.
    </div>
</div>
</body>
</html>
