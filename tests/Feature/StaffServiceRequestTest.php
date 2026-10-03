<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StaffServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_request_list_shows_status_counts_and_appropriate_actions(): void
    {
        $resident = Resident::factory()->create();
        ServiceRequest::factory()->for($resident)->count(2)->create();
        ServiceRequest::factory()->for($resident)->create(['status' => ServiceRequest::STATUS_COMPLETED]);
        $this->actingAs(User::factory()->create());

        $this->get(route('service-requests.index'))
            ->assertOk()
            ->assertViewHas('statusCounts', [
                ServiceRequest::STATUS_PENDING => 2,
                ServiceRequest::STATUS_AWAITING_PAYMENT => 0,
                ServiceRequest::STATUS_COMPLETED => 1,
                ServiceRequest::STATUS_DECLINED => 0,
            ])
            ->assertSee('Review');

        $this->get(route('service-requests.index', ['status' => ServiceRequest::STATUS_COMPLETED]))
            ->assertOk()
            ->assertSee('View details')
            ->assertDontSee('Review</a>', false);
    }

    public function test_staff_and_residents_see_the_same_philippine_submission_and_review_times(): void
    {
        $this->travelTo(Carbon::parse('2026-10-03 20:00:00', 'UTC'));
        $staff = User::factory()->create();
        $resident = Resident::factory()->create();
        $residentUser = User::factory()->resident()->for($resident)->create();
        $serviceRequest = ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'created_at' => '2026-10-03 18:30:00',
            'reviewed_at' => '2026-10-03 19:00:00',
            'reviewed_by' => $staff->id,
            'response_note' => 'Please collect the document at the barangay hall.',
        ]);

        $this->actingAs($staff)->get(route('service-requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('Submitted Oct 4, 2026 at 2:30 AM')
            ->assertSee('Oct 4, 2026 at 3:00 AM');
        $this->get(route('service-requests.index', ['status' => ServiceRequest::STATUS_COMPLETED]))
            ->assertOk()
            ->assertSee('Oct 4, 2026');

        $this->actingAs($residentUser)->get(route('account.requests.show', $serviceRequest))
            ->assertOk()
            ->assertSee('Oct 4, 2026 at 2:30 AM')
            ->assertSee('Oct 4, 2026 at 3:00 AM');
    }

    public function test_staff_completes_a_document_request_once_and_creates_a_certificate(): void
    {
        $resident = Resident::factory()->create();
        $residentUser = User::factory()->resident()->create(['resident_id' => $resident->id]);
        $serviceRequest = ServiceRequest::factory()->for($resident)->create([
            'certificate_type' => 'Certificate of Indigency',
            'fee_amount' => 0,
        ]);
        $staff = User::factory()->create();
        $this->actingAs($staff);

        $this->get(route('service-requests.index'))->assertOk()
            ->assertSee($resident->full_name);
        $this->get(route('service-requests.show', $serviceRequest))->assertOk()
            ->assertSee('Approve request');
        $this->post(route('service-requests.review', $serviceRequest), [
            'decision' => 'complete',
            'response_note' => 'Please collect the document at the barangay hall.',
        ])->assertRedirect(route('service-requests.show', $serviceRequest));

        $serviceRequest->refresh();
        $this->assertSame(ServiceRequest::STATUS_COMPLETED, $serviceRequest->status);
        $this->assertSame($staff->id, $serviceRequest->reviewed_by);
        $this->assertNotNull($serviceRequest->reviewed_at);
        $this->assertSame(1, Certificate::query()->count());
        $this->assertSame($serviceRequest->certificate_id, Certificate::query()->firstOrFail()->id);
        $this->assertSame($resident->id, Certificate::query()->firstOrFail()->resident_id);
        $this->delete(route('certificates.destroy', $serviceRequest->certificate_id))
            ->assertSessionHas('warning');
        $this->assertSame(1, Certificate::query()->count());

        $this->post(route('service-requests.review', $serviceRequest), ['decision' => 'complete'])
            ->assertSessionHas('warning');
        $this->assertSame(1, Certificate::query()->count());

        $this->actingAs($residentUser)
            ->get(route('account.requests.show', $serviceRequest))
            ->assertOk()->assertSee('Please collect the document at the barangay hall.');
    }

    public function test_staff_records_a_blotter_only_after_review(): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos']);
        $serviceRequest = ServiceRequest::factory()->blotter()->for($resident)->create();
        $this->actingAs(User::factory()->create())
            ->post(route('service-requests.review', $serviceRequest), ['decision' => 'complete'])
            ->assertRedirect(route('service-requests.show', $serviceRequest));

        $blotter = Blotter::query()->firstOrFail();
        $this->assertSame($resident->full_name, $blotter->complainant);
        $this->assertSame($serviceRequest->respondent, $blotter->respondent);
        $this->assertSame('Pending', $blotter->status);
        $this->assertSame($blotter->id, $serviceRequest->fresh()->blotter_id);
        $this->delete(route('blotters.destroy', $blotter))->assertSessionHas('warning');
        $this->assertSame(1, Blotter::query()->count());
    }

    public function test_declining_requires_a_reason_and_does_not_create_an_official_record(): void
    {
        $serviceRequest = ServiceRequest::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->post(route('service-requests.review', $serviceRequest), ['decision' => 'decline'])
            ->assertSessionHasErrors('response_note');
        $this->post(route('service-requests.review', $serviceRequest), [
            'decision' => 'decline',
            'response_note' => 'Please provide a clearer purpose.',
        ])->assertRedirect(route('service-requests.show', $serviceRequest));

        $this->assertSame(ServiceRequest::STATUS_DECLINED, $serviceRequest->fresh()->status);
        $this->assertSame(0, Certificate::query()->count());
        $this->assertSame(0, Blotter::query()->count());
        $this->get(route('service-requests.index', ['status' => 'Declined']))->assertOk();
        $this->get(route('service-requests.show', $serviceRequest))
            ->assertOk()->assertSee('Please provide a clearer purpose.');
    }

    public function test_residents_cannot_access_staff_review_routes(): void
    {
        $resident = Resident::factory()->create();
        $serviceRequest = ServiceRequest::factory()->for($resident)->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->get(route('service-requests.index'))->assertRedirect(route('account'));
        $this->get(route('service-requests.show', $serviceRequest))->assertRedirect(route('account'));
        $this->postJson(route('service-requests.review', $serviceRequest), ['decision' => 'complete'])
            ->assertForbidden();

        $this->assertSame(ServiceRequest::STATUS_PENDING, $serviceRequest->fresh()->status);
        $this->assertSame(0, Certificate::query()->count());
    }
}
