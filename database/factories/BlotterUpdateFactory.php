<?php

namespace Database\Factories;

use App\Models\Blotter;
use App\Models\BlotterUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlotterUpdate>
 */
class BlotterUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blotter_id' => Blotter::factory(),
            'user_id' => User::factory(),
            'status' => Blotter::STATUS_ACCEPTED,
            'message' => fake()->sentence(),
        ];
    }
}
