<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE billing_summaries MODIFY COLUMN status ENUM('unbilled','sent','paid','on_hold','unpaid','overdue') NOT NULL DEFAULT 'unbilled'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE billing_summaries MODIFY COLUMN status ENUM('unbilled','sent','paid','on_hold') NOT NULL DEFAULT 'unbilled'");
    }
};
