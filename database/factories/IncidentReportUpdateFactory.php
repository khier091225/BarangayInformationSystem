<?php

namespace Database\Factories;

use App\Models\IncidentReport;
use App\Models\IncidentReportUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentReportUpdate>
 */
class IncidentReportUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'incident_report_id' => IncidentReport::factory(),
            'user_id' => null,
            'status' => IncidentReport::STATUS_SUBMITTED,
            'message' => 'Report received for staff review.',
        ];
    }
}
