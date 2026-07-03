<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('plan', 20)->default('basic')->after('is_permanently_free')->index();
            $table->json('secondary_emails')->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex(['plan']);
            $table->dropColumn(['plan', 'secondary_emails']);
        });
    }
};
