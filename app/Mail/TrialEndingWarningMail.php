<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $companyName,
        public string $contactEmail,
        public string $trialEndsAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】無料トライアル期間終了のお知らせ');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial_ending_warning');
    }
}
