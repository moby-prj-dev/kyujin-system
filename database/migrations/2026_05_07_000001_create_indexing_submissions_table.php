<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indexing_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('type')->nullable();
            $table->string('action')->default('URL_UPDATED');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['url', 'submitted_at']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indexing_submissions');
    }
};
