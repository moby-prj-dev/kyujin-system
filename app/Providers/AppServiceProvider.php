<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (str_starts_with(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        // 返信先が未指定のメールは受信用アドレスを返信先にする(送信元 info@care-entry.net は受信不可のため)
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $replyTo = config('mail.default_reply_to');
            if (empty($event->message->getReplyTo()) && !empty($replyTo['address'])) {
                $event->message->replyTo(new \Symfony\Component\Mime\Address($replyTo['address'], $replyTo['name'] ?? ''));
            }
        });
    }
}
