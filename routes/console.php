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

// 毎日深夜2時：SEOコンテンツ記事を新規5件生成
Schedule::command('articles:generate --limit=5')->dailyAt('02:00');

// 毎週月曜深夜3時30分：ハローワーク求人のAI生成LP更新（最新20件）
Schedule::command('hellowork:generate-lps --limit=20')->weeklyOn(1, '03:30');

// 毎日深夜4時：期限切れのハローワーク求人を削除
Schedule::call(function () {
    \App\Models\Job::where('source', 'hellowork')
        ->where('expires_at', '<', now())
        ->delete();
})->dailyAt('04:00')->name('hellowork:cleanup-expired');

// 毎朝5時：Google Indexing API へ未送信URLを通知（クロール促進）
Schedule::command('indexing:submit --type=all')->dailyAt('05:00');

// 毎月15日朝4時：e-Stat 賃金センサスの最新データを取得（年1更新だが月1チェック）
Schedule::command('stats:fetch-estat')->monthlyOn(15, '04:00');
