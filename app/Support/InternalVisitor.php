<?php

namespace App\Support;

use Illuminate\Support\Facades\Cookie;

/**
 * 運営者(管理画面にログインしたことのあるブラウザ)と掲載者(自分の求人の管理ページを開いたブラウザ)のアクセス判定
 * 閲覧数・送客枠の表示・クリックの計測から、求職者ではないアクセスを除外するために使う
 */
class InternalVisitor
{
    public const COOKIE = 'ce_internal';
    private const MINUTES = 60 * 24 * 365; // 1年

    public static function check(): bool
    {
        return (bool) session('admin_authenticated') || request()->cookie(self::COOKIE) === '1';
    }

    /** このブラウザを計測対象外として記録(管理画面ログイン・アクセス時、掲載者の管理ページ表示時) */
    public static function mark(): void
    {
        if (request()->cookie(self::COOKIE) !== '1') {
            Cookie::queue(self::COOKIE, '1', self::MINUTES);
        }
    }
}
