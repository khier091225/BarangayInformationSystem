<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_consistent_households_and_related_records(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue(Hash::check('password', User::sole()->password));
        $this->assertDatabaseCount('households', 10);
        $this->assertDatabaseCount('officials', 7);
        $this->assertDatabaseCount('blotters', 5);

        foreach (Household::with('residents')->get() as $household) {
            $this->assertGreaterThanOrEqual(3, $household->residents->count());
            $this->assertLessThanOrEqual(5, $household->residents->count());

            $head = $household->residents->firstWhere('full_name', $household->household_head);
            $this->assertNotNull($head);
            $this->assertGreaterThanOrEqual(18, $head->birthdate->age);

            foreach ($household->residents as $resident) {
                $this->assertSame($household->address, $resident->address);
            }
        }

        $certificates = Certificate::with('resident')->get();
        $this->assertCount(5, $certificates->pluck('resident_id')->unique());
        $this->assertGreaterThanOrEqual(5, $certificates->count());
        $this->assertLessThanOrEqual(10, $certificates->count());

        foreach ($certificates as $certificate) {
            $this->assertNotNull($certificate->resident);
            $this->assertTrue($certificate->date_issued->greaterThanOrEqualTo($certificate->resident->birthdate));
        }
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
