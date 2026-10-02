<?php

namespace Database\Factories;

use App\Models\IncidentAlertContact;
use App\Models\IncidentReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentAlertContact>
 */
class IncidentAlertContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team' => fake()->randomElement(array_keys(IncidentReport::TEAM_LABELS)),
            'contact_name' => fake()->name(),
            'phone' => '09'.fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'sms_enabled' => true,
            'email_enabled' => true,
            'is_active' => true,
        ];
    }
}
