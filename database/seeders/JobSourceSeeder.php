<?php

namespace Database\Seeders;

use App\Models\JobSource;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class JobSourceSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve profession IDs by slug
        $p = Profession::whereIn('slug', [
            'doctor', 'nurse', 'pharmacist', 'physiotherapist', 'radiographer',
            'occupational-therapist', 'speech-and-language-therapist',
            'healthcare-assistant', 'midwife', 'biomedical-scientist',
        ])->pluck('id', 'slug');

        $sources = [

            // ── Doctors ───────────────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Junior Doctor (London)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_junior_doctor_london',
                'profession_id'          => $p['doctor'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'junior doctor', 'location' => 'London', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — GP / General Practitioner',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_gp_national',
                'profession_id'          => $p['doctor'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'GP general practitioner', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Consultant Physician',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_consultant_physician',
                'profession_id'          => $p['doctor'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 720,
                'configuration'          => ['keyword' => 'consultant physician', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Doctor (Manchester)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_doctor_manchester',
                'profession_id'          => $p['doctor'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'doctor', 'location' => 'Manchester', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Nurses ────────────────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Registered Nurse (London)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_nurse_london',
                'profession_id'          => $p['nurse'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 240,
                'configuration'          => ['keyword' => 'registered nurse', 'location' => 'London', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Registered Nurse (National)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_nurse_national',
                'profession_id'          => $p['nurse'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 240,
                'configuration'          => ['keyword' => 'registered nurse', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — ICU / Critical Care Nurse',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_icu_nurse',
                'profession_id'          => $p['nurse'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'ICU critical care nurse', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Mental Health Nurse',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_mental_health_nurse',
                'profession_id'          => $p['nurse'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'mental health nurse', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Nurse (Scotland)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_nurse_scotland',
                'profession_id'          => $p['nurse'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'registered nurse', 'location' => 'Edinburgh', 'country' => 'GB-SCT', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Midwife',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_midwife',
                'profession_id'          => $p['midwife'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'midwife midwifery', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Pharmacists ───────────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Pharmacist (National)',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_pharmacist_national',
                'profession_id'          => $p['pharmacist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'pharmacist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Clinical Pharmacist',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_clinical_pharmacist',
                'profession_id'          => $p['pharmacist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 720,
                'configuration'          => ['keyword' => 'clinical pharmacist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Lab / Biomedical ──────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Biomedical Scientist',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_biomedical_scientist',
                'profession_id'          => $p['biomedical-scientist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'biomedical scientist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Lab Technician / MLT',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_lab_technician',
                'profession_id'          => $p['biomedical-scientist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'laboratory technician', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — Haematology / Blood Sciences',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_haematology',
                'profession_id'          => $p['biomedical-scientist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 720,
                'configuration'          => ['keyword' => 'haematology biomedical', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Radiographers ─────────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Diagnostic Radiographer',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_diagnostic_radiographer',
                'profession_id'          => $p['radiographer'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'diagnostic radiographer', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
            [
                'name'                   => 'NHS Jobs — MRI Radiographer',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_mri_radiographer',
                'profession_id'          => $p['radiographer'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 720,
                'configuration'          => ['keyword' => 'MRI radiographer', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Physiotherapists ──────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Physiotherapist',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_physiotherapist',
                'profession_id'          => $p['physiotherapist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'physiotherapist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Occupational Therapist ────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Occupational Therapist',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_occupational_therapist',
                'profession_id'          => $p['occupational-therapist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 360,
                'configuration'          => ['keyword' => 'occupational therapist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Speech & Language ─────────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Speech and Language Therapist',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_speech_language_therapist',
                'profession_id'          => $p['speech-and-language-therapist'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 720,
                'configuration'          => ['keyword' => 'speech language therapist', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],

            // ── Healthcare Assistant ──────────────────────────────────────────
            [
                'name'                   => 'NHS Jobs — Healthcare Assistant',
                'type'                   => 'nhs_jobs',
                'base_url'               => 'https://www.jobs.nhs.uk',
                'source_identifier'      => 'nhs_healthcare_assistant',
                'profession_id'          => $p['healthcare-assistant'] ?? null,
                'active'                 => true,
                'authorised'             => true,
                'fetch_method'           => 'pull',
                'fetch_interval_minutes' => 240,
                'configuration'          => ['keyword' => 'healthcare assistant', 'location' => '', 'country' => 'GB-ENG', 'max_pages' => 5],
            ],
        ];

        foreach ($sources as $data) {
            JobSource::updateOrCreate(
                ['source_identifier' => $data['source_identifier']],
                $data
            );
        }

        $this->command->info('Created/updated ' . count($sources) . ' job sources.');
    }
}
