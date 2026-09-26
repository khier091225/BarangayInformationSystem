<?php

namespace Database\Factories;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
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
            'type' => ServiceRequest::TYPE_CERTIFICATE,
            'certificate_type' => 'Certificate of Residency',
            'purpose' => fake()->sentence(4),
            'status' => ServiceRequest::STATUS_PENDING,
        ];
    }

    public function blotter(): static
    {
        return $this->state(fn (): array => [
            'type' => ServiceRequest::TYPE_BLOTTER,
            'certificate_type' => null,
            'purpose' => null,
            'respondent' => fake()->name(),
            'incident' => fake()->paragraph(),
            'incident_date' => now()->subDay()->toDateString(),
        ]);
    }
}
