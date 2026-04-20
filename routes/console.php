<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 毎朝9時：トライアル終了7日前の警告メール送信
Schedule::command('billing:send-trial-warnings')->dailyAt('09:00');

// 毎朝9時10分：掲載期限切れ通知メール送信
Schedule::command('billing:send-job-expired-notifications')->dailyAt('09:10');

// 毎月1日8時：前月分の請求集計＆メール送信
Schedule::command('billing:generate-monthly')->monthlyOn(1, '08:00');
