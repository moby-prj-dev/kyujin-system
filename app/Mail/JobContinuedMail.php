<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobContinuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Job $job) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【ケアエントリー】掲載継続通知',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job_continued',
        );
    }
}
