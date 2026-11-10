<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** 掲載中の自社求人に3か月ごとに送る「募集は続いていますか？」の確認メール(返信不要・何もしなければ掲載継続) */
class RecruitingCheckMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job, public int $views, public int $applications) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '【ケアエントリー】掲載中の求人の募集状況のご確認');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recruiting_check');
    }
}
