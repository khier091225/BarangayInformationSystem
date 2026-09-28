<?php

namespace Database\Seeders;

use App\Models\IncidentReport;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class IncidentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Resident::query()->whereHas('user', function (Builder $query): void {
            $query->where('role', 'resident');
        })->limit(3)->get()->each(function (Resident $resident): void {
            IncidentReport::factory()->for($resident)->create();
        });
    }
}
