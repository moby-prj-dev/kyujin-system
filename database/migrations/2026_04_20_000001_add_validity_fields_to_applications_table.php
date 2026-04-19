<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('is_valid')->default(true)->after('status');
            $table->string('invalid_reason')->nullable()->after('is_valid');
            $table->boolean('is_billable')->default(false)->after('invalid_reason');
            $table->timestamp('counted_at')->nullable()->after('is_billable');
            $table->string('normalized_email')->nullable()->after('email');
            $table->string('normalized_phone')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['is_valid','invalid_reason','is_billable','counted_at','normalized_email','normalized_phone']);
        });
    }
};
