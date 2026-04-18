<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings');
            $table->string('application_type');
            $table->string('applicant_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('status')->default('received');
            $table->timestamp('applied_at');
            $table->timestamps();
            $table->index(['job_id', 'application_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
