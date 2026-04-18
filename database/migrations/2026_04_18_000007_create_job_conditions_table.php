<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('master_conditions');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['job_id', 'condition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_conditions');
    }
};
