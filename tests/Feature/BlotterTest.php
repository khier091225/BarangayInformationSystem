<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlotterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_dashboard_links_to_a_readable_blotter_report(): void
    {
        $blotter = Blotter::factory()->create([
            'incident' => "First line\n<script>alert('unsafe')</script>",
            'incident_date' => '2026-09-18',
            'status' => 'Pending',
        ]);

        $this->get(route('dashboard'))->assertOk()->assertSee(route('blotters.show', [$blotter]));
        $this->get(route('blotters.show', [$blotter]))
            ->assertOk()
            ->assertSee($blotter->complainant)
            ->assertSee($blotter->respondent)
            ->assertSee('Sep 18, 2026')
            ->assertSee('Pending')
            ->assertSee($blotter->incident)
            ->assertDontSee("<script>alert('unsafe')</script>", false)
            ->assertSee(route('blotters.edit', [$blotter]));
    }

    public function test_blotter_forms_render_and_valid_records_can_be_saved(): void
    {
        $data = Blotter::factory()->make()->getAttributes();
        $data['status'] = 'Pending';
        $this->get(route('blotters.create'))->assertOk();
        $this->post(route('blotters.store'), $data)->assertRedirect(route('blotters.index'));
        $this->assertDatabaseHas('blotters', $data);

        $blotter = Blotter::sole();
        $this->get(route('blotters.edit', [$blotter]))->assertOk()->assertDontSee('name="status"', false);
        $data['status'] = 'Settled';
        $this->put(route('blotters.update', [$blotter]), $data)->assertRedirect(route('blotters.index'));
        $this->assertSame(Blotter::STATUS_PENDING, $blotter->fresh()->status);
    }

    public function test_staff_can_record_a_blotter_with_only_the_essential_fields(): void
    {
        $this->get(route('blotters.create'))->assertOk()->assertSee('Add respondent or incident date');

        $this->post(route('blotters.store'), [
            'complainant' => 'Ana Cruz',
            'incident' => 'A dispute happened near the barangay hall.',
            'status' => 'Settled',
        ])->assertRedirect(route('blotters.index'));

        $blotter = Blotter::query()->sole();
        $this->assertSame('Unknown', $blotter->respondent);
        $this->assertSame('Pending', $blotter->status);
        $this->assertSame(today('Asia/Manila')->toDateString(), $blotter->incident_date->toDateString());
    }

    public function test_create_and_update_reject_invalid_blotter_details(): void
    {
        $blotter = Blotter::factory()->create(['status' => 'Pending']);
        $data = [
            'complainant' => '',
            'respondent' => str_repeat('a', 256),
            'incident' => '',
            'incident_date' => 'invalid-date',
            'status' => 'Unsupported',
        ];
        $invalidFields = ['complainant', 'respondent', 'incident', 'incident_date'];

        $this->post(route('blotters.store'), $data)->assertSessionHasErrors($invalidFields)->assertSessionDoesntHaveErrors('status');
        $this->put(route('blotters.update', [$blotter]), $data)->assertSessionHasErrors($invalidFields)->assertSessionDoesntHaveErrors('status');
        $this->assertDatabaseCount('blotters', 1);
        $this->assertSame('Pending', $blotter->fresh()->status);
    }

    public function test_assigned_staff_can_move_a_blotter_through_mediation_and_record_history(): void
    {
        $staff = User::factory()->create();
        $otherStaff = User::factory()->create();
        $blotter = Blotter::factory()->create(['status' => Blotter::STATUS_PENDING]);
        $this->actingAs($staff);

        $this->get(route('blotters.show', $blotter))->assertOk()
            ->assertSee('Accept case')
            ->assertSee('Case history');
        $this->get(route('blotters.index'))->assertOk()->assertSee(route('blotters.show', $blotter), false);

        $this->post(route('blotters.status.update', $blotter), [
            'action' => 'accept',
        ])->assertRedirect(route('blotters.show', $blotter));

        $blotter->refresh();
        $this->assertSame(Blotter::STATUS_ACCEPTED, $blotter->status);
        $this->assertSame($staff->id, $blotter->assigned_to);
        $this->assertNotNull($blotter->accepted_at);

        $this->actingAs($otherStaff)
            ->post(route('blotters.status.update', $blotter), [
                'action' => 'schedule',
                'hearing_at' => now()->addDay()->format('Y-m-d\TH:i'),
            ])->assertForbidden();

        $this->actingAs($staff)
            ->post(route('blotters.status.update', $blotter), ['action' => 'schedule'])
            ->assertSessionHasErrors('hearing_at');

        $hearingAt = now()->addDay()->startOfHour();
        $this->post(route('blotters.status.update', $blotter), [
            'action' => 'schedule',
            'hearing_at' => $hearingAt->format('Y-m-d\TH:i'),
            'message' => 'Please attend the mediation at the barangay hall.',
        ])->assertRedirect(route('blotters.show', $blotter));

        $blotter->refresh();
        $this->assertSame(Blotter::STATUS_SCHEDULED, $blotter->status);
        $this->assertSame($hearingAt->format('Y-m-d H:i'), $blotter->hearing_at->format('Y-m-d H:i'));

        $this->post(route('blotters.status.update', $blotter), [
            'action' => 'start',
        ])->assertRedirect(route('blotters.show', $blotter));
        $this->assertSame(Blotter::STATUS_MEDIATION, $blotter->fresh()->status);

        $this->post(route('blotters.status.update', $blotter), [
            'action' => 'settle',
            'message' => 'Both parties reached an agreement.',
        ])->assertRedirect(route('blotters.show', $blotter));

        $blotter->refresh();
        $this->assertSame(Blotter::STATUS_SETTLED, $blotter->status);
        $this->assertNotNull($blotter->closed_at);
        $this->assertSame(
            [Blotter::STATUS_ACCEPTED, Blotter::STATUS_SCHEDULED, Blotter::STATUS_MEDIATION, Blotter::STATUS_SETTLED],
            $blotter->updates()->pluck('status')->all(),
        );

        $this->get(route('blotters.show', $blotter))->assertOk()
            ->assertSee('Both parties reached an agreement.')
            ->assertSee('This case is closed');

        $this->post(route('blotters.status.update', $blotter), [
            'action' => 'dismiss',
        ])->assertSessionHas('warning');
        $this->assertSame(Blotter::STATUS_SETTLED, $blotter->fresh()->status);
    }

    public function test_missing_blotter_returns_not_found(): void
    {
        $this->get(route('blotters.show', [999]))->assertNotFound();
    }
}
