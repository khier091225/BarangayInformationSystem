<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

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
        $response->assertSee('Its number will be assigned automatically when you save.');
        $response->assertDontSee('name="household_number"', false);
    }

    public function test_new_household_can_be_stored(): void
    {
        $this->travelTo(now()->setDate(2030, 6, 15));

        $data = [
            'household_head' => 'Emilio Aguinaldo',
            'address' => 'Kawit, Cavite St.',
        ];

        $response = $this->post(route('households.store'), $data);

        $response->assertRedirect(route('households.index'));
        $household = Household::sole();
        $expectedNumber = sprintf('HH-2030-%06d', $household->id);

        $response->assertSessionHas('success', "Household {$expectedNumber} added successfully!");
        $this->assertDatabaseHas('households', [
            'household_number' => $expectedNumber,
            'household_head' => 'Emilio Aguinaldo',
        ]);
    }

    public function test_household_number_cannot_be_supplied_or_changed_by_staff(): void
    {
        $response = $this->post(route('households.store'), [
            'household_number' => 'HH-OVERRIDE',
            'household_head' => 'First Household',
            'address' => 'Purok 1',
        ]);

        $response->assertRedirect(route('households.index'));
        $household = Household::sole();
        $this->assertNotSame('HH-OVERRIDE', $household->household_number);

        $this->get(route('households.edit', $household))->assertOk()
            ->assertSee($household->household_number)
            ->assertDontSee('name="household_number"', false);

        $this->put(route('households.update', $household), [
            'household_number' => 'HH-CHANGED',
            'household_head' => 'Updated Household',
            'address' => 'Purok 2',
        ])->assertRedirect(route('households.index'));

        $household->refresh();
        $this->assertSame('Updated Household', $household->household_head);
        $this->assertNotSame('HH-CHANGED', $household->household_number);
    }

    public function test_generated_number_does_not_replace_an_existing_household_number(): void
    {
        $this->travelTo(now()->setDate(2030, 6, 15));

        $existing = Household::factory()->create();
        $reservedNumber = sprintf('HH-2030-%06d', $existing->id + 1);
        $existing->update(['household_number' => $reservedNumber]);

        $this->post(route('households.store'), [
            'household_head' => 'New Household',
            'address' => 'Purok 3',
        ])->assertRedirect(route('households.index'));

        $this->assertSame($reservedNumber, $existing->fresh()->household_number);
        $this->assertDatabaseHas('households', [
            'household_head' => 'New Household',
            'household_number' => $reservedNumber.'-1',
        ]);
    }

    public function test_household_details_can_be_viewed(): void
    {
        $household = Household::factory()->create([
            'household_number' => 'HH-2026-042',
            'household_head' => 'Andres Bonifacio',
        ]);

        $response = $this->get(route('households.show', [$household]));

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

        $response = $this->put(route('households.update', [$household]), [
            'household_number' => 'HH-2026-010-EDITED',
            'household_head' => 'Apolinario Mabini Sr.',
            'address' => 'Purok 5, Mabini St.',
        ]);

        $response->assertRedirect(route('households.index'));
        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'household_number' => 'HH-2026-010',
            'household_head' => 'Apolinario Mabini Sr.',
        ]);
    }

    public function test_household_can_be_deleted(): void
    {
        $household = Household::factory()->create();

        $response = $this->delete(route('households.destroy', [$household]));

        $response->assertRedirect(route('households.index'));
        $this->assertDatabaseMissing('households', [
            'id' => $household->id,
        ]);
    }
}
