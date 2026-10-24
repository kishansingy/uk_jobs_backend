<?php

namespace Database\Seeders;

use App\Models\InterviewCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InterviewCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'General NHS',            'description' => 'Questions about NHS values, structure and working within the NHS.'],
            ['name' => 'CV & Experience',         'description' => 'Discussing your work history, achievements and career journey.'],
            ['name' => 'Clinical Scenarios',      'description' => 'Scenario-based clinical reasoning and decision making.'],
            ['name' => 'Communication',           'description' => 'Patient communication, explaining complex information clearly.'],
            ['name' => 'Teamwork',                'description' => 'Working collaboratively with multidisciplinary teams.'],
            ['name' => 'Leadership',              'description' => 'Leadership, management and development of others.'],
            ['name' => 'Safeguarding',            'description' => 'Child and adult safeguarding awareness and responsibilities.'],
            ['name' => 'Conflict Resolution',     'description' => 'Handling difficult situations with colleagues, patients and families.'],
            ['name' => 'Patient-Centred Care',    'description' => 'Putting patient needs, dignity and preferences at the centre.'],
            ['name' => 'Equality & Diversity',    'description' => 'Inclusive practice and understanding of equality legislation.'],
            ['name' => 'Continuing Development',  'description' => 'CPD, keeping up-to-date and professional development.'],
        ];

        foreach ($categories as $i => $cat) {
            InterviewCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name'        => $cat['name'],
                    'description' => $cat['description'],
                    'sort_order'  => $i + 1,
                    'active'      => true,
                ]
            );
        }
    }
}
