<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            HousePlanSeeder::class,   // depends on CategorySeeder
            AdminUserSeeder::class,
            TestimonialSeeder::class,
            BlogPostSeeder::class,
            CompletedProjectSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}
