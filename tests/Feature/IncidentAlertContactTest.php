<?php

namespace Tests\Feature;

use App\Http\PhilSms;
use App\Jobs\SendIncidentReportAlert;
use App\Mail\IncidentReportAlert;
use App\Models\IncidentAlertContact;
use App\Models\IncidentReport;
use App\Models\Resident;
use App\Models\User;
use App\Support\IncidentAlertRecipients;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class IncidentAlertContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_move_an_environment_contact_to_the_database_and_changes_are_audited(): void
    {
        config()->set('incident_reports.alerts.tanod.phone', '09912197679');
        config()->set('incident_reports.alerts.tanod.email', 'fallback@example.com');
        $staff = User::factory()->create();

        $this->actingAs($staff)->get(route('incident-report-contacts.index'))
            ->assertOk()
            ->assertSee('Incident alert contacts')
            ->assertSee('Default settings')
            ->assertSee('09912197679')
            ->assertSee('fallback@example.com');

        $this->patch(route('incident-report-contacts.update', 'tanod'), [
            'contact_name' => 'Night Duty Officer',
            'phone' => '+63 991-219-7679',
            'email' => ' DUTY@EXAMPLE.COM ',
            'sms_enabled' => '1',
            'email_enabled' => '1',
            'is_active' => '1',
        ])->assertRedirect(route('incident-report-contacts.index').'#contact-tanod')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('incident_alert_contacts', [
            'team' => 'tanod',
            'contact_name' => 'Night Duty Officer',
            'phone' => '09912197679',
            'email' => 'duty@example.com',
            'sms_enabled' => true,
            'email_enabled' => true,
            'is_active' => true,
            'updated_by' => $staff->id,
        ]);
        $this->assertDatabaseHas('incident_alert_contact_changes', [
            'team' => 'tanod',
            'changed_by' => $staff->id,
        ]);
        $change = DB::table('incident_alert_contact_changes')->sole();
        $this->assertContains('phone', json_decode($change->changed_fields, true, flags: JSON_THROW_ON_ERROR));

        $this->get(route('incident-report-contacts.index'))
            ->assertOk()
            ->assertSee('Saved contact')
            ->assertSee('Night Duty Officer')
            ->assertSee('duty@example.com')
            ->assertSee('Recent contact changes');

        $this->patch(route('incident-report-contacts.update', 'tanod'), [
            'contact_name' => 'Night Duty Officer',
            'phone' => '09912197679',
            'email' => 'duty@example.com',
            'sms_enabled' => '1',
            'email_enabled' => '1',
            'is_active' => '1',
        ])->assertSessionHas('warning');

        $this->assertSame(1, DB::table('incident_alert_contact_changes')->count());
    }

    public function test_contact_validation_requires_valid_details_for_enabled_channels(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->from(route('incident-report-contacts.index'))
            ->patch(route('incident-report-contacts.update', 'tanod'), [
                'team_key' => 'tanod',
                'contact_name' => 'Duty Officer',
                'phone' => '12345',
                'email' => 'invalid',
                'sms_enabled' => '1',
                'email_enabled' => '1',
                'is_active' => '1',
            ])->assertRedirect(route('incident-report-contacts.index'))
            ->assertSessionHasErrors(['phone', 'email']);

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('incident-report-contacts.index'))
            ->assertSee('aria-describedby="tanod_phone-error"', false)
            ->assertSee('id="tanod_phone-error"', false)
            ->assertSee('aria-describedby="tanod_email-error"', false)
            ->assertSee('id="tanod_email-error"', false);

        $this->from(route('incident-report-contacts.index'))
            ->patch(route('incident-report-contacts.update', 'tanod'), [
                'team_key' => 'tanod',
                'sms_enabled' => '0',
                'email_enabled' => '0',
                'is_active' => '1',
            ])->assertSessionHasErrors('is_active');

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('incident-report-contacts.index'))
            ->assertSee('aria-describedby="tanod_is_active-error"', false)
            ->assertSee('id="tanod_is_active-error"', false);

        $this->assertDatabaseEmpty('incident_alert_contacts');
    }

    public function test_database_contact_receives_alerts_instead_of_the_environment_fallback(): void
    {
        Mail::fake();
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response(['status' => 'success'])]);
        config()->set('services.philsms.url', 'https://dashboard.philsms.com/api/v3');
        config()->set('services.philsms.token', 'test-token');
        config()->set('services.philsms.sender_id', 'Barangay');
        config()->set('incident_reports.alerts.tanod.phone', '09910000000');
        config()->set('incident_reports.alerts.tanod.email', 'fallback@example.com');
        IncidentAlertContact::factory()->create([
            'team' => 'tanod',
            'phone' => '09912197679',
            'email' => 'database@example.com',
        ]);
        $report = IncidentReport::factory()->create(['suggested_team' => 'tanod']);

        (new SendIncidentReportAlert($report->id))->handle(app(PhilSms::class), app(IncidentAlertRecipients::class));

        Http::assertSent(fn (Request $request): bool => $request['recipient'] === '639912197679');
        Mail::assertSent(IncidentReportAlert::class, fn (IncidentReportAlert $mail): bool => $mail->hasTo('database@example.com'));
        Mail::assertNotSent(IncidentReportAlert::class, fn (IncidentReportAlert $mail): bool => $mail->hasTo('fallback@example.com'));
    }

    public function test_inactive_database_contact_overrides_the_environment_fallback_and_prevents_dispatch(): void
    {
        Queue::fake();
        config()->set('incident_reports.alerts.tanod.phone', '09912197679');
        config()->set('incident_reports.alerts.tanod.email', 'fallback@example.com');
        IncidentAlertContact::factory()->create(['team' => 'tanod', 'is_active' => false]);
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.incidents.store'), [
            'category' => 'noise_complaint',
            'description' => 'There is loud music near the covered court.',
            'location' => 'Purok 3',
            'occurred_at' => now('Asia/Manila')->subHour()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        Queue::assertNotPushed(SendIncidentReportAlert::class);
    }

    public function test_guests_and_residents_cannot_manage_incident_alert_contacts(): void
    {
        $payload = ['sms_enabled' => '0', 'email_enabled' => '0', 'is_active' => '0'];

        $this->get(route('incident-report-contacts.index'))->assertRedirect(route('login'));
        $this->patch(route('incident-report-contacts.update', 'tanod'), $payload)->assertRedirect(route('login'));

        $resident = User::factory()->resident()->create();
        $this->actingAs($resident)->get(route('incident-report-contacts.index'))->assertRedirect(route('account'));
        $this->patchJson(route('incident-report-contacts.update', 'tanod'), $payload)->assertForbidden();

        $this->assertDatabaseEmpty('incident_alert_contacts');
    }
}
