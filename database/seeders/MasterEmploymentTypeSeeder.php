<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterEmploymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => '正社員',   'slug' => 'full_time'],
            ['name' => '契約社員', 'slug' => 'contract'],
            ['name' => 'パート',   'slug' => 'part_time'],
            ['name' => 'アルバイト', 'slug' => 'part_time_casual'],
            ['name' => '業務委託', 'slug' => 'freelance'],
            ['name' => '派遣社員', 'slug' => 'temp_staff'],
        ];

        foreach ($types as $i => $type) {
            DB::table('master_employment_types')->insertOrIgnore(array_merge($type, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
