<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('area_statistics')) return;

        Schema::create('area_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('prefecture', 50);                  // 沖縄県
            $table->string('occupation', 100);                 // 介護職員、ホームヘルパー等
            $table->unsignedSmallInteger('year');              // 統計年
            $table->unsignedInteger('monthly_wage')->nullable();      // 月給(円)
            $table->unsignedInteger('annual_special_wage')->nullable();// 年間賞与その他特別給与額(円)
            $table->unsignedInteger('sample_size')->nullable();// 集計対象者数
            $table->string('source', 50);                      // 'estat_chingin_census'
            $table->json('raw_payload')->nullable();           // APIレスポンス保存
            $table->timestamp('fetched_at');
            $table->timestamps();
            $table->unique(['prefecture', 'occupation', 'year', 'source'], 'uniq_area_stats');
            $table->index(['prefecture', 'occupation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_statistics');
    }
};
