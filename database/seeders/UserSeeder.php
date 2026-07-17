<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administratorRole = Role::where('name', 'Administrador')->first();

        User::create([
            'role_id' => $administratorRole->id,

            'username' => 'admin',

            'first_name' => 'Administrador',
            'middle_name' => null,

            'first_last_name' => 'Sistema',
            'second_last_name' => null,

            'password' => 'admin123',

            'is_active' => true,
        ]);
    }
}