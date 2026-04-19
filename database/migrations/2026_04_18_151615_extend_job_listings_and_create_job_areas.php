<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');
            $table->text('free_text')->nullable()->after('description_generated');
            $table->string('photo_path')->nullable()->after('free_text');
        });

        Schema::create('job_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('master_areas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_areas');

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['free_text', 'photo_path']);
            $table->foreignId('area_id')->constrained('master_areas');
        });
    }
};
