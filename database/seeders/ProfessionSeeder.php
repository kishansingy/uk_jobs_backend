<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            [
                'name'              => 'Doctor',
                'registration_body' => 'GMC',
                'specialties'       => [
                    'General Practice', 'Emergency Medicine', 'Cardiology', 'Neurology',
                    'Oncology', 'Paediatrics', 'Psychiatry', 'Radiology', 'Surgery',
                    'Anaesthetics', 'Obstetrics & Gynaecology', 'Geriatrics',
                ],
            ],
            [
                'name'              => 'Nurse',
                'registration_body' => 'NMC',
                'specialties'       => [
                    'Adult Nursing', 'Mental Health Nursing', 'Learning Disability Nursing',
                    'Children\'s Nursing', 'District Nursing', 'Health Visiting',
                    'Critical Care', 'Theatre', 'Community Nursing',
                ],
            ],
            [
                'name'              => 'Pharmacist',
                'registration_body' => 'GPhC',
                'specialties'       => [
                    'Clinical Pharmacy', 'Community Pharmacy', 'Hospital Pharmacy',
                    'Mental Health Pharmacy', 'Oncology Pharmacy',
                ],
            ],
            [
                'name'              => 'Physiotherapist',
                'registration_body' => 'HCPC',
                'specialties'       => [
                    'Musculoskeletal', 'Neurological', 'Respiratory', 'Paediatric',
                    'Sports & Exercise', 'Elderly Care',
                ],
            ],
            [
                'name'              => 'Radiographer',
                'registration_body' => 'HCPC',
                'specialties'       => [
                    'Diagnostic Imaging', 'Therapeutic Radiography', 'MRI', 'CT', 'Ultrasound',
                ],
            ],
            [
                'name'              => 'Occupational Therapist',
                'registration_body' => 'HCPC',
                'specialties'       => [
                    'Physical Rehabilitation', 'Mental Health OT', 'Paediatric OT',
                    'Community OT', 'Social Care',
                ],
            ],
            [
                'name'              => 'Speech and Language Therapist',
                'registration_body' => 'HCPC',
                'specialties'       => [
                    'Paediatric Speech Therapy', 'Adult Acquired Disorders',
                    'Dysphagia', 'Autism Spectrum',
                ],
            ],
            [
                'name'              => 'Healthcare Assistant',
                'registration_body' => null,
                'specialties'       => [
                    'Ward Based', 'Community', 'Theatre Support', 'Elderly Care', 'Mental Health Support',
                ],
            ],
            [
                'name'              => 'Midwife',
                'registration_body' => 'NMC',
                'specialties'       => [
                    'Community Midwifery', 'Labour Ward', 'Postnatal Care', 'High Risk Obstetrics',
                ],
            ],
            [
                'name'              => 'Biomedical Scientist',
                'registration_body' => 'HCPC',
                'specialties'       => [
                    'Haematology', 'Biochemistry', 'Microbiology', 'Immunology',
                    'Cellular Pathology', 'Transfusion Science',
                ],
            ],
            [
                'name'              => 'Other',
                'registration_body' => null,
                'specialties'       => [],
            ],
        ];

        foreach ($professions as $data) {
            $profession = Profession::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'              => $data['name'],
                    'registration_body' => $data['registration_body'],
                    'active'            => true,
                ]
            );

            foreach ($data['specialties'] as $specialtyName) {
                Specialty::firstOrCreate(
                    [
                        'profession_id' => $profession->id,
                        'slug'          => Str::slug($specialtyName),
                    ],
                    ['name' => $specialtyName, 'active' => true]
                );
            }
        }
    }
}
