<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type_id' => DocumentType::inRandomOrder()->first()->id,
            'department_id'    => Department::inRandomOrder()->first()->id,
            'position_id'      => Position::inRandomOrder()->first()->id,

            'document_number' => fake()->unique()->numerify('##########'),

            'first_name' => fake()->firstName(),

            'middle_name' => fake()->boolean(50)
                ? fake()->firstName()
                : null,

            'first_last_name' => fake()->lastName(),

            'second_last_name' => fake()->boolean(50)
                ? fake()->lastName()
                : null,

            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),

            'phone' => fake()->phoneNumber(),

            'email' => fake()->safeEmail(),

            'address' => fake()->address(),

            'hire_date' => fake()->dateTimeBetween('-10 years', 'now'),

            'termination_date' => null,

            'is_active' => true,
        ];
    }
}
