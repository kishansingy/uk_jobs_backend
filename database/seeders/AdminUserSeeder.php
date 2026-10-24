<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ukmedicaljobs.co.uk'],
            [
                'name'                 => 'System Admin',
                'password'             => Hash::make(env('ADMIN_SEED_PASSWORD', 'changeme_immediately')),
                'email_verified_at'    => now(),
                'onboarding_completed' => true,
                'active'               => true,
            ]
        );

        $admin->assignRole('super-admin');

        $this->command->warn('Admin user created: admin@ukmedicaljobs.co.uk — change the password immediately!');
    }
}
