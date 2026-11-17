<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 職種「看護師（福祉施設勤務）」を「看護師（施設・訪問看護）」に改名する。
 * 「福祉施設勤務」だと訪問看護を探す看護師が自分向けだと気づきにくいため(slug・URLは変えない)。
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('master_job_types')->where('slug', 'nurse_welfare_facility')
            ->update(['name' => '看護師（施設・訪問看護）']);
    }

    public function down(): void
    {
        DB::table('master_job_types')->where('slug', 'nurse_welfare_facility')
            ->update(['name' => '看護師（福祉施設勤務）']);
    }
};
