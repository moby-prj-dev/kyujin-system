<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminJobNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】新規求人が掲載されました');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin_job_notification');
    }
}
