<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><style>
body{font-family:'Hiragino Kaku Gothic ProN',sans-serif;background:#f5f7fa;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:linear-gradient(135deg,#1a73e8,#0d47a1);color:#fff;padding:28px 32px;}
.header h1{margin:0;font-size:1.2rem;}
.body{padding:28px 32px;}
table{width:100%;border-collapse:collapse;margin:16px 0;}
th,td{padding:10px 14px;border:1px solid #d6e4f7;font-size:0.93rem;}
th{background:#f0f7ff;font-weight:700;text-align:left;}
.total-row td{font-weight:800;font-size:1.05rem;color:#1a73e8;}
.btn{display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;margin-top:8px;}
.footer{background:#f5f7fa;padding:16px 32px;font-size:0.82rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>📋 {{ $summary->billing_month }} 月次請求のご案内</h1>
    </div>
    <div class="body">
        <p>{{ $companyName }} ご担当者様</p>

        <p>
            Care Entry（ケア・エントリー）をご利用いただきありがとうございます。<br>
            {{ $summary->billing_month }} の成果報酬をご請求申し上げます。
        </p>

        <table>
            <tr><th>請求月</th><td>{{ $summary->billing_month }}</td></tr>
            <tr><th>有効応募件数</th><td>{{ $summary->valid_count }} 件</td></tr>
            <tr><th>請求対象件数</th><td>{{ $summary->billable_count }} 件</td></tr>
            <tr><th>単価</th><td>¥{{ number_format($summary->unit_price) }}</td></tr>
            <tr class="total-row"><th>合計金額（税別）</th><td>¥{{ number_format($summary->total_amount) }}</td></tr>
        </table>

        <p style="font-size:0.9rem;color:#555;">
            お支払い方法・振込先口座につきましては、別途ご案内いたします。<br>
            ご不明な点がございましたら、下記の窓口までお問い合わせください。
        </p>

        <p style="margin-top:20px;font-size:0.88rem;color:#666;">
            Care Entry 運営事務局<br>
            メール：<a href="mailto:careentry.info@gmail.com">careentry.info@gmail.com</a><br>
            電話：070-9401-9492（平日10:00〜18:00）
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Care Entry. All rights reserved.
    </div>
</div>
</body>
</html>
