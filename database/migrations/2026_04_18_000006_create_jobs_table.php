<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('master_areas');
            $table->foreignId('job_type_id')->constrained('master_job_types');
            $table->foreignId('employment_type_id')->constrained('master_employment_types');
            $table->string('title');
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->text('description_generated')->nullable();
            $table->string('status')->default('draft');
            $table->string('token', 64)->unique();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
