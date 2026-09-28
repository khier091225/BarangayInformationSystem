<?php

namespace Database\Factories;

use App\Models\IncidentReport;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentReport>
 */
class IncidentReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resident_id' => Resident::factory(),
            'category' => 'noise_complaint',
            'description' => 'Loud music has continued late into the night near our street.',
            'location' => 'Purok 3',
            'occurred_at' => now()->subHour(),
            'keep_identity_confidential' => false,
            'status' => IncidentReport::STATUS_SUBMITTED,
            'suggested_team' => 'tanod',
        ];
    }
}
