<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($index = 1; $index <= 20; $index++) {
            $documentNumber = '900000' . str_pad((string) $index, 4, '0', STR_PAD_LEFT);
            $employee = Employee::factory()->make([
                'document_number' => $documentNumber,
            ]);

            Employee::firstOrCreate(
                ['document_number' => $documentNumber],
                $employee->getAttributes()
            );
        }
    }
}
