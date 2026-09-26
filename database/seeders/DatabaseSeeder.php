<?php

namespace Database\Seeders;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Barangay Admin',
            'email' => 'jannyca@khier.com',
            'role' => 'staff',
        ]);

        Household::factory(10)->create()->each(function (Household $household): void {
            $head = Resident::factory()->create([
                'household_id' => $household->id,
                'address' => $household->address,
                'birthdate' => fake()->dateTimeBetween('-75 years', '-18 years')->format('Y-m-d'),
            ]);

            $household->update(['household_head' => $head->full_name]);

            Resident::factory(fake()->numberBetween(2, 4))->create([
                'household_id' => $household->id,
                'address' => $household->address,
            ]);
        });

        Official::factory(7)->create();

        Blotter::factory(5)->create();

        Resident::inRandomOrder()->take(5)->get()->each(function (Resident $resident): void {
            Certificate::factory(fake()->numberBetween(1, 2))->create([
                'resident_id' => $resident->id,
            ]);
        });
    }
}
