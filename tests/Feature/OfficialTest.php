<?php

namespace Tests\Feature;

use App\Models\Official;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialTest extends TestCase
{
    use RefreshDatabase;

    public function test_officials_index_page_can_be_rendered(): void
    {
        Official::factory()->count(3)->create();

        $response = $this->get(route('officials.index'));

        $response->assertOk();
        $response->assertSee('Barangay Officials');
    }

    public function test_official_create_form_can_be_rendered(): void
    {
        $response = $this->get(route('officials.create'));

        $response->assertOk();
        $response->assertSee('Add New Official');
    }

    public function test_new_official_can_be_stored(): void
    {
        $data = [
            'name' => 'Hon. Jose Rizal',
            'position' => 'Barangay Captain',
            'contact_number' => '09171234567',
            'term_start' => '2023-07-01',
            'term_end' => '2026-06-30',
        ];

        $response = $this->post(route('officials.store'), $data);

        $response->assertRedirect(route('officials.index'));
        $this->assertDatabaseHas('officials', [
            'name' => 'Hon. Jose Rizal',
            'position' => 'Barangay Captain',
        ]);
    }

    public function test_official_edit_form_can_be_rendered(): void
    {
        $official = Official::factory()->create();

        $response = $this->get(route('officials.edit', $official));

        $response->assertOk();
        $response->assertSee('Edit Official');
    }

    public function test_official_can_be_updated(): void
    {
        $official = Official::factory()->create([
            'name' => 'Juan Luna',
            'position' => 'Barangay Kagawad',
        ]);

        $response = $this->put(route('officials.update', $official), [
            'name' => 'Juan Luna Updated',
            'position' => 'Barangay Captain',
            'contact_number' => '09998887777',
            'term_start' => '2023-07-01',
            'term_end' => '2026-06-30',
        ]);

        $response->assertRedirect(route('officials.index'));
        $this->assertDatabaseHas('officials', [
            'id' => $official->id,
            'name' => 'Juan Luna Updated',
            'position' => 'Barangay Captain',
        ]);
    }

    public function test_official_can_be_deleted(): void
    {
        $official = Official::factory()->create();

        $response = $this->delete(route('officials.destroy', $official));

        $response->assertRedirect(route('officials.index'));
        $this->assertDatabaseMissing('officials', [
            'id' => $official->id,
        ]);
    }
}
