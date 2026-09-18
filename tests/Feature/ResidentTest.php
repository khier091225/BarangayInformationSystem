<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentTest extends TestCase
{
    use RefreshDatabase;

    public function test_residents_index_page_can_be_rendered(): void
    {
        Resident::factory()->count(3)->create();

        $response = $this->get(route('residents.index'));

        $response->assertOk();
        $response->assertSee('Resident Registry');
    }

    public function test_resident_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('residents.create'));

        $response->assertOk();
        $response->assertSee('Register New Resident');
    }

    public function test_new_resident_can_be_stored(): void
    {
        $household = Household::factory()->create();

        $data = [
            'household_id' => $household->id,
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Clara',
            'birthdate' => '1995-05-12',
            'gender' => 'Female',
            'civil_status' => 'Single',
            'address' => 'Purok 1, Main Street',
            'contact_number' => '09171234567',
            'is_voter' => '1',
        ];

        $response = $this->post(route('residents.store'), $data);

        $response->assertRedirect(route('residents.index'));
        $this->assertDatabaseHas('residents', [
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'is_voter' => true,
        ]);
    }

    public function test_resident_profile_can_be_viewed(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Crisostomo',
            'last_name' => 'Ibarra',
        ]);

        $response = $this->get(route('residents.show', $resident));

        $response->assertOk();
        $response->assertSee('Crisostomo');
        $response->assertSee('Ibarra');
    }

    public function test_resident_details_can_be_updated(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Luna',
        ]);

        $response = $this->put(route('residents.update', $resident), [
            'first_name' => 'Juan',
            'middle_name' => 'Novicio',
            'last_name' => 'Luna',
            'birthdate' => '1980-10-23',
            'gender' => 'Male',
            'civil_status' => 'Married',
            'address' => 'Updated Street, Purok 2',
            'contact_number' => '09189998877',
            'is_voter' => '1',
        ]);

        $response->assertRedirect(route('residents.index'));
        $this->assertDatabaseHas('residents', [
            'id' => $resident->id,
            'middle_name' => 'Novicio',
            'address' => 'Updated Street, Purok 2',
        ]);
    }

    public function test_resident_can_be_deleted(): void
    {
        $resident = Resident::factory()->create();

        $response = $this->delete(route('residents.destroy', $resident));

        $response->assertRedirect(route('residents.index'));
        $this->assertDatabaseMissing('residents', [
            'id' => $resident->id,
        ]);
    }
}
