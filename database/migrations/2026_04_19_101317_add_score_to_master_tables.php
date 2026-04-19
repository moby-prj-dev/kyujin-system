<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['master_job_types', 'master_conditions', 'master_appeals'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->tinyInteger('score')->default(3)->after('is_active');
                $table->boolean('is_main_candidate')->default(false)->after('score');
            });
        }
    }

    public function down(): void
    {
        foreach (['master_job_types', 'master_conditions', 'master_appeals'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['score', 'is_main_candidate']);
            });
        }
    }
};
