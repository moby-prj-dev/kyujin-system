<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('salary_type', 20)->nullable()->after('free_text');
            $table->unsignedInteger('salary_min')->nullable()->after('salary_type');
            $table->unsignedInteger('salary_max')->nullable()->after('salary_min');
            $table->string('salary_note', 500)->nullable()->after('salary_max');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['salary_type', 'salary_min', 'salary_max', 'salary_note']);
        });
    }
};
