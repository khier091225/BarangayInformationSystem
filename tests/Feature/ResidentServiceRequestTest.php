<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ResidentServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_resident_can_request_a_document_from_the_dashboard(): void
    {
        $resident = Resident::factory()->create();
        $user = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $this->actingAs($user);

        $this->get(route('account'))->assertOk()
            ->assertSee('Request a document')
            ->assertSee('File a blotter report')
            ->assertSee('Recent activity')
            ->assertViewHas('latestReviewedRequest', null)
            ->assertViewHas('requestCounts', ['total' => 0, 'pending' => 0, 'completed' => 0, 'declined' => 0]);
        $this->get(route('account.requests.certificate.create'))->assertOk()
            ->assertSee('Business Clearance');

        $this->post(route('account.requests.certificate.store'), [
            'resident_id' => Resident::factory()->create()->id,
            'certificate_type' => 'Certificate of Residency',
            'purpose' => 'Scholarship application',
            'status' => 'Completed',
        ])->assertRedirect();

        $serviceRequest = ServiceRequest::query()->firstOrFail();
        $this->assertSame($resident->id, $serviceRequest->resident_id);
        $this->assertSame(ServiceRequest::TYPE_CERTIFICATE, $serviceRequest->type);
        $this->assertSame(ServiceRequest::STATUS_PENDING, $serviceRequest->status);
        $this->assertSame(0, Certificate::query()->count());
        $this->get(route('account'))->assertOk()->assertSee('Certificate of Residency')->assertSee('Pending');
        $this->get(route('account.requests.show', $serviceRequest))->assertOk()->assertSee('Scholarship application');
    }

    public function test_blotter_request_is_validated_and_waits_for_staff_review(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.requests.blotter.store'), [
            'respondent' => str_repeat('a', 256),
            'incident' => 'Short',
            'incident_date' => today()->addDay()->toDateString(),
        ])->assertSessionHasErrors(['respondent', 'incident', 'incident_date']);

        $this->post(route('account.requests.blotter.store'), [
            'respondent' => 'Another Person',
            'incident' => 'An incident happened near the barangay hall.',
            'incident_date' => today()->toDateString(),
            'status' => 'Settled',
        ])->assertRedirect();

        $serviceRequest = ServiceRequest::query()->firstOrFail();
        $this->assertSame(ServiceRequest::TYPE_BLOTTER, $serviceRequest->type);
        $this->assertSame(ServiceRequest::STATUS_PENDING, $serviceRequest->status);
        $this->assertSame(0, Blotter::query()->count());
        $this->get(route('account.requests.index'))->assertOk()->assertSee('Blotter report');
    }

    public function test_resident_can_follow_the_progress_of_their_completed_blotter_request(): void
    {
        $resident = Resident::factory()->create();
        $staff = User::factory()->create();
        $blotter = Blotter::factory()->create([
            'status' => Blotter::STATUS_SCHEDULED,
            'assigned_to' => $staff->id,
            'accepted_at' => now()->subDay(),
            'hearing_at' => now()->addDay(),
        ]);
        $blotter->updates()->create([
            'user_id' => $staff->id,
            'status' => Blotter::STATUS_ACCEPTED,
            'message' => 'Your case was accepted for mediation.',
        ]);
        $blotter->updates()->create([
            'user_id' => $staff->id,
            'status' => Blotter::STATUS_SCHEDULED,
            'message' => 'Please attend the scheduled mediation.',
        ]);
        $serviceRequest = ServiceRequest::factory()->blotter()->for($resident)->create([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'blotter_id' => $blotter->id,
        ]);
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->get(route('account.requests.show', $serviceRequest))->assertOk()
            ->assertSee('Blotter case progress')
            ->assertSee('Scheduled')
            ->assertSee('Your case was accepted for mediation.')
            ->assertSee('Please attend the scheduled mediation.');
    }

    public function test_resident_can_file_a_blotter_with_only_the_incident_description(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->get(route('account.requests.blotter.create'))
            ->assertOk()
            ->assertSee('Add person involved or incident date');
        $this->post(route('account.requests.blotter.store'), [
            'incident' => 'A dispute happened near the barangay hall today.',
        ])->assertRedirect();

        $request = ServiceRequest::query()->sole();
        $this->assertSame('Unknown', $request->respondent);
        $this->assertSame(today('Asia/Manila')->toDateString(), $request->incident_date->toDateString());
    }

    public function test_only_verified_residents_can_submit_and_only_owners_can_view_requests(): void
    {
        $resident = Resident::factory()->create();
        $owner = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $serviceRequest = ServiceRequest::factory()->for($resident)->create();

        $this->get(route('account.requests.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())
            ->get(route('account.requests.index'))->assertRedirect(route('dashboard'));
        $this->actingAs(User::factory()->resident()->create())
            ->get(route('account.requests.index'))->assertRedirect(route('account'));
        $this->postJson(route('account.requests.certificate.store'), [
            'certificate_type' => 'Barangay Clearance', 'purpose' => 'Work',
        ])->assertForbidden();

        $otherResident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $otherResident->id]))
            ->get(route('account.requests.show', $serviceRequest))->assertNotFound();
        $this->actingAs($owner)->get(route('account.requests.show', $serviceRequest))->assertOk();
    }

    public function test_document_request_rejects_unsupported_types_and_missing_purpose(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.requests.certificate.store'), [
            'certificate_type' => 'Passport',
            'purpose' => '',
        ])->assertSessionHasErrors(['certificate_type', 'purpose']);

        $this->assertSame(0, ServiceRequest::query()->count());
    }

    public function test_dashboard_shows_recent_owned_activity_and_latest_staff_response(): void
    {
        $this->freezeTime();
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));
        ServiceRequest::factory()->for($resident)->count(2)->create([
            'created_at' => now()->subHours(2), 'updated_at' => now()->subHours(2),
        ]);
        $completed = ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMinute(),
            'reviewed_at' => now()->subMinute(),
            'response_note' => 'Please visit the barangay hall for collection.',
        ]);
        ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_DECLINED,
            'created_at' => now()->subDays(4), 'updated_at' => now()->subDays(2),
            'reviewed_at' => now()->subDays(2),
        ]);
        $otherRequest = ServiceRequest::factory()->create([
            'status' => ServiceRequest::STATUS_COMPLETED, 'reviewed_at' => now(),
            'response_note' => 'Private update belonging to another resident.',
        ]);

        $response = $this->get(route('account'))->assertOk()
            ->assertSee('Recent activity')
            ->assertSee($completed->response_note)
            ->assertDontSee($otherRequest->response_note)
            ->assertSee(route('account.requests.show', $completed), false)
            ->assertSee(route('account.requests.index', ['status' => 'Declined']), false);

        $this->assertSame(['total' => 4, 'pending' => 2, 'completed' => 1, 'declined' => 1], $response->viewData('requestCounts'));
        $this->assertTrue($response->viewData('latestReviewedRequest')->is($completed));
        $this->assertCount(3, $response->viewData('recentRequests'));
        $this->assertFalse($response->viewData('recentRequests')->contains('id', $completed->id));
    }

    public function test_request_history_filters_only_owned_records_and_preserves_status_across_pages(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));
        ServiceRequest::factory()->for($resident)->count(11)->create(['status' => ServiceRequest::STATUS_COMPLETED]);
        $pending = ServiceRequest::factory()->for($resident)->create();
        $otherRequest = ServiceRequest::factory()->create(['status' => ServiceRequest::STATUS_COMPLETED]);

        $response = $this->get(route('account.requests.index', ['status' => 'Completed']))->assertOk()
            ->assertViewHas('status', 'Completed')
            ->assertDontSee(route('account.requests.show', $otherRequest), false)
            ->assertDontSee(route('account.requests.show', $pending), false);
        $requests = $response->viewData('requests');

        $this->assertSame(11, $requests->total());
        $this->assertCount(10, $requests->items());
        $this->assertStringContainsString('status=Completed', $requests->nextPageUrl());
        $this->get($requests->nextPageUrl())->assertOk()
            ->assertViewHas('requests', fn (LengthAwarePaginator $page): bool => $page->total() === 11 && $page->count() === 1);
        $this->get(route('account.requests.index', ['status' => 'Declined']))->assertOk()
            ->assertSee('No declined requests')
            ->assertViewHas('requests', fn (LengthAwarePaginator $page): bool => $page->total() === 0);
    }

    public function test_document_and_blotter_buttons_open_their_own_request_filters(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));
        $documentRequest = ServiceRequest::factory()->for($resident)->create(['status' => ServiceRequest::STATUS_COMPLETED]);
        $blotterRequest = ServiceRequest::factory()->blotter()->for($resident)->create();
        ServiceRequest::factory()->create();

        $this->get(route('account.requests.certificate.create'))->assertOk()
            ->assertSee('My document requests')
            ->assertSee(route('account.requests.index', ['type' => ServiceRequest::TYPE_CERTIFICATE]), false);
        $this->get(route('account.requests.blotter.create'))->assertOk()
            ->assertSee('My blotter requests')
            ->assertSee(route('account.requests.index', ['type' => ServiceRequest::TYPE_BLOTTER]), false);

        $this->get(route('account.requests.index', ['type' => ServiceRequest::TYPE_CERTIFICATE]))->assertOk()
            ->assertSee('My document requests')
            ->assertViewHas('requests', fn (LengthAwarePaginator $page): bool => $page->total() === 1 && $page->getCollection()->sole()->is($documentRequest));

        $this->get(route('account.requests.index', ['type' => ServiceRequest::TYPE_BLOTTER, 'status' => 'Pending']))->assertOk()
            ->assertSee('My blotter requests')
            ->assertSee(route('account.requests.index', ['type' => ServiceRequest::TYPE_BLOTTER, 'status' => 'Completed']))
            ->assertViewHas('requests', fn (LengthAwarePaginator $page): bool => $page->total() === 1 && $page->getCollection()->sole()->is($blotterRequest));

        $this->get(route('account.requests.index', ['type' => ServiceRequest::TYPE_BLOTTER, 'status' => 'Completed']))->assertOk()
            ->assertSee('No completed blotter requests');
    }

    public function test_request_history_rejects_an_unknown_status(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->getJson(route('account.requests.index', ['status' => 'Unknown']))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->getJson(route('account.requests.index', ['type' => 'unknown']))
            ->assertUnprocessable()->assertJsonValidationErrors('type');
    }
}
