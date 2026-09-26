<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResidentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $invalidValues
     */
    #[DataProvider('invalidResidentDetails')]
    public function test_create_and_update_reject_invalid_resident_details(array $invalidValues): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Original']);
        $originalAttributes = $resident->fresh()->getAttributes();
        $data = array_replace(Resident::factory()->make(['household_id' => null])->getAttributes(), $invalidValues);

        $this->post(route('residents.store', ['role' => 'admin']), $data)->assertSessionHasErrors(array_keys($invalidValues));
        $this->put(route('residents.update', [$resident, 'role' => 'admin']), $data)->assertSessionHasErrors(array_keys($invalidValues));

        $this->assertDatabaseCount('residents', 1);
        $this->assertSame($originalAttributes, $resident->fresh()->getAttributes());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidResidentDetails(): array
    {
        return [
            'required fields' => [['first_name' => '', 'last_name' => '', 'address' => '']],
            'invalid choices' => [['gender' => 'Invalid', 'civil_status' => 'Invalid']],
            'invalid date' => [['birthdate' => 'not-a-date']],
            'future birthdate' => [['birthdate' => '2999-01-01']],
            'unknown household' => [['household_id' => 999999]],
            'invalid voter flag' => [['is_voter' => 'invalid']],
        ];
    }

    public function test_updating_a_resident_with_an_unchecked_voter_box_clears_the_flag(): void
    {
        $resident = Resident::factory()->create(['is_voter' => true]);
        $data = Resident::factory()->make(['household_id' => null])->getAttributes();
        unset($data['is_voter']);

        $this->put(route('residents.update', [$resident, 'role' => 'admin']), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('residents.index', ['role' => 'admin']));

        $this->assertFalse($resident->fresh()->is_voter);
        $this->assertNull($resident->fresh()->household_id);
    }

    public function test_residents_index_page_can_be_rendered(): void
    {
        Resident::factory()->count(3)->create();

        $response = $this->get(route('residents.index', ['role' => 'admin']));

        $response->assertOk();
        $response->assertSee('Resident Registry');
    }

    public function test_resident_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('residents.create', ['role' => 'admin']));

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

        $response = $this->post(route('residents.store', ['role' => 'admin']), $data);

        $response->assertRedirect(route('residents.index', ['role' => 'admin']));
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

        $response = $this->get(route('residents.show', [$resident, 'role' => 'admin']));

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

        $response = $this->put(route('residents.update', [$resident, 'role' => 'admin']), [
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

        $response->assertRedirect(route('residents.index', ['role' => 'admin']));
        $this->assertDatabaseHas('residents', [
            'id' => $resident->id,
            'middle_name' => 'Novicio',
            'address' => 'Updated Street, Purok 2',
        ]);
    }

    public function test_resident_can_be_deleted(): void
    {
        $resident = Resident::factory()->create();

        $response = $this->delete(route('residents.destroy', [$resident, 'role' => 'admin']));

        $response->assertRedirect(route('residents.index', ['role' => 'admin']));
        $this->assertDatabaseMissing('residents', [
            'id' => $resident->id,
        ]);
    }
}
