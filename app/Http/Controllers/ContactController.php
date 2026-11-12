<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    private const SUCCESS = 'お問い合わせを受け付けました。担当者より平日10:00〜18:00の間にご連絡いたします。';

    /** ページを開いてから送信までの最短秒数(これより速いのはボット) */
    private const MIN_SECONDS = 3;

    /** フォームの有効期限(これより古い表示時刻は使い回しとみなす) */
    private const MAX_SECONDS = 60 * 60 * 24;

    public function show(): View
    {
        return view('contact');
    }

    public function send(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // 迷惑メールは、ボットに見分けられないよう「受け付けました」と表示したうえで送らない
        if ($reason = $this->spamReason($request, $data)) {
            Log::info('お問い合わせを迷惑メールとして破棄', [
                'reason'  => $reason,
                'ip'      => $request->ip(),
                'email'   => $data['email'] ?? null,
                'subject' => mb_substr($data['subject'] ?? '', 0, 80),
            ]);

            return redirect()->route('contact.show')->with('success', self::SUCCESS);
        }

        Mail::to(config('mail.contact_to', 'careentry.info@gmail.com'))
            ->send(new ContactReceived($data));

        return redirect()
            ->route('contact.show')
            ->with('success', self::SUCCESS);
    }

    /** 迷惑メールと判定した理由。問題なければ null */
    private function spamReason(Request $request, array $data): ?string
    {
        // 1. フォームを表示した時刻(ページを開かずに直接送信・数秒で送信・使い回し)
        try {
            $shownAt = (int) decrypt((string) $request->input('_ts'));
        } catch (\Throwable) {
            return 'no_timestamp';
        }
        $elapsed = now()->timestamp - $shownAt;
        if ($elapsed < self::MIN_SECONDS) {
            return 'too_fast';
        }
        if ($elapsed > self::MAX_SECONDS) {
            return 'stale_form';
        }

        // 2. 日本語(ひらがな・カタカナ・漢字)が件名にも本文にもない
        $text = ($data['subject'] ?? '') . "\n" . ($data['message'] ?? '');
        if (! preg_match('/[\p{Hiragana}\p{Katakana}\p{Han}]/u', $text)) {
            return 'no_japanese';
        }

        // 3. 本文のURLが多い(宣伝リンクの貼り付け)
        if (preg_match_all('#https?://#i', $data['message'] ?? '') >= 3) {
            return 'too_many_urls';
        }

        return null;
    }
}
