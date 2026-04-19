<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Hiragino Sans', 'Meiryo', sans-serif; background: #f5f7fa; margin: 0; padding: 20px; }
        .wrap { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg,#1a73e8,#0d47a1); color: #fff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .body { padding: 32px; color: #333; font-size: 0.95rem; line-height: 1.8; }
        .btn { display: inline-block; margin: 24px 0; background: #1a73e8; color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-weight: 700; font-size: 1rem; }
        .note { font-size: 0.82rem; color: #888; border-top: 1px solid #eee; margin-top: 24px; padding-top: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>求人編集用リンク</h1>
    </div>
    <div class="body">
        <p>{{ $job->company_name }} 様</p>
        <p>ご登録済みの求人（{{ $job->title }}）の編集用リンクをお送りします。<br>
        以下のボタンから求人の確認・編集が行えます。</p>

        <a href="{{ route('jobs.manage', $job->token) }}" class="btn">
            求人を確認・編集する
        </a>

        <p>ボタンが機能しない場合は以下のURLをブラウザに貼り付けてください：<br>
        <small>{{ route('jobs.manage', $job->token) }}</small></p>

        <div class="note">
            <p>このメールに心当たりがない場合は無視してください。<br>
            リンクの有効期限はありません。URLは第三者と共有しないでください。</p>
        </div>
    </div>
</div>
</body>
</html>
