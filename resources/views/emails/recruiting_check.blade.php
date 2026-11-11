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
        .stats { background: #f5f7fa; border-radius: 6px; padding: 12px 16px; margin: 16px 0; }
        .btn { display: inline-block; margin: 20px 0; background: #1a73e8; color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-weight: 700; font-size: 1rem; }
        .note { font-size: 0.82rem; color: #888; border-top: 1px solid #eee; margin-top: 24px; padding-top: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>掲載中の求人の募集状況のご確認</h1>
    </div>
    <div class="body">
        <p>{{ $job->company_name }} 様</p>
        <p>いつもCare Entry（ケアエントリー）をご利用いただきありがとうございます。<br>
        下記の求人は、現在も掲載を続けています。</p>

        <p><strong>{{ $job->displayTitle() }}</strong></p>

        <div class="stats">
            直近3か月の求人ページの閲覧：<strong>{{ number_format($views) }}回</strong><br>
            直近3か月の応募：<strong>{{ number_format($applications) }}件</strong>
        </div>

        <p><strong>引き続き募集中の場合は、お手続きは不要です。</strong>このまま掲載を続けます。</p>
        <p>すでに募集を終えている場合は、応募が届き続けないよう、管理ページの「掲載停止」ボタンから停止してください。
        募集内容（給与・勤務条件など）が変わった場合も、管理ページから編集できます。</p>

        <a href="{{ route('jobs.manage', $job->manage_token) }}" class="btn">管理ページを開く</a>

        <p class="note">
            このメールは、掲載中の求人について3か月ごとにお送りしています。<br>
            ご不明な点は、このメールにご返信いただくか careentry.info@gmail.com までご連絡ください。<br>
            Care Entry（ケアエントリー） https://care-entry.net
        </p>
    </div>
</div>
</body>
</html>
