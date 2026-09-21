<?php

namespace Database\Factories;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resident_id' => Resident::factory(),
            'certificate_type' => fake()->randomElement([
                'Barangay Clearance',
                'Certificate of Indigency',
                'Certificate of Residency',
                'Business Clearance',
            ]),
            'purpose' => fake()->randomElement([
                'Job Application',
                'Scholarship',
                'Postal ID',
                'Bank Account Opening',
            ]),
            'date_issued' => today()->toDateString(),
        ];
    }
}
