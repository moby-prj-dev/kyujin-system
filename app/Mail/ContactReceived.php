<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ケアエントリー] お問い合わせを受信しました：' . $this->data['subject'],
            replyTo: [new Address($this->data['email'], $this->data['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_received',
            // $message はLaravelのメール予約変数(Illuminate\Mail\Message)と衝突するため inquiry に渡す
            with: [...collect($this->data)->except('message')->all(), 'inquiry' => $this->data['message'] ?? ''],
        );
    }
}
