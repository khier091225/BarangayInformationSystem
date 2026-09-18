<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OfficialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => fake()->randomElement([
                'Barangay Captain',
                'Barangay Kagawad',
                'SK Chairman',
                'Barangay Secretary',
                'Barangay Treasurer',
            ]),
            'contact_number' => fake()->numerify('09#########'),
            'term_start' => '2023-11-01',
            'term_end' => '2025-11-01',
        ];
    }
}
