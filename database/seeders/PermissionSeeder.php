<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            'dashboard.view',

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            /*
            |--------------------------------------------------------------------------
            | Employees
            |--------------------------------------------------------------------------
            */
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            /*
            |--------------------------------------------------------------------------
            | Departments
            |--------------------------------------------------------------------------
            */
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',

            /*
            |--------------------------------------------------------------------------
            | Positions
            |--------------------------------------------------------------------------
            */
            'positions.view',
            'positions.create',
            'positions.edit',
            'positions.delete',

            /*
            |--------------------------------------------------------------------------
            | Document Types
            |--------------------------------------------------------------------------
            */
            'document-types.view',
            'document-types.create',
            'document-types.edit',
            'document-types.delete',

            /*
            |--------------------------------------------------------------------------
            | Work Schedules
            |--------------------------------------------------------------------------
            */
            'work-schedules.view',
            'work-schedules.create',
            'work-schedules.edit',
            'work-schedules.delete',

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */
            'attendance.view',
            'attendance.register',
            'attendance.edit',
            'attendance.delete',

            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */
            'reports.view',
            'reports.export',

            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

        }
    }
}