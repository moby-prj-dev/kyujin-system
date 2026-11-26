<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * スタンダードの無料期間中(切り替えた月の月末まで)にベーシックへ戻した日時。
     * 無料期間中の戻しはロック中でも1回だけ認める(繰り返してスタンダードを無料で使い続けるのを防ぐため記録する)
     */
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->timestamp('free_downgrade_used_at')->nullable()->after('plan_locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn('free_downgrade_used_at');
        });
    }
};
