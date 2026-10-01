<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class SeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_restores_the_saved_records_and_uploads(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $snapshot = json_decode(
            File::get(database_path('seeders/data/current-database.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->seed();

        foreach ($snapshot['tables'] as $table => $records) {
            $this->assertDatabaseCount($table, count($records));

            foreach ($records as $record) {
                $this->assertDatabaseHas($table, $record);
            }
        }

        foreach ($snapshot['files'] as $file) {
            Storage::disk($file['disk'])->assertExists($file['path']);
            $this->assertSame(
                File::get(database_path('seeders/data/files/'.$file['disk'].'/'.$file['path'])),
                Storage::disk($file['disk'])->get($file['path']),
            );
        }

        foreach (['sessions', 'password_reset_tokens', 'cache', 'jobs'] as $table) {
            $this->assertDatabaseEmpty($table);
        }

        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_seeding_refuses_to_mix_the_snapshot_with_existing_records(): void
    {
        Storage::fake('public');
        $official = Official::factory()->create();

        $this->assertThrows(
            fn () => $this->seed(),
            RuntimeException::class,
            'The database snapshot requires empty application tables. Back up existing data before using php artisan migrate:fresh --seed.',
        );

        $this->assertDatabaseCount('officials', 1);
        $this->assertModelExists($official);
        $this->assertDatabaseEmpty('households');
        $this->assertDatabaseEmpty('residents');
        $this->assertDatabaseEmpty('users');
        Storage::disk('public')->assertDirectoryEmpty('/');
    }

    public function test_a_failed_upload_restore_rolls_back_the_seeded_records(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('officials', 'An existing file blocks this directory.');

        $this->assertThrows(
            fn () => $this->seed(),
            RuntimeException::class,
        );

        foreach (['households', 'residents', 'users', 'officials', 'blotters', 'certificates', 'service_requests', 'incident_reports', 'incident_report_updates'] as $table) {
            $this->assertDatabaseEmpty($table);
        }

        Storage::disk('public')->assertExists('officials');
    }

    public function test_minor_residents_are_single_and_not_voters_by_default(): void
    {
        $resident = Resident::factory()->create([
            'birthdate' => today()->subYears(10)->toDateString(),
        ]);

        $this->assertSame('Single', $resident->civil_status);
        $this->assertFalse($resident->is_voter);
    }

    public function test_household_numbers_and_default_official_terms_follow_the_current_year(): void
    {
        $this->travelTo(now()->setDate(2030, 6, 15));

        $household = Household::factory()->create();
        $official = Official::factory()->create();

        $this->assertStringStartsWith('HH-2030-', $household->household_number);
        $this->assertTrue($official->term_start->lessThanOrEqualTo(today()));
        $this->assertTrue($official->term_end->greaterThanOrEqualTo(today()));
    }

    public function test_official_term_end_follows_an_overridden_start_date(): void
    {
        $official = Official::factory()->create(['term_start' => '2035-07-01']);

        $this->assertTrue($official->term_end->greaterThan($official->term_start));
    }

    public function test_certificate_factory_creates_a_resident_and_household_with_a_valid_issue_date(): void
    {
        $certificate = Certificate::factory()->create();

        $this->assertDatabaseCount('residents', 1);
        $this->assertDatabaseCount('households', 1);
        $this->assertNotNull($certificate->resident->household);
        $this->assertTrue($certificate->date_issued->greaterThanOrEqualTo($certificate->resident->birthdate));
        $this->assertTrue($certificate->date_issued->lessThanOrEqualTo(today()));
    }
}
