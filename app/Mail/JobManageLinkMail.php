<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobManageLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】編集用リンクをお送りします');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.job_manage_link');
    }
}
