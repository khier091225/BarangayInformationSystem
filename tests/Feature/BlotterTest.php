<?php

namespace Tests\Feature;

use App\Models\Blotter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlotterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_links_to_a_readable_blotter_report(): void
    {
        $blotter = Blotter::factory()->create([
            'incident' => "First line\n<script>alert('unsafe')</script>",
            'incident_date' => '2026-09-18',
            'status' => 'Pending',
        ]);

        $this->get(route('dashboard'))->assertOk()->assertSee(route('blotters.show', $blotter));
        $this->get(route('blotters.show', $blotter))
            ->assertOk()
            ->assertSee($blotter->complainant)
            ->assertSee($blotter->respondent)
            ->assertSee('Sep 18, 2026')
            ->assertSee('Pending')
            ->assertSee($blotter->incident)
            ->assertDontSee("<script>alert('unsafe')</script>", false)
            ->assertSee(route('blotters.edit', $blotter));
    }

    public function test_blotter_forms_render_and_valid_records_can_be_saved(): void
    {
        $data = Blotter::factory()->make()->getAttributes();
        $this->get(route('blotters.create'))->assertOk();
        $this->post(route('blotters.store'), $data)->assertRedirect(route('blotters.index'));
        $this->assertDatabaseHas('blotters', $data);

        $blotter = Blotter::sole();
        $this->get(route('blotters.edit', $blotter))->assertOk();
        $data['status'] = 'Settled';
        $this->put(route('blotters.update', $blotter), $data)->assertRedirect(route('blotters.index'));
        $this->assertSame('Settled', $blotter->fresh()->status);
    }

    public function test_create_and_update_reject_invalid_blotter_details(): void
    {
        $blotter = Blotter::factory()->create(['status' => 'Pending']);
        $data = [
            'complainant' => '',
            'respondent' => '',
            'incident' => '',
            'incident_date' => 'invalid-date',
            'status' => 'Unsupported',
        ];

        $this->post(route('blotters.store'), $data)->assertSessionHasErrors(array_keys($data));
        $this->put(route('blotters.update', $blotter), $data)->assertSessionHasErrors(array_keys($data));
        $this->assertDatabaseCount('blotters', 1);
        $this->assertSame('Pending', $blotter->fresh()->status);
    }

    public function test_missing_blotter_returns_not_found(): void
    {
        $this->get(route('blotters.show', 999))->assertNotFound();
    }
}
