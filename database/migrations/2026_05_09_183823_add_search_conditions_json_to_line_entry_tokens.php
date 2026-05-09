<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('line_entry_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('line_entry_tokens', 'search_conditions_json')) {
                $table->json('search_conditions_json')->nullable()->after('line_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('line_entry_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('line_entry_tokens', 'search_conditions_json')) {
                $table->dropColumn('search_conditions_json');
            }
        });
    }
};
