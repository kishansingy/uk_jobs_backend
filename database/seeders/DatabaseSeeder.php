<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core (order matters — roles before users, professions before profiles)
            RolesAndPermissionsSeeder::class,
            ProfessionSeeder::class,
            LocationSeeder::class,
            PlanSeeder::class,
            InterviewCategorySeeder::class,
            InterviewQuestionSeeder::class,
            VideoCategorySeeder::class,
            AdminUserSeeder::class,

            // Demo data — comment out for production
            DemoDataSeeder::class,
        ]);
    }
}
