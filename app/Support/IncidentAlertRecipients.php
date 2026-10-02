<?php

namespace App\Support;

use App\Models\IncidentAlertContact;

class IncidentAlertRecipients
{
    /** @return array{phone: ?string, email: ?string, source: 'database'|'environment'} */
    public function forTeam(string $team): array
    {
        $contact = IncidentAlertContact::query()->where('team', $team)->first();

        if ($contact !== null) {
            return [
                'phone' => $contact->is_active && $contact->sms_enabled ? $contact->phone : null,
                'email' => $contact->is_active && $contact->email_enabled ? $contact->email : null,
                'source' => 'database',
            ];
        }

        $fallback = config("incident_reports.alerts.{$team}", []);

        return [
            'phone' => filled($fallback['phone'] ?? null) ? $fallback['phone'] : null,
            'email' => filled($fallback['email'] ?? null) ? $fallback['email'] : null,
            'source' => 'environment',
        ];
    }

    public function hasConfiguredChannel(string $team): bool
    {
        $recipients = $this->forTeam($team);

        return filled($recipients['phone']) || filled($recipients['email']);
    }
}
