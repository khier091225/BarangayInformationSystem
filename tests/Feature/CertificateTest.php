<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    #[DataProvider('supportedCertificateTypes')]
    public function test_each_supported_certificate_type_can_be_issued(string $type): void
    {
        $resident = Resident::factory()->create();

        $this->post(route('certificates.store'), [
            'resident_id' => $resident->id,
            'certificate_type' => $type,
            'purpose' => 'Employment',
            'date_issued' => '2026-09-18',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('certificates', ['certificate_type' => $type, 'resident_id' => $resident->id]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function supportedCertificateTypes(): array
    {
        return [
            'clearance' => ['Barangay Clearance'],
            'residency' => ['Certificate of Residency'],
            'indigency' => ['Certificate of Indigency'],
            'business' => ['Business Clearance'],
        ];
    }

    public function test_unsupported_certificate_type_is_rejected_and_form_input_is_preserved(): void
    {
        $resident = Resident::factory()->create();
        $data = [
            'resident_id' => $resident->id,
            'certificate_type' => 'Unsupported Certificate',
            'purpose' => 'Employment',
            'date_issued' => '2026-09-18',
        ];

        $this->from(route('certificates.create'))->post(route('certificates.store'), $data)
            ->assertRedirect(route('certificates.create'))
            ->assertSessionHasErrors('certificate_type')
            ->assertSessionHasInput('purpose', 'Employment');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_invalid_certificate_details_are_rejected(): void
    {
        $this->post(route('certificates.store'), [
            'resident_id' => 999999,
            'certificate_type' => 'Barangay Clearance',
            'purpose' => '',
            'date_issued' => 'not-a-date',
        ])->assertSessionHasErrors(['resident_id', 'purpose', 'date_issued']);

        $this->assertDatabaseCount('certificates', 0);
    }

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
        $response->assertRedirect(route('certificates.show', [$certificate]));
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

        $response = $this->get(route('certificates.show', [$certificate]));

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

        $response = $this->delete(route('certificates.destroy', [$certificate]));

        $response->assertRedirect(route('certificates.index'));
        $this->assertDatabaseMissing('certificates', [
            'id' => $certificate->id,
        ]);
    }
}
