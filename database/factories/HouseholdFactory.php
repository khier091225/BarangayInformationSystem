<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HouseholdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'household_number' => 'HH-'.now()->year.'-'.fake()->unique()->numerify('####'),
            'household_head' => fake()->name(),
            'address' => fake()->streetAddress().', Purok '.fake()->numberBetween(1, 7),
        ];
    }
}
