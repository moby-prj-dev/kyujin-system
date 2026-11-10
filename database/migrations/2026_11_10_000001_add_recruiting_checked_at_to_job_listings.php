<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->timestamp('recruiting_checked_at')->nullable()->after('continue_notified_at')
                ->comment('募集継続の確認メールを最後に送った日時(3か月ごと)');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn('recruiting_checked_at');
        });
    }
};
