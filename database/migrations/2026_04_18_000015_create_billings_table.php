<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings');
            $table->foreignId('application_id')->constrained('applications');
            $table->string('application_type');
            $table->string('billing_trigger_type')->default('application_completed');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('JPY');
            $table->timestamp('billed_at');
            $table->string('agreement_version');
            $table->string('billing_status')->default('pending');
            $table->string('notification_status')->default('unsent');
            $table->timestamp('created_at')->useCurrent();
            $table->unique('application_id');
            $table->index('billing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
