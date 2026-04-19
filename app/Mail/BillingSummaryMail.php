<?php

namespace App\Mail;

use App\Models\BillingSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BillingSummary $summary,
        public string $companyName,
    ) {}

    public function envelope(): Envelope
    {
        $month = $this->summary->billing_month;
        return new Envelope(subject: "【Care Entry】{$month} 月次請求のご案内");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.billing_summary');
    }
}
