<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateIncidentAlertContactRequest;
use App\Models\IncidentAlertContact;
use App\Models\IncidentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IncidentAlertContactController extends Controller
{
    public function index(): View
    {
        $contacts = IncidentAlertContact::query()->with('updater')->get()->keyBy('team');
        $contactCards = collect(IncidentReport::TEAM_LABELS)->map(function (string $label, string $team) use ($contacts): array {
            $contact = $contacts->get($team);
            $fallback = config("incident_reports.alerts.{$team}", []);
            $phone = $contact === null
                ? (filled($fallback['phone'] ?? null) ? $fallback['phone'] : null)
                : $contact->phone;
            $email = $contact === null
                ? (filled($fallback['email'] ?? null) ? $fallback['email'] : null)
                : $contact->email;

            return [
                'team' => $team,
                'label' => $label,
                'contact' => $contact,
                'source' => $contact === null ? 'environment' : 'database',
                'values' => [
                    'contact_name' => $contact?->contact_name,
                    'phone' => $phone,
                    'email' => $email,
                    'sms_enabled' => $contact?->sms_enabled ?? filled($phone),
                    'email_enabled' => $contact?->email_enabled ?? filled($email),
                    'is_active' => $contact?->is_active ?? (filled($phone) || filled($email)),
                ],
            ];
        });

        $recentChanges = DB::table('incident_alert_contact_changes as changes')
            ->leftJoin('users', 'users.id', '=', 'changes.changed_by')
            ->select(['changes.team', 'changes.changed_fields', 'changes.created_at', 'users.name as changed_by_name'])
            ->latest('changes.id')
            ->limit(8)
            ->get()
            ->map(function (object $change): object {
                $change->team_label = IncidentReport::TEAM_LABELS[$change->team] ?? $change->team;
                $change->changed_fields = json_decode($change->changed_fields, true, flags: JSON_THROW_ON_ERROR);
                $change->changed_at = Carbon::parse($change->created_at);

                return $change;
            });

        return view('incident-reports.staff.contacts', compact('contactCards', 'recentChanges'));
    }

    public function update(UpdateIncidentAlertContactRequest $request, string $team): RedirectResponse
    {
        abort_unless(array_key_exists($team, IncidentReport::TEAM_LABELS), 404);
        $validated = $request->validated();
        $trackedFields = ['contact_name', 'phone', 'email', 'sms_enabled', 'email_enabled', 'is_active'];
        $values = array_intersect_key($validated, array_flip($trackedFields));

        $changed = DB::transaction(function () use ($request, $team, $trackedFields, $values): bool {
            $contact = IncidentAlertContact::query()->where('team', $team)->lockForUpdate()->first()
                ?? new IncidentAlertContact(['team' => $team]);
            $before = $contact->exists ? $contact->only($trackedFields) : null;

            $contact->fill($values);

            if ($contact->exists && ! $contact->isDirty($trackedFields)) {
                return false;
            }

            $contact->updated_by = $request->user()->id;
            $contact->save();
            $after = $contact->only($trackedFields);
            $changedFields = array_values(array_filter(
                $trackedFields,
                fn (string $field): bool => $before === null || $before[$field] !== $after[$field],
            ));

            DB::table('incident_alert_contact_changes')->insert([
                'incident_alert_contact_id' => $contact->id,
                'team' => $team,
                'changed_by' => $request->user()->id,
                'before_values' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
                'after_values' => json_encode($after, JSON_THROW_ON_ERROR),
                'changed_fields' => json_encode($changedFields, JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);

            return true;
        });

        return redirect()->to(route('incident-report-contacts.index').'#contact-'.$team)
            ->with($changed ? 'success' : 'warning', $changed
                ? IncidentReport::TEAM_LABELS[$team].' alert contact updated.'
                : 'No contact changes were found.');
    }
}
