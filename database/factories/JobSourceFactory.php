<?php

namespace Database\Factories;

use App\Enums\JobSourceType;
use App\Models\JobSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobSourceFactory extends Factory
{
    protected $model = JobSource::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->company() . ' Jobs',
            'type'              => JobSourceType::Api,
            'base_url'          => fake()->url(),
            'source_identifier' => Str::slug(fake()->company()) . '-' . fake()->randomNumber(4),
            'active'            => true,
            'authorised'        => true,
            'fetch_method'      => 'pull',
            'permission_status' => 'authorised',
            'terms_reference'   => 'API Terms v1.0',
        ];
    }

    public function manual(): static
    {
        return $this->state(fn() => [
            'type'         => JobSourceType::Manual,
            'base_url'     => null,
            'authorised'   => true,
        ]);
    }
}
