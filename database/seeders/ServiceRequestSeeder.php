<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Resident::query()->whereHas('user', function (Builder $query): void {
            $query->where('role', 'resident');
        })->limit(5)->get()->each(function (Resident $resident): void {
            ServiceRequest::factory()->for($resident)->create();
        });
    }
}
