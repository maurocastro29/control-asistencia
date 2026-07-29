<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentType::insert([
            [
                'name' => 'Cédula de Ciudadanía',
                'abbreviation' => 'CC',
                'description' => 'Documento de identificación nacional para ciudadanos colombianos',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tarjeta de Identidad',
                'abbreviation' => 'TI',
                'description' => 'Documento de identificación para menores de edad en Colombia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cédula de Extranjería',
                'abbreviation' => 'CE',
                'description' => 'Documento de identificación para extranjeros residentes en Colombia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pasaporte',
                'abbreviation' => 'PA',
                'description' => 'Documento de viaje internacional que certifica la identidad y nacionalidad del titular',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Permiso por Protección Temporal',
                'abbreviation' => 'PPT',
                'description' => 'Documento que otorga protección a personas que no pueden regresar a su país de origen',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
