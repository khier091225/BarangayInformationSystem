<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Official;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ResourceRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsupported_actions_are_not_registered(): void
    {
        $official = Official::factory()->create();
        $certificate = Certificate::factory()->create();

        $this->assertFalse(Route::has('officials.show'));
        $this->assertFalse(Route::has('certificates.edit'));
        $this->assertFalse(Route::has('certificates.update'));
        $this->get('/officials/'.$official->id)->assertStatus(405);
        $this->get('/certificates/'.$certificate->id.'/edit')->assertNotFound();
        $this->put('/certificates/'.$certificate->id, [])->assertStatus(405);
        $this->patch('/certificates/'.$certificate->id, [])->assertStatus(405);
    }
}
