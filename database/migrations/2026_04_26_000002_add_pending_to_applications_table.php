<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('is_suspicious')->default(false)->after('is_billable');
            $table->string('suspicious_reason')->nullable()->after('is_suspicious');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['is_suspicious', 'suspicious_reason']);
        });
    }
};
