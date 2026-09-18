<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BlotterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'complainant' => fake()->name(),
            'respondent' => fake()->name(),
            'incident' => fake()->sentence(10),
            'incident_date' => fake()->date(),
            'status' => fake()->randomElement(['Pending', 'Settled', 'Dismissed']),
        ];
    }
}
