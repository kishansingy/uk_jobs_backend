<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Job management
            'jobs.view', 'jobs.create', 'jobs.update', 'jobs.delete', 'jobs.reclassify',
            // Sources
            'sources.view', 'sources.create', 'sources.update', 'sources.delete', 'sources.import',
            // Users
            'users.view', 'users.update', 'users.delete', 'users.assign-role',
            // Content
            'content.view', 'content.create', 'content.update', 'content.delete', 'content.publish',
            // Videos
            'videos.view', 'videos.create', 'videos.update', 'videos.delete', 'videos.publish',
            // Advisors
            'advisors.view', 'advisors.manage',
            // Reports
            'reports.view', 'reports.export',
            // Subscriptions
            'subscriptions.view', 'subscriptions.manage',
            // Audit
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'jobs.view', 'jobs.create', 'jobs.update', 'jobs.delete', 'jobs.reclassify',
            'sources.view', 'sources.create', 'sources.update', 'sources.import',
            'users.view', 'users.update', 'users.assign-role',
            'content.view', 'content.create', 'content.update', 'content.publish',
            'videos.view', 'videos.create', 'videos.update', 'videos.publish',
            'advisors.view', 'advisors.manage',
            'reports.view', 'reports.export',
            'subscriptions.view',
        ]);

        // Content Manager
        $content = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'web']);
        $content->givePermissionTo([
            'content.view', 'content.create', 'content.update', 'content.publish',
            'videos.view', 'videos.create', 'videos.update', 'videos.publish',
        ]);

        // Job Manager
        $jobManager = Role::firstOrCreate(['name' => 'job-manager', 'guard_name' => 'web']);
        $jobManager->givePermissionTo([
            'jobs.view', 'jobs.create', 'jobs.update', 'jobs.reclassify',
            'sources.view', 'sources.import',
        ]);

        // Career Advisor
        $advisor = Role::firstOrCreate(['name' => 'career-advisor', 'guard_name' => 'web']);
        $advisor->givePermissionTo(['advisors.view']);

        // Healthcare Professional Contributor
        Role::firstOrCreate(['name' => 'contributor', 'guard_name' => 'web']);

        // Premium User
        Role::firstOrCreate(['name' => 'premium-user', 'guard_name' => 'web']);

        // Free User (default)
        Role::firstOrCreate(['name' => 'free-user', 'guard_name' => 'web']);
    }
}
