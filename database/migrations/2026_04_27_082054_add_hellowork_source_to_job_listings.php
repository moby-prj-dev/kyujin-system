<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('source')->default('care_entry')->after('id')->index();
            $table->string('hw_job_no', 30)->nullable()->unique()->after('source');
            $table->string('hw_job_url', 512)->nullable()->after('hw_job_no');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['source', 'hw_job_no', 'hw_job_url']);
        });
    }
};
