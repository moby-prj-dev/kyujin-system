<!DOCTYPE html>
<html lang="ja">
<head>
@if(app()->environment('production'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募完了｜{{ $job->seo_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        body{background:#f5f7fa;font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;}
        .thanks-wrap{max-width:480px;margin:0 auto;padding:3rem 1.5rem;}
        .thanks-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.08);padding:2.5rem 2rem;text-align:center;}
        .thanks-icon{font-size:4rem;color:#06C755;margin-bottom:1rem;}
        .thanks-title{font-size:1.3rem;font-weight:800;margin-bottom:0.75rem;}
        .thanks-body{font-size:0.92rem;color:#555;line-height:1.85;}
        .btn-close-liff{display:inline-flex;align-items:center;gap:0.5rem;margin-top:2rem;background:#06C755;color:#fff;border:none;border-radius:30px;font-size:0.95rem;font-weight:700;padding:0.7rem 2rem;cursor:pointer;}
        .btn-close-liff:hover{background:#05a847;}
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<div class="thanks-wrap">
    <div class="thanks-card">
        <div class="thanks-icon"><i class="bi bi-check-circle-fill"></i></div>
        <h1 class="thanks-title">応募が完了しました</h1>
        <p class="thanks-body">
            LINEでのご応募ありがとうございます。<br>
            担当者より折り返しご連絡いたします。<br>
            しばらくお待ちください。
        </p>
        <button class="btn-close-liff" onclick="closeLiff()">
            <i class="bi bi-x-lg"></i>閉じる
        </button>
    </div>
</div>

<script>
async function closeLiff() {
    try {
        await liff.init({ liffId: '{{ config('line.liff_id') }}' });
        liff.closeWindow();
    } catch {
        window.close();
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
