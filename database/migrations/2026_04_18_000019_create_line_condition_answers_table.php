<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_condition_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('master_conditions');
            $table->string('answer');
            $table->string('answer_text')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['application_id', 'condition_id']);
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_condition_answers');
    }
};
