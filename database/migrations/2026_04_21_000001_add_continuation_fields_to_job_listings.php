<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->timestamp('continued_at')->nullable()->after('expired_notified_at');
            $table->timestamp('continue_notified_at')->nullable()->after('continued_at');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['continued_at', 'continue_notified_at']);
        });
    }
};
