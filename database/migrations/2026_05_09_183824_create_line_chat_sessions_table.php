<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('line_chat_sessions')) {
            return;
        }

        Schema::create('line_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('line_user_id')->index();
            $table->string('step');
            $table->foreignId('job_id')->nullable()->constrained('job_listings')->nullOnDelete();
            $table->string('entry_token', 64)->nullable();
            $table->json('search_conditions_json')->nullable();
            $table->string('applicant_name')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_chat_sessions');
    }
};
