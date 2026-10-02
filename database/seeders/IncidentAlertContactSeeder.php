<?php

namespace Database\Seeders;

use App\Models\IncidentAlertContact;
use App\Models\IncidentReport;
use Illuminate\Database\Seeder;

class IncidentAlertContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (IncidentReport::TEAM_LABELS as $team => $label) {
            $fallback = config("incident_reports.alerts.{$team}", []);
            $phone = filled($fallback['phone'] ?? null) ? $fallback['phone'] : null;
            $email = filled($fallback['email'] ?? null) ? $fallback['email'] : null;

            if ($phone === null && $email === null) {
                continue;
            }

            IncidentAlertContact::query()->firstOrCreate(
                ['team' => $team],
                [
                    'contact_name' => $label.' duty contact',
                    'phone' => $phone,
                    'email' => $email,
                    'sms_enabled' => $phone !== null,
                    'email_enabled' => $email !== null,
                    'is_active' => true,
                ],
            );
        }
    }
}
