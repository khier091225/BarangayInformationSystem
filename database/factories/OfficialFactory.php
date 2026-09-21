<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'term_start' => now()->startOfYear()->toDateString(),
            'term_end' => fn (array $attributes): string => Carbon::parse($attributes['term_start'])->addYears(3)->subDay()->toDateString(),
        ];
    }
}
