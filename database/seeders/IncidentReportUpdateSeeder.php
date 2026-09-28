<?php

namespace Database\Seeders;

use App\Models\IncidentReport;
use App\Models\IncidentReportUpdate;
use Illuminate\Database\Seeder;

class IncidentReportUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IncidentReport::query()->limit(3)->get()->each(function (IncidentReport $report): void {
            IncidentReportUpdate::factory()->for($report, 'report')->create();
        });
    }
}
