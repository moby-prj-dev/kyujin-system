<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job, public Application $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】ご応募ありがとうございます(応募控え)');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.applicant_confirmation');
    }
}
