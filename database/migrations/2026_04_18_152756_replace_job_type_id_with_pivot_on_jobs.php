<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropForeign(['job_type_id']);
            $table->dropColumn('job_type_id');
        });

        Schema::create('job_job_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('job_type_id')->constrained('master_job_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_job_types');

        Schema::table('job_listings', function (Blueprint $table) {
            $table->foreignId('job_type_id')->constrained('master_job_types');
        });
    }
};
