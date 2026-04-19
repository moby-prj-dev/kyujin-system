<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募完了｜{{ $job->seo_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body{background-color:#f5f7fa;color:#333;font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;}
        .thanks-wrap{max-width:640px;margin:0 auto;padding:3rem 1.5rem;}
        .thanks-icon{font-size:4rem;color:#1a73e8;margin-bottom:1rem;}
        .thanks-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.08);padding:2.5rem 2rem;text-align:center;}
        .thanks-title{font-size:1.4rem;font-weight:800;margin-bottom:0.75rem;}
        .thanks-body{font-size:0.93rem;color:#555;line-height:1.8;}
        .btn-back{display:inline-flex;align-items:center;gap:0.5rem;margin-top:2rem;background:#1a73e8;color:#fff;border:none;border-radius:30px;font-size:0.95rem;font-weight:700;padding:0.7rem 2rem;text-decoration:none;}
        .btn-back:hover{background:#1558b0;color:#fff;}
    </style>
</head>
<body>

<div class="thanks-wrap">
    <div class="thanks-card">
        <div class="thanks-icon"><i class="bi bi-check-circle-fill"></i></div>
        <h1 class="thanks-title">応募が完了しました</h1>
        <p class="thanks-body">
            ご応募ありがとうございます。<br>
            担当者より折り返しご連絡いたします。<br>
            しばらくお待ちください。
        </p>
        <a href="{{ route('lp.show', ['token' => $job->token]) }}" class="btn-back">
            <i class="bi bi-arrow-left"></i>求人詳細に戻る
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
