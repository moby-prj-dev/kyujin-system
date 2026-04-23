<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->enum('category', ['industry', 'job_type', 'area', 'qualification', 'beginner']);
            $table->string('title');
            $table->string('h1');
            $table->string('meta_description', 200);
            $table->longText('body');
            $table->foreignId('area_id')->nullable()->constrained('master_areas')->nullOnDelete();
            $table->foreignId('job_type_id')->nullable()->constrained('master_job_types')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_articles');
    }
};
