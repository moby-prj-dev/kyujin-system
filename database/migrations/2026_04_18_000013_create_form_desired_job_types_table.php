<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_desired_job_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('job_type_id')->constrained('master_job_types');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'job_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_desired_job_types');
    }
};
