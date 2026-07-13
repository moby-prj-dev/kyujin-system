<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Hiragino Sans', 'Meiryo', sans-serif; background: #f5f7fa; margin: 0; padding: 20px; }
        .wrap { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg,#f59e0b,#d97706); color: #fff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .body { padding: 32px; color: #333; font-size: 0.95rem; line-height: 1.8; }
        .badge { display: inline-block; background: #fff8e1; color: #b45309; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; margin-bottom: 12px; }
        .table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .table th { text-align: left; padding: 8px 12px; background: #fff8e1; font-size: 0.85rem; color: #555; width: 35%; }
        .table td { padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .btn { display: inline-block; margin: 24px 0 8px; background: #f59e0b; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-weight: 700; font-size: 0.95rem; }
        .note { background: #f0f7ff; border-left: 4px solid #1a73e8; padding: 12px 16px; margin: 16px 0; font-size: 0.85rem; color: #333; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>新規申込がありました(メール認証待ち)</h1>
    </div>
    <div class="body">
        <span class="badge">⏳ 認証待ち・仮登録</span>
        <p>掲載主が求人掲載申込フォームを送信しました。掲載主のメール認証が完了すると本掲載されます。</p>

        <table class="table">
            <tr><th>会社名</th><td>{{ $job->company_name }}</td></tr>
            <tr><th>求人タイトル</th><td>{{ $job->title }}</td></tr>
            <tr><th>プラン</th><td>{{ $job->plan === 'standard' ? 'スタンダード(月3,000円)' : 'ベーシック(月額無料)' }}</td></tr>
            <tr><th>連絡先メール</th><td>{{ $job->contact_email }}</td></tr>
            <tr><th>連絡先電話</th><td>{{ $job->contact_phone ?? '未記入' }}</td></tr>
            <tr><th>申込日時</th><td>{{ now()->format('Y年m月d日 H:i') }}</td></tr>
        </table>

        <div class="note">
            💡 掲載主が認証URLをクリックすると、続いて「新規求人が掲載されました」の通知メールが届きます。<br>
            もし数日経っても認証されない場合は、フォローの連絡をご検討ください。
        </div>

        <a href="{{ url('/admin/jobs') }}" class="btn">管理画面で確認する</a>
    </div>
</div>
</body>
</html>
