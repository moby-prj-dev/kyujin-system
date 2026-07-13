<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('line_application_details', function (Blueprint $table) {
            $table->text('appeal_message')->nullable()->after('area');
        });
    }

    public function down(): void
    {
        Schema::table('line_application_details', function (Blueprint $table) {
            $table->dropColumn('appeal_message');
        });
    }
};
