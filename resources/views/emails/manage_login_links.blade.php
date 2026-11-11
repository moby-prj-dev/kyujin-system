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
        .job { border: 1px solid #e5e9f0; border-radius: 6px; padding: 14px 16px; margin: 12px 0; }
        .job .t { font-weight: 700; margin: 0 0 4px; }
        .job .s { font-size: 0.8rem; color: #666; margin: 0 0 10px; }
        .btn { display: inline-block; background: #1a73e8; color: #fff; text-decoration: none; padding: 10px 24px; border-radius: 6px; font-weight: 700; font-size: 0.9rem; }
        .note { font-size: 0.82rem; color: #888; border-top: 1px solid #eee; margin-top: 24px; padding-top: 16px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>管理ページへのログインリンク</h1>
    </div>
    <div class="body">
        <p>{{ $jobs->first()->company_name }} 様</p>
        <p>ご登録の求人の管理ページへのリンクをお送りします。<br>
        各ボタンから、求人の確認・編集・停止・応募の確認が行えます。</p>

        @foreach($jobs as $job)
            @php
                $statusLabel = ['active' => '公開中', 'paused' => '一時停止中', 'closed' => '掲載停止中', 'expired' => '掲載期限切れ'][$job->status] ?? $job->status;
            @endphp
            <div class="job">
                <p class="t">{{ $job->displayTitle() }}</p>
                <p class="s">状態：{{ $statusLabel }}</p>
                <a href="{{ route('jobs.manage', $job->token) }}" class="btn">管理ページを開く</a>
            </div>
        @endforeach

        <p style="margin-top:20px;"><strong>管理ページはブラウザのブックマーク（お気に入り）に登録しておくと便利です。</strong><br>
        リンクが分からなくなったときは、サイト下部の「掲載企業ログイン」からいつでも再送できます。</p>

        <p class="note">
            このメールに心当たりがない場合は、破棄してください（管理ページには、このメールのリンクからしか入れません）。<br>
            ご不明な点は、このメールにご返信いただくか careentry.info@gmail.com までご連絡ください。<br>
            Care Entry（ケアエントリー） https://care-entry.net
        </p>
    </div>
</div>
</body>
</html>
