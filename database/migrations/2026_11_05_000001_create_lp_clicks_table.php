<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 求人ページ等のボタン・送客枠のクリック記録(施策の効果測定用)
    public function up(): void
    {
        Schema::create('lp_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->comment('hw_apply / line_apply / form_apply / pr_job / direct_job');
            $table->unsignedBigInteger('job_id')->nullable()->comment('クリックが起きたページの求人ID(記事・トップはnull)');
            $table->unsignedBigInteger('target_job_id')->nullable()->comment('送客枠でクリックされた求人ID');
            $table->string('page', 30)->nullable()->comment('lp / article / top');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('clicked_at')->useCurrent();
            $table->index(['type', 'clicked_at']);
            $table->index('target_job_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lp_clicks');
    }
};
