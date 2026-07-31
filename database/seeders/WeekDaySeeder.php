<?php

namespace Database\Seeders;

use App\Models\WeekDay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeekDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [

            [
                'name' => 'Lunes',
                'code' => 'MON',
                'order' => 1,
                'is_working_day_default' => true,
            ],

            [
                'name' => 'Martes',
                'code' => 'TUE',
                'order' => 2,
                'is_working_day_default' => true,
            ],

            [
                'name' => 'Miércoles',
                'code' => 'WED',
                'order' => 3,
                'is_working_day_default' => true,
            ],

            [
                'name' => 'Jueves',
                'code' => 'THU',
                'order' => 4,
                'is_working_day_default' => true,
            ],

            [
                'name' => 'Viernes',
                'code' => 'FRI',
                'order' => 5,
                'is_working_day_default' => true,
            ],

            [
                'name' => 'Sábado',
                'code' => 'SAT',
                'order' => 6,
                'is_working_day_default' => false,
            ],

            [
                'name' => 'Domingo',
                'code' => 'SUN',
                'order' => 7,
                'is_working_day_default' => false,
            ],

        ];

        foreach ($days as $day) {

            WeekDay::create($day);

        }
    }
}