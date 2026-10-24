<?php

namespace Database\Seeders;

use App\Models\VideoCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VideoCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'NHS Career Guidance',
            'CV & Application Tips',
            'Interview Preparation',
            'Profession-Specific Guidance',
            'Registration & Revalidation',
            'International Healthcare Careers',
            'Workplace Guidance',
            'Mental Health & Wellbeing at Work',
            'Leadership & Development',
        ];

        foreach ($categories as $i => $name) {
            VideoCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i + 1, 'active' => true]
            );
        }
    }
}
