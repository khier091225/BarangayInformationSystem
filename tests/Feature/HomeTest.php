<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_is_accessible_to_guests(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Barangay Kay-Anlog')
            ->assertSee('Information &amp; E-Services Portal', false)
            ->assertSee('Barangay Clearance')
            ->assertSee('Certificate of Indigency')
            ->assertSee('Certificate of Residency')
            ->assertSee('Blotter &amp; Incident Reporting', false)
            ->assertSee('Emergency Hotlines');
    }

    public function test_portal_route_redirects_to_public_home(): void
    {
        $response = $this->get('/portal');

        $response->assertRedirect(route('home'));
    }

    public function test_public_homepage_displays_officials_and_statistics(): void
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->create(['household_id' => $household->id]);
        $official = Official::factory()->create([
            'name' => 'Hon. Roberto Mendoza',
            'position' => 'Punong Barangay',
            'contact_number' => '09181234567',
        ]);
        Certificate::factory()->create([
            'resident_id' => $resident->id,
            'certificate_type' => 'Barangay Clearance',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Hon. Roberto Mendoza')
            ->assertSee('Punong Barangay')
            ->assertSee('09181234567')
            ->assertSee('Community At A Glance');
    }

    public function test_authenticated_staff_sees_dashboard_shortcut(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertOk()
            ->assertSee('Staff Dashboard')
            ->assertSee(route('dashboard'));
    }

    public function test_authenticated_resident_sees_portal_shortcut(): void
    {
        $resident = Resident::factory()->create();
        $residentUser = User::factory()->create([
            'role' => 'resident',
            'resident_id' => $resident->id,
        ]);

        $response = $this->actingAs($residentUser)->get(route('home'));

        $response->assertOk()
            ->assertSee('Resident Portal')
            ->assertSee(route('account'));
    }
}
