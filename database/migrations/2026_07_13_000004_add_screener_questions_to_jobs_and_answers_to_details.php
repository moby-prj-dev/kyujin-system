<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->json('screener_questions')->nullable()->after('secondary_emails');
        });
        Schema::table('form_application_details', function (Blueprint $table) {
            $table->json('screener_answers')->nullable()->after('appeal_message');
        });
        Schema::table('line_application_details', function (Blueprint $table) {
            $table->json('screener_answers')->nullable()->after('appeal_message');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn('screener_questions');
        });
        Schema::table('form_application_details', function (Blueprint $table) {
            $table->dropColumn('screener_answers');
        });
        Schema::table('line_application_details', function (Blueprint $table) {
            $table->dropColumn('screener_answers');
        });
    }
};
