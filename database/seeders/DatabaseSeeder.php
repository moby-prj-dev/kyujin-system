<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterAreaSeeder::class,
            MasterJobTypeSeeder::class,
            MasterEmploymentTypeSeeder::class,
            MasterConditionSeeder::class,
            MasterAppealSeeder::class,
        ]);
    }
}
