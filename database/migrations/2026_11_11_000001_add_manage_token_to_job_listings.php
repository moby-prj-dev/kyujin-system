<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * 管理ページ専用のトークンを追加する。
 * これまで管理ページ(/jobs/{token})と公開の求人ページ(/lp/{token})が同じ token を使っており、
 * 求人ページのURLから誰でも管理ページ(応募者情報・編集・削除)を開けてしまっていたため、分離する。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('manage_token', 64)->nullable()->after('token');
        });

        DB::table('job_listings')->whereNull('manage_token')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('job_listings')->where('id', $row->id)->update(['manage_token' => Str::random(48)]);
            }
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->unique('manage_token');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropUnique(['manage_token']);
            $table->dropColumn('manage_token');
        });
    }
};
