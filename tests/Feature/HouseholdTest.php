<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\User;
use Carbon\CarbonImmutable;
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
        $response->assertSee('href="'.route('households.create').'"', false);
        $response->assertSee('data-household-create-dialog', false);
        $response->assertSee('action="'.route('households.store').'"', false);
        $response->assertSee('name="household_head"', false);
        $response->assertSee('name="address"', false);
        $response->assertDontSee('data-open-on-load', false);
    }

    public function test_household_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('households.create'));

        $response->assertOk();
        $response->assertSee('Add New Household');
        $response->assertSee('Its number will be assigned automatically when you save.');
        $response->assertSee('action="'.route('households.store').'"', false);
        $response->assertSee('name="household_head"', false);
        $response->assertSee('name="address"', false);
        $response->assertDontSee('data-household-create-dialog', false);
        $response->assertDontSee('name="household_number"', false);
    }

    public function test_invalid_submission_from_modal_reopens_it_with_errors_and_old_input(): void
    {
        $this->from(route('households.index'))->post(route('households.store'), [
            'household_head' => 'Test Household',
            'address' => '',
        ])->assertRedirect(route('households.index'))
            ->assertSessionHasErrors('address');

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('households.index'))->assertOk()
            ->assertSee('data-open-on-load', false)
            ->assertSee('value="Test Household"', false)
            ->assertSee('The address field is required.');

        $this->assertDatabaseCount('households', 0);
    }

    public function test_invalid_submission_from_create_page_returns_to_that_page(): void
    {
        $this->from(route('households.create'))->post(route('households.store'), [
            'household_head' => 'Test Household',
            'address' => '',
        ])->assertRedirect(route('households.create'))
            ->assertSessionHasErrors('address');

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('households.create'))->assertOk()
            ->assertSee('value="Test Household"', false)
            ->assertSee('The address field is required.');
    }

    public function test_household_edit_modal_and_edit_page_use_the_same_prefilled_form(): void
    {
        $household = Household::factory()->create([
            'household_head' => 'Original Head',
            'address' => 'Purok 1',
        ]);

        $this->get(route('households.index'))->assertOk()
            ->assertSee('href="'.route('households.edit', $household).'"', false)
            ->assertSee('id="household-edit-dialog-'.$household->getKey().'"', false)
            ->assertSee('action="'.route('households.update', $household).'"', false)
            ->assertSee('id="household-edit-'.$household->getKey().'-household_head"', false)
            ->assertSee('value="Original Head"', false);

        $this->get(route('households.edit', $household))->assertOk()
            ->assertSee('action="'.route('households.update', $household).'"', false)
            ->assertSee('value="Original Head"', false)
            ->assertSee('value="Purok 1"', false)
            ->assertDontSee('data-household-dialog', false);
    }

    public function test_invalid_edit_from_modal_reopens_only_its_household_with_old_input(): void
    {
        $household = Household::factory()->create(['household_head' => 'First Head']);
        $otherHousehold = Household::factory()->create(['household_head' => 'Second Head']);

        $this->from(route('households.index'))->put(route('households.update', $household), [
            '_household_edit_id' => (string) $household->getKey(),
            'household_head' => 'Revised Head',
            'address' => '',
        ])->assertRedirect(route('households.index'))
            ->assertSessionHasErrors('address');

        $response = $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('households.index'));

        $response->assertOk()
            ->assertSee('value="Revised Head"', false)
            ->assertSee('value="Second Head"', false)
            ->assertSee('The address field is required.');
        $this->assertMatchesRegularExpression('/<dialog id="household-edit-dialog-'.$household->getKey().'"[^>]*data-open-on-load/', $response->getContent());
        $this->assertSame(1, substr_count($response->getContent(), 'data-open-on-load'));
        $this->assertSame('First Head', $household->fresh()->household_head);
        $this->assertSame('Second Head', $otherHousehold->fresh()->household_head);
    }

    public function test_invalid_edit_from_full_page_returns_to_that_page(): void
    {
        $household = Household::factory()->create();

        $this->from(route('households.edit', $household))->put(route('households.update', $household), [
            'household_head' => 'Revised Head',
            'address' => '',
        ])->assertRedirect(route('households.edit', $household))
            ->assertSessionHasErrors('address');

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('households.edit', $household))->assertOk()
            ->assertSee('value="Revised Head"', false)
            ->assertSee('The address field is required.');
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

    public function test_generated_number_uses_the_barangay_calendar_year(): void
    {
        $this->travelTo(CarbonImmutable::parse('2030-12-31 16:30:00', 'UTC'));

        $this->post(route('households.store'), [
            'household_head' => 'New Year Household',
            'address' => 'Purok 4',
        ])->assertRedirect(route('households.index'));

        $household = Household::sole();
        $this->assertSame(sprintf('HH-2031-%06d', $household->id), $household->household_number);
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
