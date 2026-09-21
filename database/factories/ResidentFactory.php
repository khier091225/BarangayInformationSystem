<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ResidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'birthdate' => fake()->dateTimeBetween('-75 years', '-1 year')->format('Y-m-d'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'civil_status' => fn (array $attributes): string => Carbon::parse($attributes['birthdate'])->age < 18
                ? 'Single'
                : fake()->randomElement(['Single', 'Married', 'Widowed', 'Separated', 'Divorced']),
            'address' => fake()->streetAddress(),
            'contact_number' => fake()->numerify('09#########'),
            'is_voter' => fn (array $attributes): bool => Carbon::parse($attributes['birthdate'])->age >= 18 && fake()->boolean(75),
        ];
    }
}
