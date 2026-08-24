<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Define Permissions ---
        $permissions = [
            // Curriculum
            'manage majors',
            'manage subjects',
            'manage sub_subjects',
            // Questions
            'manage questions',
            'approve questions',
            'upload question pdfs',
            // Exams
            'create exams',
            'view exams',
            'take exams',
            // Users
            'manage users',
            'view student progress',
            // AI
            'trigger ai jobs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Create Roles & assign permissions ---

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'manage majors',
            'manage subjects',
            'manage sub_subjects',
            'manage questions',
            'approve questions',
            'upload question pdfs',
            'view exams',
            'view student progress',
            'trigger ai jobs',
        ]);

        $student = Role::firstOrCreate(['name' => 'student']);
        $student->givePermissionTo([
            'create exams',
            'view exams',
            'take exams',
        ]);

        // --- Seed default Super Admin user ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartprep.test'],
            [
                'name'              => 'Super Admin',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($superAdmin);

        // --- Seed demo teacher ---
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@smartprep.test'],
            [
                'name'              => 'Demo Teacher',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $teacherUser->assignRole($teacher);

        // --- Seed demo student ---
        $studentUser = User::firstOrCreate(
            ['email' => 'student@smartprep.test'],
            [
                'name'              => 'Demo Student',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $studentUser->assignRole($student);

        $this->command->info('✅ Roles, permissions, and default users seeded successfully.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super_admin', 'admin@smartprep.test',   'password'],
                ['teacher',     'teacher@smartprep.test', 'password'],
                ['student',     'student@smartprep.test', 'password'],
            ]
        );
    }
}
