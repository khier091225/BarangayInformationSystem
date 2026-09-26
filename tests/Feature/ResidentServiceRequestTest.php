<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('File a blotter report');
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
        $this->get(route('account'))->assertOk()->assertSee('Certificate of Residency')->assertSee('Under review');
        $this->get(route('account.requests.show', $serviceRequest))->assertOk()->assertSee('Scholarship application');
    }

    public function test_blotter_request_is_validated_and_waits_for_staff_review(): void
    {
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->resident()->create(['resident_id' => $resident->id]));

        $this->post(route('account.requests.blotter.store'), [
            'respondent' => '',
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
}
