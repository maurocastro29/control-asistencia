<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::insert([

            [
                'name' => 'Gerente',
                'description' => 'Gerencia General',
                'is_active' => true,
            ],

            [
                'name' => 'Supervisor',
                'description' => 'Supervisor de área',
                'is_active' => true,
            ],

            [
                'name' => 'Operario',
                'description' => 'Operario de producción',
                'is_active' => true,
            ],

            [
                'name' => 'Auxiliar Administrativo',
                'description' => 'Auxiliar administrativo',
                'is_active' => true,
            ],

            [
                'name' => 'Vendedor',
                'description' => 'Área comercial',
                'is_active' => true,
            ],

        ]);
    }
}