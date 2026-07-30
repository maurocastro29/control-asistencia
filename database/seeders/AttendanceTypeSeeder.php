<?php

namespace Database\Seeders;

use App\Models\AttendanceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttendanceType::insert([
            [
                'name' => 'Entrada',
                'description' => 'Inicio de la jornada laboral',
                'is_active' => true,
            ],
            [
                'name' => 'Salida',
                'description' => 'Fin de la jornada laboral',
                'is_active' => true,
            ],
        ]);
    }
}
