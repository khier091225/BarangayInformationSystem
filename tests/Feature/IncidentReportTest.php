<?php

namespace Tests\Feature;

use App\Http\PhilSms;
use App\Jobs\SendIncidentReportAlert;
use App\Mail\IncidentReportAlert;
use App\Models\Blotter;
use App\Models\IncidentReport;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IncidentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_resident_can_submit_and_track_an_incident_without_creating_a_blotter(): void
    {
        Queue::fake();
        config()->set('incident_reports.alerts.tanod.phone', null);
        config()->set('incident_reports.alerts.tanod.email', null);
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));
        $occurredAt = now('Asia/Manila')->subHour()->format('Y-m-d\TH:i');

        $this->get(route('account'))->assertOk()->assertSee('Report an incident');
        $this->get(route('account.incidents.create'))->assertOk()->assertSee('Garbage / Sanitation');
        $this->post(route('account.incidents.store'), [
            'category' => 'noise_complaint',
            'description' => 'Loud music has continued near our house after midnight.',
            'location' => 'Purok 3',
            'occurred_at' => $occurredAt,
            'keep_identity_confidential' => '1',
        ])->assertRedirect();

        $report = IncidentReport::query()->firstOrFail();
        $this->assertSame($resident->id, $report->resident_id);
        $this->assertMatchesRegularExpression('/\AINC-\d{4}-\d{6}\z/', $report->reference_number);
        $this->assertSame(IncidentReport::STATUS_SUBMITTED, $report->status);
        $this->assertSame('tanod', $report->suggested_team);
        $this->assertTrue($report->keep_identity_confidential);
        $this->assertSame($occurredAt, $report->occurred_at->timezone('Asia/Manila')->format('Y-m-d\TH:i'));
        $this->assertSame(1, $report->updates()->count());
        $this->assertSame(0, Blotter::query()->count());
        $this->get(route('account.incidents.index'))->assertOk()->assertSee($report->reference_number);
        $this->get(route('account.incidents.show', $report))->assertOk()->assertSee('Report received for staff review.');
        Queue::assertNothingPushed();
    }

    public function test_incident_submission_validates_category_date_and_description(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.incidents.store'), [
            'category' => 'unknown',
            'description' => 'Short',
            'location' => '',
            'occurred_at' => now('Asia/Manila')->addDay()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors(['category', 'description', 'location', 'occurred_at']);
        $this->assertSame(0, IncidentReport::query()->count());
    }

    public function test_resident_can_submit_essential_incident_details_without_entering_a_date(): void
    {
        $this->freezeTime();
        Queue::fake();
        config()->set('incident_reports.alerts.tanod.phone', null);
        config()->set('incident_reports.alerts.tanod.email', null);
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->get(route('account.incidents.create'))
            ->assertOk()
            ->assertSee('Add date, photo, or privacy preference');
        $this->post(route('account.incidents.store'), [
            'category' => 'noise_complaint',
            'description' => 'Loud music has continued near our home.',
            'location' => 'Purok 3',
        ])->assertRedirect();

        $this->assertSame(now()->toDateTimeString(), IncidentReport::query()->sole()->occurred_at->toDateTimeString());
    }

    public function test_confidential_identity_and_evidence_are_limited_to_owner_and_assigned_staff(): void
    {
        Storage::fake('local');
        $resident = Resident::factory()->create(['first_name' => 'Confidential', 'middle_name' => null, 'last_name' => 'Resident']);
        $owner = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $otherResident = User::factory()->resident()->create(['resident_id' => Resident::factory()->create()->id]);
        $staff = User::factory()->create();
        $otherStaff = User::factory()->create();
        $this->actingAs($owner)->post(route('account.incidents.store'), [
            'category' => 'suspicious_activity',
            'description' => 'Someone has been checking doors along our street at night.',
            'location' => 'Purok 2',
            'occurred_at' => now('Asia/Manila')->subHour()->format('Y-m-d\TH:i'),
            'keep_identity_confidential' => '1',
            'evidence' => UploadedFile::fake()->image('scene.jpg'),
        ])->assertRedirect();
        $report = IncidentReport::query()->firstOrFail();
        Storage::disk('local')->assertExists($report->evidence_path);

        $this->actingAs($otherResident)->get(route('account.incidents.show', $report))->assertNotFound();
        $this->get(route('incident-reports.evidence', $report))->assertNotFound();
        $this->actingAs($staff)->get(route('incident-reports.index'))->assertOk()->assertDontSee('Confidential Resident');
        $this->get(route('incident-reports.show', $report))->assertOk()->assertDontSee('Confidential Resident')->assertDontSee('Download scene.jpg');
        $this->get(route('incident-reports.evidence', $report))->assertNotFound();

        $this->post(route('incident-reports.update', $report), ['action' => 'accept', 'team' => 'leadership'])
            ->assertRedirect(route('incident-reports.show', $report));
        $this->assertSame($staff->id, $report->fresh()->assigned_to);
        $this->get(route('incident-reports.show', $report))->assertOk()->assertSee('Confidential Resident');
        $this->get(route('incident-reports.evidence', $report))->assertOk();
        $this->actingAs($otherStaff)->get(route('incident-reports.show', $report))->assertOk()->assertDontSee('Confidential Resident');
        $this->get(route('incident-reports.evidence', $report))->assertNotFound();
        $this->postJson(route('incident-reports.update', $report), ['action' => 'advance', 'message' => 'We are looking into the reported activity.'])
            ->assertForbidden();
        $this->actingAs($owner)->get(route('account.incidents.show', $report))->assertOk()->assertSee('Barangay Captain / Authorized Staff');
        $this->get(route('incident-reports.evidence', $report))->assertOk();
    }

    public function test_assigned_staff_can_advance_each_status_without_writing_a_message(): void
    {
        $report = IncidentReport::factory()->create();
        $report->updates()->create(['status' => IncidentReport::STATUS_SUBMITTED, 'message' => 'Report received for staff review.']);
        $staff = User::factory()->create();
        $this->actingAs($staff);

        $this->post(route('incident-reports.update', $report), ['action' => 'accept', 'team' => 'tanod'])
            ->assertSessionHas('success');
        $this->assertSame(IncidentReport::STATUS_ASSIGNED, $report->fresh()->status);
        $this->get(route('incident-reports.show', $report))->assertOk()->assertSee('Add a message to the resident');

        foreach ([
            'Responding' => 'Barangay staff are responding to your report.',
            'Resolved' => 'Barangay staff marked your report as resolved.',
            'Closed' => 'Your report has been closed.',
        ] as $status => $defaultMessage) {
            $this->post(route('incident-reports.update', $report), ['action' => 'advance'])
                ->assertSessionHas('success');
            $this->assertSame($status, $report->fresh()->status);
            $this->assertSame($defaultMessage, $report->updates()->reorder()->latest('id')->firstOrFail()->message);
        }

        $this->assertSame(5, $report->updates()->count());
        $this->assertNotNull($report->fresh()->closed_at);
        $this->assertSame(0, Blotter::query()->count());
        $this->post(route('incident-reports.update', $report), [
            'action' => 'advance', 'message' => 'Another update after this report was closed.',
        ])->assertSessionHas('warning');
    }

    public function test_staff_can_add_a_short_custom_status_message_for_the_resident(): void
    {
        $staff = User::factory()->create();
        $report = IncidentReport::factory()->create([
            'status' => IncidentReport::STATUS_ASSIGNED,
            'assigned_team' => 'tanod',
            'assigned_to' => $staff->id,
        ]);
        $this->actingAs($staff)->post(route('incident-reports.update', $report), [
            'action' => 'advance',
            'message' => 'On it',
        ])->assertSessionHas('success');

        $this->assertSame('On it', $report->updates()->sole()->message);
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $report->resident_id]))
            ->get(route('account.incidents.show', $report))
            ->assertOk()
            ->assertSee('On it');
    }

    public function test_configured_team_alert_uses_existing_philsms_and_sends_no_private_details(): void
    {
        Queue::fake();
        Mail::fake();
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response(['status' => 'success'])]);
        config()->set('services.philsms.url', 'https://dashboard.philsms.com/api/v3');
        config()->set('services.philsms.token', 'test-token');
        config()->set('services.philsms.sender_id', 'Barangay');
        config()->set('incident_reports.alerts.tanod.phone', '09912197679');
        config()->set('incident_reports.alerts.tanod.email', 'duty@example.com');
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.incidents.store'), [
            'category' => 'noise_complaint',
            'description' => 'Private description with identifying details.',
            'location' => 'Private location',
            'occurred_at' => now('Asia/Manila')->subHour()->format('Y-m-d\TH:i'),
            'keep_identity_confidential' => '1',
        ])->assertRedirect();
        $report = IncidentReport::query()->firstOrFail();
        Queue::assertPushed(SendIncidentReportAlert::class, fn (SendIncidentReportAlert $job): bool => $job->connection === 'deferred');

        (new SendIncidentReportAlert($report->id))->handle(app(PhilSms::class));
        $this->assertSame('submitted', $report->fresh()->alert_sms_status);
        $this->assertSame('submitted', $report->fresh()->alert_email_status);
        Http::assertSent(fn (Request $request): bool => $request['recipient'] === '639912197679'
            && str_contains($request['message'], $report->reference_number)
            && ! str_contains($request['message'], 'Private description')
            && ! str_contains($request['message'], 'Private location'));
        Mail::assertSent(IncidentReportAlert::class, fn (IncidentReportAlert $mail): bool => $mail->referenceNumber === $report->reference_number);

        $this->actingAs(User::factory()->create())
            ->get(route('incident-reports.show', $report))
            ->assertOk()
            ->assertSee('Alerts are attempted when the resident submits the report')
            ->assertSee('Accepted by SMS service; phone delivery unverified');
    }

    public function test_alert_is_attempted_after_submission_without_a_queue_worker(): void
    {
        Mail::fake();
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response(['status' => 'success'])]);
        config()->set('services.philsms.url', 'https://dashboard.philsms.com/api/v3');
        config()->set('services.philsms.token', 'test-token');
        config()->set('services.philsms.sender_id', 'Barangay');
        config()->set('incident_reports.alerts.tanod.phone', '09912197679');
        config()->set('incident_reports.alerts.tanod.email', 'duty@example.com');
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.incidents.store'), [
            'category' => 'noise_complaint',
            'description' => 'There is loud music on our street.',
            'location' => 'Purok 3',
            'occurred_at' => now('Asia/Manila')->subHour()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $report = IncidentReport::query()->firstOrFail();
        $this->assertSame('submitted', $report->fresh()->alert_sms_status);
        $this->assertSame('submitted', $report->fresh()->alert_email_status);
        Http::assertSentCount(1);
        Mail::assertSent(IncidentReportAlert::class);
    }

    public function test_unverified_resident_and_staff_cannot_submit_incident_reports(): void
    {
        $this->get(route('account.incidents.create'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->resident()->create())
            ->get(route('account.incidents.create'))->assertRedirect(route('account'));
        $this->postJson(route('account.incidents.store'), [])->assertForbidden();
        $this->actingAs(User::factory()->create())
            ->get(route('account.incidents.create'))->assertRedirect(route('dashboard'));
        $this->assertSame(0, IncidentReport::query()->count());
    }
}
