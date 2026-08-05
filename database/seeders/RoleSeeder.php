<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [

            'Administrador' => Permission::pluck('name')->toArray(),

            'Supervisor' => [
                'dashboard.view',

                'employees.view',
                'employees.create',
                'employees.edit',

                'attendance.view',
                'attendance.register',
                'attendance.edit',

                'reports.view',
                'reports.export',
            ],

            'Consulta' => [
                'dashboard.view',

                'employees.view',

                'attendance.view',

                'reports.view',
            ],

        ];

        foreach ($roles as $roleName => $permissions) {

            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissions);

        }
    }
}