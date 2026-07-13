<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ご応募ありがとうございます</title>
</head>
<body style="font-family:'Hiragino Kaku Gothic ProN','Meiryo',sans-serif;font-size:14px;color:#333;line-height:1.7;">
<div style="max-width:600px;margin:0 auto;padding:20px;">

<p>{{ $application->applicant_name }} 様</p>

<p>
このたびは <strong>Care Entry(ケアエントリー)</strong> より、以下の求人にご応募いただきありがとうございました。<br>
応募内容の控えをお送りいたします。
</p>

<div style="background:#f0f7ff;border-left:4px solid #1a73e8;padding:16px 20px;margin:24px 0;">
    <p style="margin:0 0 4px;font-size:12px;color:#666;">応募した求人</p>
    <p style="margin:0 0 8px;font-size:16px;font-weight:bold;color:#1a1a2e;">
        {{ $job->seo_title ?: $job->title }}
    </p>
    <p style="margin:0 0 12px;font-size:13px;color:#555;">
        <span style="color:#888;">事業所名:</span> {{ $job->company_name }}
    </p>
    <p style="margin:0;">
        <a href="{{ url('/lp/' . $job->token) }}" style="color:#1a73e8;text-decoration:none;font-weight:bold;">
            → 求人詳細を確認する
        </a>
    </p>
</div>

<h3 style="border-bottom:2px solid #e5e9f0;padding-bottom:6px;margin-top:24px;">応募内容(控え)</h3>
<table style="width:100%;border-collapse:collapse;margin:12px 0;">
    <tr>
        <td style="padding:8px 4px;color:#888;width:100px;font-size:13px;">お名前</td>
        <td style="padding:8px 4px;font-weight:bold;">{{ $application->applicant_name }}</td>
    </tr>
    <tr>
        <td style="padding:8px 4px;color:#888;font-size:13px;">電話番号</td>
        <td style="padding:8px 4px;">{{ $application->phone }}</td>
    </tr>
    @if($application->email)
    <tr>
        <td style="padding:8px 4px;color:#888;font-size:13px;">メールアドレス</td>
        <td style="padding:8px 4px;">{{ $application->email }}</td>
    </tr>
    @endif
    <tr>
        <td style="padding:8px 4px;color:#888;font-size:13px;">応募方法</td>
        <td style="padding:8px 4px;">
            @if($application->application_type === 'line')LINE応募@else Webフォーム応募@endif
        </td>
    </tr>
    <tr>
        <td style="padding:8px 4px;color:#888;font-size:13px;">応募日時</td>
        <td style="padding:8px 4px;">{{ $application->applied_at->format('Y年n月j日 H:i') }}</td>
    </tr>
</table>

@php
    $appealMessage = $application->application_type === 'line'
        ? ($application->lineDetail?->appeal_message)
        : ($application->formDetail?->appeal_message);
@endphp
@if($appealMessage)
<div style="margin:16px 0;padding:12px 14px;background:#f0f7ff;border-left:4px solid #1a73e8;border-radius:4px;">
    <p style="margin:0 0 6px;font-size:12px;color:#666;font-weight:bold;">志望動機・自己PR</p>
    <p style="margin:0;white-space:pre-wrap;font-size:13px;line-height:1.6;">{{ $appealMessage }}</p>
</div>
@endif

<div style="background:#fff8e1;border-left:4px solid #f59e0b;padding:14px 18px;margin:24px 0;font-size:13px;">
    <strong style="color:#8a6d00;">📞 今後の流れ</strong><br>
    応募内容は求人掲載主様(<strong>{{ $job->company_name }}</strong>)にお送りしました。<br>
    掲載主様から直接お電話またはメールにてご連絡いたします(通常 1〜3営業日以内)。
</div>

<p style="font-size:12px;color:#888;margin-top:32px;border-top:1px solid #e5e9f0;padding-top:16px;">
    ※ このメールは Care Entry(ケアエントリー)から自動送信されました。<br>
    ※ このメールに心当たりのない場合、破棄いただけますようお願いいたします。<br>
    ※ 応募に関するお問い合わせは、掲載主様に直接ご連絡ください。
</p>

<p style="font-size:12px;color:#888;">
    <strong>Care Entry(ケアエントリー)</strong><br>
    沖縄の介護・福祉専門 求人サービス<br>
    <a href="{{ url('/') }}" style="color:#1a73e8;">https://care-entry.net</a>
</p>

</div>
</body>
</html>
