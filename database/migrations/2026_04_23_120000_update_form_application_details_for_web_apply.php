<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_application_details', function (Blueprint $table) {
            $table->foreignId('desired_area_id')->nullable()->after('application_id')
                ->constrained('master_areas')->nullOnDelete();
            $table->text('appeal_message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('form_application_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('desired_area_id');
            $table->text('appeal_message')->nullable(false)->change();
        });
    }
};
