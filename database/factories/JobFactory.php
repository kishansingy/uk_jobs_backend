<?php

namespace Database\Factories;

use App\Enums\JobStatus;
use App\Models\Job;
use App\Models\JobSource;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $salaryMin = fake()->numberBetween(25000, 60000);

        return [
            'source_id'      => JobSource::factory(),
            'external_id'    => Str::uuid(),
            'title'          => fake()->randomElement([
                'Staff Nurse', 'Senior Physiotherapist', 'GP Registrar', 'Clinical Pharmacist',
                'Healthcare Assistant', 'Consultant Cardiologist', 'Band 6 Occupational Therapist',
                'Midwife', 'Biomedical Scientist', 'Radiographer',
            ]),
            'organisation'   => fake()->randomElement([
                'NHS Trust', 'Royal London Hospital', 'Barts Health NHS Trust',
                'Manchester University NHS Foundation Trust', 'Leeds Teaching Hospitals NHS Trust',
            ]),
            'description'    => fake()->paragraphs(5, true),
            'location'       => fake()->randomElement([
                'London', 'Manchester', 'Birmingham', 'Leeds', 'Bristol', 'Liverpool',
                'Sheffield', 'Newcastle', 'Nottingham', 'Cardiff',
            ]),
            'postcode'       => fake()->postcode(),
            'salary_min'     => $salaryMin,
            'salary_max'     => $salaryMin + fake()->numberBetween(3000, 10000),
            'salary_text'    => "£{$salaryMin} - £" . ($salaryMin + 5000) . " per annum",
            'band'           => fake()->randomElement(['5', '6', '7', '8a', '8b', null]),
            'contract_type'  => fake()->randomElement(['permanent', 'fixed_term', 'bank', 'locum']),
            'employment_type'=> fake()->randomElement(['full_time', 'part_time']),
            'closing_date'   => fake()->dateTimeBetween('+7 days', '+60 days')->format('Y-m-d'),
            'published_date' => fake()->dateTimeBetween('-14 days', 'now')->format('Y-m-d'),
            'source_url'     => fake()->url(),
            'status'         => JobStatus::Active->value,
            'first_seen_at'  => now(),
            'last_seen_at'   => now(),
            'last_updated_at'=> now(),
        ];
    }

    public function closing(): static
    {
        return $this->state(fn() => [
            'status'       => JobStatus::ClosingSoon->value,
            'closing_date' => now()->addDays(2)->toDateString(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn() => [
            'status'       => JobStatus::Expired->value,
            'closing_date' => now()->subDays(10)->toDateString(),
        ]);
    }
}
