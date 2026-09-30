<?php

namespace Tests\Feature;

use App\Models\Official;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OfficialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

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
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('Official photo');
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

        $response = $this->get(route('officials.edit', [$official]));

        $response->assertOk();
        $response->assertSee('Edit Official');
        $response->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_official_can_be_updated(): void
    {
        $official = Official::factory()->create([
            'name' => 'Juan Luna',
            'position' => 'Barangay Kagawad',
        ]);

        $response = $this->put(route('officials.update', [$official]), [
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

        $response = $this->delete(route('officials.destroy', [$official]));

        $response->assertRedirect(route('officials.index'));
        $this->assertDatabaseMissing('officials', [
            'id' => $official->id,
        ]);
    }

    public function test_staff_can_upload_an_official_photo_for_the_public_homepage(): void
    {
        Storage::fake('public');

        $this->post(route('officials.store'), [
            'name' => 'Hon. Maria Santos',
            'position' => 'Barangay Captain',
            'photo' => UploadedFile::fake()->image('portrait.jpg'),
        ])->assertRedirect(route('officials.index'));

        $official = Official::query()->sole();
        $this->assertNotNull($official->image_path);
        Storage::disk('public')->assertExists($official->image_path);
        $this->get(route('home'))->assertOk()
            ->assertSee('Hon. Maria Santos')
            ->assertSee(asset('storage/'.$official->image_path), false);
    }

    public function test_official_photo_rejects_non_images_and_files_over_two_megabytes(): void
    {
        Storage::fake('public');
        $details = ['name' => 'Hon. Maria Santos', 'position' => 'Barangay Captain'];

        $this->post(route('officials.store'), $details + [
            'photo' => UploadedFile::fake()->create('document.txt', 10, 'text/plain'),
        ])->assertSessionHasErrors('photo');

        $this->post(route('officials.store'), $details + [
            'photo' => UploadedFile::fake()->image('large.jpg')->size(2049),
        ])->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('officials', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('officials'));
    }

    public function test_official_photo_can_be_replaced_and_removed(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('officials/previous.jpg', 'previous photo');
        $official = Official::factory()->create(['image_path' => 'officials/previous.jpg']);
        $details = ['name' => $official->name, 'position' => $official->position];

        $this->post(route('officials.update', $official), $details + [
            '_method' => 'PUT',
            'photo' => UploadedFile::fake()->image('replacement.png'),
        ])->assertRedirect(route('officials.index'));

        $official->refresh();
        $replacementPath = $official->image_path;
        $this->assertNotSame('officials/previous.jpg', $replacementPath);
        Storage::disk('public')->assertExists($replacementPath);
        Storage::disk('public')->assertMissing('officials/previous.jpg');

        $this->put(route('officials.update', $official), $details + ['remove_photo' => '1'])
            ->assertRedirect(route('officials.index'));

        $this->assertNull($official->refresh()->image_path);
        Storage::disk('public')->assertMissing($replacementPath);
    }

    public function test_deleting_an_official_removes_their_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('officials/portrait.jpg', 'official photo');
        $official = Official::factory()->create(['image_path' => 'officials/portrait.jpg']);

        $this->delete(route('officials.destroy', $official))->assertRedirect(route('officials.index'));

        Storage::disk('public')->assertMissing('officials/portrait.jpg');
    }
}
