<?php

namespace Database\Seeders;

use App\Models\AdvisorAvailability;
use App\Models\CareerAdvisor;
use App\Models\CandidateProfile;
use App\Models\Job;
use App\Models\JobSource;
use App\Models\Profession;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo candidate ────────────────────────────────────────────────────
        $candidate = User::firstOrCreate(
            ['email' => 'candidate@demo.com'],
            [
                'name'                 => 'Dr. Sarah Ahmed',
                'phone'                => '+44 7700 900123',
                'country'              => 'GB',
                'password'             => Hash::make('password'),
                'email_verified_at'    => now(),
                'onboarding_completed' => true,
                'active'               => true,
            ]
        );
        $candidate->assignRole('free-user');

        $nurse = Profession::where('slug', 'nurse')->first();
        $doctor = Profession::where('slug', 'doctor')->first();
        $specialty = Specialty::where('slug', 'general-practice')->first();

        CandidateProfile::firstOrCreate(
            ['user_id' => $candidate->id],
            [
                'profession_id'          => $doctor?->id,
                'specialty_id'           => $specialty?->id,
                'current_country'        => 'GB',
                'uk_location_preference' => 'London',
                'experience_years'       => '5',
                'registration_body'      => 'GMC',
                'sponsorship_required'   => false,
                'professional_summary'   => 'Experienced GP with 5 years of NHS service, passionate about patient-centred care.',
            ]
        );

        // ── Demo advisor ──────────────────────────────────────────────────────
        $advisorUser = User::firstOrCreate(
            ['email' => 'advisor@demo.com'],
            [
                'name'              => 'James Okafor',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'onboarding_completed' => true,
                'active'            => true,
            ]
        );
        $advisorUser->assignRole('career-advisor');

        $advisor = CareerAdvisor::firstOrCreate(
            ['user_id' => $advisorUser->id],
            [
                'display_name' => 'James Okafor',
                'specialisms'  => json_encode(['NHS Career Transition', 'International Doctors', 'Band 6-8 Applications']),
                'bio'          => 'Former NHS HR manager with 12 years of experience helping international healthcare professionals secure NHS roles.',
                'active'       => true,
            ]
        );

        // Add availability slots for the next 14 days
        $times = [['09:00', '09:45'], ['10:00', '10:45'], ['14:00', '14:45'], ['15:00', '15:45']];
        for ($d = 1; $d <= 14; $d++) {
            $date = Carbon::now()->addDays($d);
            if ($date->isWeekend()) continue;

            foreach ($times as [$start, $end]) {
                AdvisorAvailability::firstOrCreate(
                    [
                        'advisor_id'     => $advisor->id,
                        'available_date' => $date->toDateString(),
                        'start_time'     => $start,
                    ],
                    [
                        'end_time' => $end,
                        'booked'   => false,
                    ]
                );
            }
        }

        // ── Demo job source ───────────────────────────────────────────────────
        $source = JobSource::firstOrCreate(
            ['source_identifier' => 'nhs-jobs-demo'],
            [
                'name'       => 'NHS Jobs (Demo)',
                'base_url'   => 'https://www.jobs.nhs.uk',
                'type'       => 'api',
                'active'     => true,
                'authorised' => true,
            ]
        );

        // ── Demo jobs ─────────────────────────────────────────────────────────
        $demoJobs = [
            [
                'title'        => 'Band 5 Staff Nurse — General Medicine',
                'organisation' => 'Royal London Hospital',
                'location'     => 'London',
                'profession'   => 'nurse',
                'specialty'    => 'adult-nursing',
                'band'         => '5',
                'salary_min'   => 28407,
                'salary_max'   => 34581,
                'salary_text'  => '£28,407 – £34,581 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'E1',
                'closing_days' => 21,
                'description'  => 'We are seeking an enthusiastic and compassionate Band 5 Staff Nurse to join our General Medicine ward. You will deliver high-quality patient care, work collaboratively with the MDT, and maintain accurate records.',
            ],
            [
                'title'        => 'Band 7 Senior Physiotherapist — MSK',
                'organisation' => 'Manchester University NHS Foundation Trust',
                'location'     => 'Manchester',
                'profession'   => 'physiotherapist',
                'specialty'    => 'musculoskeletal',
                'band'         => '7',
                'salary_min'   => 43742,
                'salary_max'   => 50056,
                'salary_text'  => '£43,742 – £50,056 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'M13',
                'closing_days' => 14,
                'description'  => 'An exciting opportunity for an experienced MSK Physiotherapist to lead a specialist outpatient caseload and mentor junior staff within our award-winning therapy department.',
            ],
            [
                'title'        => 'GP Partner — Full or Part Time',
                'organisation' => 'Oakfield Medical Practice',
                'location'     => 'Birmingham',
                'profession'   => 'doctor',
                'specialty'    => 'general-practice',
                'band'         => null,
                'salary_min'   => 65000,
                'salary_max'   => 105000,
                'salary_text'  => '£65,000 – £105,000 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'B15',
                'closing_days' => 30,
                'description'  => 'Oakfield Medical Practice is a well-established training practice with 8,500 patients. We are looking for a motivated GP Partner to join our supportive team.',
            ],
            [
                'title'        => 'Band 6 Community Mental Health Nurse',
                'organisation' => 'Leeds and York Partnership NHS Foundation Trust',
                'location'     => 'Leeds',
                'profession'   => 'nurse',
                'specialty'    => 'mental-health-nursing',
                'band'         => '6',
                'salary_min'   => 35392,
                'salary_max'   => 42618,
                'salary_text'  => '£35,392 – £42,618 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'LS1',
                'closing_days' => 18,
                'description'  => 'Join our dynamic Community Mental Health Team delivering care to adults with complex mental health needs. You will manage a caseload, conduct risk assessments and work collaboratively with social care.',
            ],
            [
                'title'        => 'Locum Consultant Radiologist',
                'organisation' => 'Edinburgh Royal Infirmary',
                'location'     => 'Edinburgh',
                'profession'   => 'radiographer',
                'specialty'    => 'diagnostic-imaging',
                'band'         => null,
                'salary_min'   => 93666,
                'salary_max'   => 126281,
                'salary_text'  => '£93,666 – £126,281 per annum',
                'contract'     => 'fixed_term',
                'employment'   => 'full_time',
                'postcode'     => 'EH16',
                'closing_days' => 10,
                'description'  => '6-month locum consultant radiologist position covering general and cross-sectional imaging. On-call commitment required. FRCR or equivalent required.',
            ],
            [
                'title'        => 'Band 4 Healthcare Assistant — Critical Care',
                'organisation' => 'University Hospital Southampton',
                'location'     => 'Southampton',
                'profession'   => 'healthcare-assistant',
                'specialty'    => 'ward-based',
                'band'         => '4',
                'salary_min'   => 25147,
                'salary_max'   => 27596,
                'salary_text'  => '£25,147 – £27,596 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'SO16',
                'closing_days' => 25,
                'description'  => 'Support our Critical Care team in delivering outstanding patient care. You will assist with monitoring, documentation and patient comfort under the supervision of registered nurses.',
            ],
            [
                'title'        => 'Senior Pharmacist — Oncology',
                'organisation' => 'Christie Hospital NHS Foundation Trust',
                'location'     => 'Manchester',
                'profession'   => 'pharmacist',
                'specialty'    => 'oncology-pharmacy',
                'band'         => '7',
                'salary_min'   => 43742,
                'salary_max'   => 50056,
                'salary_text'  => '£43,742 – £50,056 per annum',
                'contract'     => 'permanent',
                'employment'   => 'full_time',
                'postcode'     => 'M20',
                'closing_days' => 20,
                'description'  => 'The Christie is seeking a specialist oncology pharmacist to provide a clinical pharmacy service to our world-leading cancer centre. Experience in chemotherapy and biologics is essential.',
            ],
            [
                'title'        => 'Midwife — Community Team',
                'organisation' => 'Birmingham Women\'s and Children\'s NHS Foundation Trust',
                'location'     => 'Birmingham',
                'profession'   => 'midwife',
                'specialty'    => 'community-midwifery',
                'band'         => '6',
                'salary_min'   => 35392,
                'salary_max'   => 42618,
                'salary_text'  => '£35,392 – £42,618 per annum',
                'contract'     => 'permanent',
                'employment'   => 'part_time',
                'postcode'     => 'B15',
                'closing_days' => 16,
                'description'  => 'An excellent opportunity for an experienced midwife to join our community team, providing continuity of care to women and their families throughout their pregnancy journey.',
            ],
        ];

        foreach ($demoJobs as $jobData) {
            $profession = Profession::where('slug', $jobData['profession'])->first();
            $specialty  = Specialty::where('slug', $jobData['specialty'])->first();

            Job::firstOrCreate(
                [
                    'title'        => $jobData['title'],
                    'organisation' => $jobData['organisation'],
                ],
                [
                    'source_id'       => $source->id,
                    'external_id'     => 'DEMO-' . strtoupper(substr(md5($jobData['title']), 0, 8)),
                    'location'        => $jobData['location'],
                    'postcode'        => $jobData['postcode'],
                    'profession_id'   => $profession?->id,
                    'specialty_id'    => $specialty?->id,
                    'band'            => $jobData['band'],
                    'salary_min'      => $jobData['salary_min'],
                    'salary_max'      => $jobData['salary_max'],
                    'salary_text'     => $jobData['salary_text'],
                    'contract_type'   => $jobData['contract'],
                    'employment_type' => $jobData['employment'],
                    'description'     => $jobData['description'],
                    'status'          => 'active',
                    'closing_date'    => Carbon::now()->addDays($jobData['closing_days'])->toDateString(),
                    'published_date'  => Carbon::now()->subDays(rand(1, 7))->toDateString(),
                    'source_url'      => 'https://www.jobs.nhs.uk/candidate/jobadvert/demo',
                    'first_seen_at'   => now(),
                    'last_seen_at'    => now(),
                ]
            );
        }

        // ── Demo videos ───────────────────────────────────────────────────────
        $cvCategory       = VideoCategory::where('slug', 'cv-application-tips')->first();
        $interviewCat     = VideoCategory::where('slug', 'interview-preparation')->first();
        $guidanceCat      = VideoCategory::where('slug', 'nhs-career-guidance')->first();

        $demoVideos = [
            [
                'title'            => 'How to Write a Standout NHS Application',
                'description'      => 'Step-by-step guide to writing a compelling NHS job application that gets you shortlisted.',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'thumbnail_url'    => 'https://picsum.photos/seed/video1/640/360',
                'duration_seconds' => 847,
                'category_id'      => $cvCategory?->id ?? 1,
                'premium_only'     => false,
                'status'           => 'published',
            ],
            [
                'title'            => 'Mastering the NHS Interview: STAR Technique',
                'description'      => 'Learn how to use the STAR method to answer competency-based NHS interview questions with confidence.',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'thumbnail_url'    => 'https://picsum.photos/seed/video2/640/360',
                'duration_seconds' => 1240,
                'category_id'      => $interviewCat?->id ?? 2,
                'premium_only'     => false,
                'status'           => 'published',
            ],
            [
                'title'            => 'Understanding NHS Bands and Pay Scales 2024',
                'description'      => 'A comprehensive breakdown of NHS Agenda for Change pay bands and what they mean for your career.',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'thumbnail_url'    => 'https://picsum.photos/seed/video3/640/360',
                'duration_seconds' => 965,
                'category_id'      => $guidanceCat?->id ?? 1,
                'premium_only'     => false,
                'status'           => 'published',
            ],
            [
                'title'            => 'AI-Powered CV Analysis Walkthrough',
                'description'      => 'See how our AI CV analysis tool works and how to get the most out of your feedback report.',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'thumbnail_url'    => 'https://picsum.photos/seed/video4/640/360',
                'duration_seconds' => 620,
                'category_id'      => $cvCategory?->id ?? 1,
                'premium_only'     => true,
                'status'           => 'published',
            ],
            [
                'title'            => 'Working as an International Doctor in the NHS',
                'description'      => 'Everything you need to know about GMC registration, PLAB, visa sponsorship and adapting to UK practice.',
                'video_url'        => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'thumbnail_url'    => 'https://picsum.photos/seed/video5/640/360',
                'duration_seconds' => 1890,
                'category_id'      => $guidanceCat?->id ?? 1,
                'premium_only'     => true,
                'status'           => 'published',
            ],
        ];

        foreach ($demoVideos as $vid) {
            Video::firstOrCreate(
                ['title' => $vid['title']],
                array_merge($vid, ['published_at' => now()])
            );
        }

        $this->command->info('Demo data seeded: candidate, advisor (with availability), 8 jobs, 5 videos.');
        $this->command->info('Demo login — Candidate: candidate@demo.com / password');
        $this->command->info('Demo login — Advisor:   advisor@demo.com   / password');
    }
}
