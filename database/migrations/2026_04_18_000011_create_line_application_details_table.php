<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_application_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('line_user_id')->nullable();
            $table->string('line_session_id')->nullable();
            $table->string('available_from')->nullable();
            $table->boolean('experience_flag')->nullable();
            $table->text('preferred_conditions_summary')->nullable();
            $table->string('area')->nullable();
            $table->json('raw_answers_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_application_details');
    }
};
