<?php

namespace Tests\Feature;

use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdTest extends TestCase
{
    use RefreshDatabase;

    public function test_households_index_page_can_be_rendered(): void
    {
        Household::factory()->count(3)->create();

        $response = $this->get(route('households.index'));

        $response->assertOk();
        $response->assertSee('Household Registry');
    }

    public function test_household_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('households.create'));

        $response->assertOk();
        $response->assertSee('Add New Household');
    }

    public function test_new_household_can_be_stored(): void
    {
        $data = [
            'household_number' => 'HH-2026-999',
            'household_head' => 'Emilio Aguinaldo',
            'address' => 'Kawit, Cavite St.',
        ];

        $response = $this->post(route('households.store'), $data);

        $response->assertRedirect(route('households.index'));
        $this->assertDatabaseHas('households', [
            'household_number' => 'HH-2026-999',
            'household_head' => 'Emilio Aguinaldo',
        ]);
    }

    public function test_household_details_can_be_viewed(): void
    {
        $household = Household::factory()->create([
            'household_number' => 'HH-2026-042',
            'household_head' => 'Andres Bonifacio',
        ]);

        $response = $this->get(route('households.show', $household));

        $response->assertOk();
        $response->assertSee('HH-2026-042');
        $response->assertSee('Andres Bonifacio');
    }

    public function test_household_details_can_be_updated(): void
    {
        $household = Household::factory()->create([
            'household_number' => 'HH-2026-010',
            'household_head' => 'Apolinario Mabini',
        ]);

        $response = $this->put(route('households.update', $household), [
            'household_number' => 'HH-2026-010-EDITED',
            'household_head' => 'Apolinario Mabini Sr.',
            'address' => 'Purok 5, Mabini St.',
        ]);

        $response->assertRedirect(route('households.index'));
        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'household_number' => 'HH-2026-010-EDITED',
            'household_head' => 'Apolinario Mabini Sr.',
        ]);
    }

    public function test_household_can_be_deleted(): void
    {
        $household = Household::factory()->create();

        $response = $this->delete(route('households.destroy', $household));

        $response->assertRedirect(route('households.index'));
        $this->assertDatabaseMissing('households', [
            'id' => $household->id,
        ]);
    }
}
