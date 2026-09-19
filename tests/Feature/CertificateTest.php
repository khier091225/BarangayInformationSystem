<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificates_index_page_can_be_rendered(): void
    {
        $resident = Resident::factory()->create();
        Certificate::factory()->count(2)->create([
            'resident_id' => $resident->id,
        ]);

        $response = $this->get(route('certificates.index'));

        $response->assertOk();
        $response->assertSee('Certificates & Clearances', false);
    }

    public function test_certificate_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('certificates.create'));

        $response->assertOk();
        $response->assertSee('Issue New Certificate / Clearance');
    }

    public function test_new_certificate_can_be_issued(): void
    {
        $resident = Resident::factory()->create();

        $data = [
            'resident_id' => $resident->id,
            'certificate_type' => 'Barangay Clearance',
            'purpose' => 'Job Application',
            'date_issued' => '2026-09-18',
        ];

        $response = $this->post(route('certificates.store'), $data);

        $this->assertDatabaseHas('certificates', [
            'resident_id' => $resident->id,
            'certificate_type' => 'Barangay Clearance',
            'purpose' => 'Job Application',
        ]);

        $certificate = Certificate::latest('id')->first();
        $response->assertRedirect(route('certificates.show', $certificate));
    }

    public function test_certificate_printable_view_can_be_rendered(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Graciano',
            'last_name' => 'Lopez-Jaena',
        ]);

        $certificate = Certificate::factory()->create([
            'resident_id' => $resident->id,
            'certificate_type' => 'Certificate of Residency',
            'purpose' => 'Bank Account Requirement',
        ]);

        $response = $this->get(route('certificates.show', $certificate));

        $response->assertOk();
        $response->assertSee('GRACIANO');
        $response->assertSee('Certificate of Residency');
        $response->assertSee('BANK ACCOUNT REQUIREMENT');
        $this->assertStringStartsWith('<!DOCTYPE html>', ltrim($response->getContent()));
        $this->assertMatchesRegularExpression('/<title>\s*'.preg_quote($certificate->certificate_type.' - '.$resident->full_name, '/').'\s*<\/title>/', $response->getContent());
        $response->assertDontSee('@endsection', false);
    }

    public function test_certificate_can_be_deleted(): void
    {
        $resident = Resident::factory()->create();
        $certificate = Certificate::factory()->create([
            'resident_id' => $resident->id,
        ]);

        $response = $this->delete(route('certificates.destroy', $certificate));

        $response->assertRedirect(route('certificates.index'));
        $this->assertDatabaseMissing('certificates', [
            'id' => $certificate->id,
        ]);
    }
}
