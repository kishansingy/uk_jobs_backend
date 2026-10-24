<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Free',
                'slug'          => 'free',
                'description'   => 'Get started with job searching and basic career guidance.',
                'price_monthly' => 0,
                'price_annual'  => 0,
                'sort_order'    => 1,
                'features'      => [
                    'job_alerts'          => '2',
                    'saved_jobs'          => '10',
                    'cv_upload'           => 'true',
                    'cv_analysis'         => 'false',
                    'supporting_statement'=> 'false',
                    'interview_prep'      => 'basic',
                    'video_library'       => 'limited',
                    'advisor_access'      => 'false',
                ],
            ],
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'description'   => 'Advanced matching, unlimited alerts, AI-powered CV and interview tools.',
                'price_monthly' => 9.99,
                'price_annual'  => 99.99,
                'sort_order'    => 2,
                'features'      => [
                    'job_alerts'           => 'unlimited',
                    'saved_jobs'           => 'unlimited',
                    'cv_upload'            => 'true',
                    'cv_analysis'          => 'true',
                    'supporting_statement' => 'true',
                    'interview_prep'       => 'full',
                    'video_library'        => 'full',
                    'advisor_access'       => 'false',
                ],
            ],
            [
                'name'          => 'Premium',
                'slug'          => 'premium',
                'description'   => 'Everything in Pro, plus human advisor sessions and mock interviews.',
                'price_monthly' => 24.99,
                'price_annual'  => 249.99,
                'sort_order'    => 3,
                'features'      => [
                    'job_alerts'           => 'unlimited',
                    'saved_jobs'           => 'unlimited',
                    'cv_upload'            => 'true',
                    'cv_analysis'          => 'true',
                    'supporting_statement' => 'true',
                    'interview_prep'       => 'full',
                    'video_library'        => 'full',
                    'advisor_access'       => 'true',
                    'mock_interview'       => 'true',
                    'one_to_one_support'   => 'true',
                ],
            ],
        ];

        foreach ($plans as $data) {
            $plan = Plan::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'          => $data['name'],
                    'description'   => $data['description'],
                    'price_monthly' => $data['price_monthly'],
                    'price_annual'  => $data['price_annual'],
                    'currency'      => 'GBP',
                    'active'        => true,
                    'sort_order'    => $data['sort_order'],
                ]
            );

            foreach ($data['features'] as $key => $value) {
                PlanFeature::updateOrCreate(
                    ['plan_id' => $plan->id, 'feature_key' => $key],
                    ['feature_value' => $value]
                );
            }
        }
    }
}
