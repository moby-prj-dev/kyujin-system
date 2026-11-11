<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** 掲載企業ログイン: そのメールアドレスで登録された全求人の管理ページリンクを1通で送る */
class ManageLoginLinksMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $jobs) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】管理ページへのログインリンク');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.manage_login_links');
    }
}
