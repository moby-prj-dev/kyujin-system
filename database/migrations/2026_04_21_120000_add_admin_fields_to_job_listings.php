<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->boolean('is_admin_hidden')->default(false)->after('status');
            $table->text('admin_memo')->nullable()->after('is_admin_hidden');
            $table->timestamp('admin_memo_updated_at')->nullable()->after('admin_memo');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['is_admin_hidden', 'admin_memo', 'admin_memo_updated_at']);
        });
    }
};
