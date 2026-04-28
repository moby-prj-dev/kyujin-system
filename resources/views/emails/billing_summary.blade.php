<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><style>
body{font-family:'Hiragino Kaku Gothic ProN',sans-serif;background:#f5f7fa;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:linear-gradient(135deg,#1a73e8,#0d47a1);color:#fff;padding:28px 32px;}
.header h1{margin:0;font-size:1.2rem;}
.body{padding:28px 32px;}
.info-box{background:#f0f7ff;border:1.5px solid #d6e4f7;border-radius:8px;padding:16px 20px;margin:16px 0;font-size:0.93rem;line-height:2;}
.bank-box{background:#f9f9f9;border:1.5px solid #ddd;border-radius:8px;padding:16px 20px;margin:16px 0;font-size:0.93rem;line-height:2;}
.note{font-size:0.85rem;color:#666;margin:8px 0;}
.footer{background:#f5f7fa;padding:16px 32px;font-size:0.82rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>📋 {{ $summary->billing_month }} ご請求書のご案内</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <p>
            Care Entry（ケア・エントリー）をご利用いただきありがとうございます。<br>
            {{ $summary->billing_month }} の成果報酬についてご請求申し上げます。
        </p>

        <div class="info-box">
            請求月：{{ $summary->billing_month }}<br>
            有効応募件数：{{ $summary->valid_count }} 件<br>
            請求対象件数：{{ $summary->billable_count }} 件<br>
            単価：¥{{ number_format($summary->unit_price) }}<br>
            <strong>合計金額（税別）：¥{{ number_format($summary->total_amount) }}</strong>
        </div>

        <p class="note">※有効応募とは、連絡可能な応募（氏名・連絡先あり）を指します。</p>

        <p><strong>【お振込先】</strong></p>
        <div class="bank-box">
            銀行名：住信SBIネット銀行<br>
            支店名：バナナ支店<br>
            口座番号：9466315<br>
            口座名義：キシモト　ヤスシ
        </div>

        <p class="note">※本サービスは個人事業として運営しており、上記名義でのお振込みをお願いいたします。</p>

        <p>請求書受領後、翌月末日までにお振込みいただきますようお願いいたします。</p>

        <p>ご不明な点がございましたら、下記までお問い合わせください。</p>

        <p style="font-size:0.88rem;color:#666;line-height:1.9;">
            サービス名：Care Entry（ケア・エントリー）<br>
            運営：沖縄デジタルワークス 代表者 岸本 安史<br>
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
