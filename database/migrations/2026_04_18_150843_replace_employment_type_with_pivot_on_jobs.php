<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropForeign(['employment_type_id']);
            $table->dropColumn('employment_type_id');
        });

        Schema::create('job_employment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('employment_type_id')->constrained('master_employment_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_employment_types');

        Schema::table('job_listings', function (Blueprint $table) {
            $table->foreignId('employment_type_id')->constrained('master_employment_types');
        });
    }
};
