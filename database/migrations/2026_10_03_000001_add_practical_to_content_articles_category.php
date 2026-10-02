<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // 記事カテゴリに「介護・福祉の仕事術(practical)」を追加(面接・履歴書・職場選びなどの実用記事)
    public function up(): void
    {
        DB::statement("ALTER TABLE content_articles MODIFY COLUMN category ENUM('industry','job_type','area','qualification','beginner','practical') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE content_articles MODIFY COLUMN category ENUM('industry','job_type','area','qualification','beginner') NOT NULL");
    }
};
