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
        // 1. Admin User
        User::factory()->create([
            'name' => 'Barangay Admin',
            'email' => 'admin@barangay.test',
            'password' => bcrypt('password'),
        ]);

        // 2. 10 Households na may 3 hanggang 5 na Residente bawat bahay
        Household::factory(10)->create()->each(function ($household) {
            Resident::factory(rand(3, 5))->create([
                'household_id' => $household->id,
            ]);
        });

        // 3. 7 Barangay Officials
        Official::factory(7)->create();

        // 4. 5 Blotter Records
        Blotter::factory(5)->create();

        // 5. Certificates para sa mga random na Residente
        Resident::inRandomOrder()->take(5)->get()->each(function ($resident) {
            Certificate::factory(rand(1, 2))->create([
                'resident_id' => $resident->id,
            ]);
        });
    }
}
