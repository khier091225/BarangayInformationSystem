<?php

namespace Database\Factories;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'household_id'   => Household::factory(),
            'first_name'     => fake()->firstName(),
            'middle_name'    => fake()->lastName(),
            'last_name'      => fake()->lastName(),
            'birthdate'      => fake()->dateTimeBetween('-75 years', '-1 year')->format('Y-m-d'),
            'gender'         => fake()->randomElement(['Male', 'Female']),
            'civil_status'   => fake()->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
            'address'        => fake()->streetAddress(),
            'contact_number' => fake()->numerify('09#########'),
            'is_voter'       => fake()->boolean(75),
        ];
    }
}