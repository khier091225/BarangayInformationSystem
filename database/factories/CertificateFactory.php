<?php

namespace Database\Factories;

use App\Models\Resident;
use App\Support\CertificateFees;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    public function definition(): array
    {
        $certificateType = fake()->randomElement(CertificateFees::types());

        return [
            'resident_id' => Resident::factory(),
            'certificate_type' => $certificateType,
            'purpose' => fake()->randomElement([
                'Job Application',
                'Scholarship',
                'Postal ID',
                'Bank Account Opening',
            ]),
            'fee' => CertificateFees::amountFor($certificateType),
            'date_issued' => today()->toDateString(),
        ];
    }
}
