<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $companyName,
        public string $contactEmail,
        public string $jobTitle,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【Care Entry】求人掲載期間が終了しました');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.job_expired');
    }
}
