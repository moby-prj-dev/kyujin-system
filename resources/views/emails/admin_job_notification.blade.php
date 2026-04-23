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
        .table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .table th { text-align: left; padding: 8px 12px; background: #f0f4ff; font-size: 0.85rem; color: #555; width: 35%; }
        .table td { padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .btn { display: inline-block; margin: 24px 0 8px; background: #1a73e8; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-weight: 700; font-size: 0.95rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>新規求人が掲載されました</h1>
    </div>
    <div class="body">
        <p>以下の求人が新規掲載されました。</p>

        <table class="table">
            <tr><th>会社名</th><td>{{ $job->company_name }}</td></tr>
            <tr><th>求人タイトル</th><td>{{ $job->title }}</td></tr>
            <tr><th>連絡先メール</th><td>{{ $job->contact_email }}</td></tr>
            <tr><th>連絡先電話</th><td>{{ $job->contact_phone ?? '未記入' }}</td></tr>
            <tr><th>掲載開始</th><td>{{ now()->format('Y年m月d日 H:i') }}</td></tr>
            <tr><th>掲載期限</th><td>{{ now()->addMonths(3)->format('Y年m月d日') }}</td></tr>
        </table>

        <a href="{{ url('/admin/jobs') }}" class="btn">管理画面で確認する</a>
    </div>
</div>
</body>
</html>
