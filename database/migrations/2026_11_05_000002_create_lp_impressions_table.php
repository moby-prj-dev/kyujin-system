<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 送客枠(おすすめ求人PR / 直接応募できる求人)にカードが表示された回数(ボットは記録しない)
    public function up(): void
    {
        Schema::create('lp_impressions', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 20)->comment('pr_job / direct_job');
            $table->unsignedBigInteger('target_job_id')->comment('表示された求人ID');
            $table->string('page', 20)->comment('lp / article / top');
            $table->timestamp('shown_at')->useCurrent();
            $table->index(['target_job_id', 'shown_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lp_impressions');
    }
};
