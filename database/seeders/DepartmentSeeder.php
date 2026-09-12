<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Administración',
                'description' => 'Área administrativa',
                'is_active' => true,
            ],
            [
                'name' => 'Producción',
                'description' => 'Área de producción',
                'is_active' => true,
            ],
            [
                'name' => 'Ventas',
                'description' => 'Área comercial',
                'is_active' => true,
            ],
            [
                'name' => 'Logística',
                'description' => 'Área logística',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
