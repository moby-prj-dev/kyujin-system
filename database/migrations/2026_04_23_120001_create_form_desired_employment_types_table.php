<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_desired_employment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('employment_type_id')->constrained('master_employment_types');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'employment_type_id'], 'fdet_app_emp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_desired_employment_types');
    }
};
