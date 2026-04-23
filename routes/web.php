<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::view('/client', 'client.index')->name('client');

// -----------------------------------------------
// 求人管理（掲載主向け）
// -----------------------------------------------
Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
Route::get('/jobs/check-trial', [\App\Http\Controllers\JobController::class, 'checkTrial'])->name('jobs.check_trial');
Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->middleware('throttle:5,60')->name('jobs.store');
Route::get('/jobs/verify-sent', [\App\Http\Controllers\JobController::class, 'verifySent'])->name('jobs.verify_sent');
Route::get('/jobs/verify/{verificationToken}', [\App\Http\Controllers\JobVerificationController::class, 'verify'])->name('jobs.verify');
Route::get('/jobs/duplicate', [\App\Http\Controllers\JobController::class, 'duplicate'])->name('jobs.duplicate');
Route::get('/jobs/resend', [\App\Http\Controllers\JobResendController::class, 'show'])->name('jobs.resend.show');
Route::post('/jobs/resend', [\App\Http\Controllers\JobResendController::class, 'resend'])->middleware('throttle:5,60')->name('jobs.resend');
// -----------------------------------------------
// SEOページ（求職者向け求人一覧）
// -----------------------------------------------
Route::get('/jobs/okinawa', [\App\Http\Controllers\SeoJobController::class, 'index'])->name('seo.jobs.okinawa');
Route::get('/jobs/okinawa/{slug}', [\App\Http\Controllers\SeoJobController::class, 'area'])->name('seo.jobs.area');

// -----------------------------------------------
// コンテンツSEO記事
// -----------------------------------------------
Route::get('/articles', [\App\Http\Controllers\ContentArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [\App\Http\Controllers\ContentArticleController::class, 'show'])->name('articles.show');

Route::get('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'manage'])->name('jobs.manage');
Route::put('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
Route::post('/jobs/{token}/continue', [\App\Http\Controllers\JobController::class, 'continue'])->name('jobs.continue');
Route::patch('/jobs/{token}/close', [\App\Http\Controllers\JobController::class, 'close'])->name('jobs.close');
Route::patch('/jobs/{token}/reopen', [\App\Http\Controllers\JobController::class, 'reopen'])->name('jobs.reopen');
Route::delete('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'destroy'])->name('jobs.destroy');

// -----------------------------------------------
// LINE 応募エントリー（検索条件付きでLINEに遷移）
// -----------------------------------------------
Route::get('/line-entry/{token}', [\App\Http\Controllers\LineEntryController::class, 'entry'])->name('line.entry');

// -----------------------------------------------
// LINE LIFF（求職者向け）
// -----------------------------------------------
Route::get('/liff/{token}', [\App\Http\Controllers\LiffController::class, 'show'])->name('liff.show');
Route::post('/liff/{token}/apply', [\App\Http\Controllers\LiffController::class, 'store'])->name('liff.apply.store');
Route::get('/liff/{token}/thanks', [\App\Http\Controllers\LiffController::class, 'thanks'])->name('liff.thanks');

// -----------------------------------------------
// LINE Webhook（LINEサーバーからのコールバック）
// -----------------------------------------------
Route::post('/webhook/line', [\App\Http\Controllers\LineWebhookController::class, 'handle'])->name('webhook.line');

// -----------------------------------------------
// 求人LP（求職者向け）
// -----------------------------------------------
Route::get('/lp/{token}', [\App\Http\Controllers\LpController::class, 'show'])->name('lp.show');
Route::get('/lp/{token}/apply', [\App\Http\Controllers\ApplyController::class, 'show'])->name('lp.apply');
Route::post('/lp/{token}/apply/judge', [\App\Http\Controllers\ApplyController::class, 'judge'])->middleware('throttle:20,60')->name('lp.apply.judge');
Route::post('/lp/{token}/apply', [\App\Http\Controllers\ApplyController::class, 'store'])->middleware('throttle:10,60')->name('lp.apply.store');
Route::get('/lp/{token}/apply/thanks', [\App\Http\Controllers\ApplyController::class, 'thanks'])->name('lp.apply.thanks');

// -----------------------------------------------
// 管理画面
// -----------------------------------------------
Route::get('/admin/login',  [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.jobs.index'));

    // 求人・企業一覧
    Route::get('/jobs', [\App\Http\Controllers\Admin\JobController::class, 'index'])->name('jobs.index');
    Route::patch('/jobs/{job}/toggle-hidden', [\App\Http\Controllers\Admin\JobController::class, 'toggleHidden'])->name('jobs.toggle_hidden');
    Route::patch('/jobs/{job}/memo', [\App\Http\Controllers\Admin\JobController::class, 'updateMemo'])->name('jobs.memo');

    // 応募一覧
    Route::get('/applications', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'update'])->name('applications.update');

    // 記事管理
    Route::get('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles/generate', [\App\Http\Controllers\Admin\ArticleController::class, 'generate'])->name('articles.generate');
    Route::delete('/articles/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('articles.destroy');

    // 請求管理
    Route::get('/billings', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('billings.index');
    Route::patch('/billings/{billing}/status', [\App\Http\Controllers\Admin\BillingController::class, 'updateStatus'])->name('billings.status');
    Route::post('/billings/{billing}/send', [\App\Http\Controllers\Admin\BillingController::class, 'send'])->name('billings.send');
    Route::post('/billings/generate', [\App\Http\Controllers\Admin\BillingController::class, 'generate'])->name('billings.generate');
});

// -----------------------------------------------
// 固定ページ
// -----------------------------------------------
Route::view('/company', 'pages.company')->name('company');
Route::view('/privacy-policy', 'pages.privacy_policy')->name('privacy-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/legal', 'pages.legal')->name('legal');
