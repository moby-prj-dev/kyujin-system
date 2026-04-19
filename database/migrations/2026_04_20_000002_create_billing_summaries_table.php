<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('contact_email');
            $table->string('billing_month', 7);
            $table->integer('valid_count')->default(0);
            $table->integer('billable_count')->default(0);
            $table->integer('unit_price')->default(3000);
            $table->integer('total_amount')->default(0);
            $table->enum('status', ['unbilled','sent','paid','on_hold'])->default('unbilled');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['contact_email','billing_month']);
            $table->index('contact_email');
            $table->index('billing_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_summaries');
    }
};
