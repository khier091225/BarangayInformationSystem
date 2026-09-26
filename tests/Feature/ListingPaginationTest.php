<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ListingPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $attributes
     * @param  array<string, string>  $filters
     */
    #[DataProvider('filteredListings')]
    public function test_pagination_keeps_search_and_filters_without_role_query(string $resource, string $model, array $attributes, array $filters): void
    {
        $model::factory()->count(11)->create($attributes);
        $model::factory()->create($this->nonMatchingAttributes($resource));

        $response = $this->get(route($resource.'.index', $filters))->assertOk();
        $paginator = $response->viewData($resource);
        $this->assertSame(11, $paginator->total());
        $nextPageUrl = $paginator->nextPageUrl();
        $this->assertNotNull($nextPageUrl);
        $response->assertSee($nextPageUrl);
        parse_str(parse_url($nextPageUrl, PHP_URL_QUERY), $query);
        $this->assertSame('2', $query['page']);
        $this->assertArrayNotHasKey('role', $query);
        foreach ($filters as $key => $value) {
            $this->assertSame($value, $query[$key]);
        }

        $nextPage = $this->get($nextPageUrl)->assertOk()->viewData($resource);
        $this->assertSame(11, $nextPage->total());
        $this->assertCount(1, $nextPage->items());
    }

    public function test_resident_search_matches_a_full_name_across_separate_columns(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Marina',
            'middle_name' => 'Luz',
            'last_name' => 'Santos',
        ]);
        Resident::factory()->create(['first_name' => 'Marina', 'last_name' => 'Reyes']);

        $results = $this->get(route('residents.index', ['search' => 'Marina Santos']))
            ->assertOk()
            ->viewData('residents');

        $this->assertSame([$resident->id], $results->pluck('id')->all());
    }

    public function test_certificate_search_matches_resident_full_name_and_document_type(): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Ana', 'last_name' => 'Reyes']);
        $certificate = Certificate::factory()->create([
            'resident_id' => $resident->id,
            'certificate_type' => 'Barangay Clearance',
        ]);
        Certificate::factory()->create([
            'resident_id' => Resident::factory()->create(['first_name' => 'Ana', 'last_name' => 'Garcia'])->id,
            'certificate_type' => 'Barangay Clearance',
        ]);

        $results = $this->get(route('certificates.index', [
            'search' => 'Ana Reyes',
            'type' => 'Barangay Clearance',
        ]))->assertOk()->viewData('certificates');

        $this->assertSame([$certificate->id], $results->pluck('id')->all());
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $matchingAttributes
     * @param  array<string, mixed>  $otherAttributes
     */
    #[DataProvider('searchableFields')]
    public function test_search_matches_fields_shown_in_each_listing(string $resource, string $model, array $matchingAttributes, array $otherAttributes, string $search): void
    {
        $matchingRecord = $model::factory()->create($matchingAttributes);
        $model::factory()->create($otherAttributes);

        $response = $this->get(route($resource.'.index', ['search' => $search]))->assertOk();
        $results = $response->viewData($resource);

        $this->assertSame([$matchingRecord->id], $results->pluck('id')->all());

        if ($resource === 'households') {
            $response->assertSee('0 members');
        }
    }

    /**
     * @return array<string, array{string, class-string<Model>, array<string, mixed>, array<string, mixed>, string}>
     */
    public static function searchableFields(): array
    {
        return [
            'household address' => [
                'households', Household::class,
                ['household_head' => 'Marina Santos', 'address' => '12 Rizal Street, Purok 3'],
                ['household_head' => 'Other Household', 'address' => '45 Mabini Street, Purok 2'],
                'Rizal',
            ],
            'blotter respondent' => [
                'blotters', Blotter::class,
                ['complainant' => 'Mila Santos', 'respondent' => 'Roberto Reyes', 'incident' => 'Noise complaint'],
                ['complainant' => 'Ana Cruz', 'respondent' => 'Ricardo Garcia', 'incident' => 'Property dispute'],
                'Roberto',
            ],
            'official contact' => [
                'officials', Official::class,
                ['name' => 'Alfred Paldez', 'contact_number' => '09123456789'],
                ['name' => 'Other Official', 'contact_number' => '09987654321'],
                '09123456789',
            ],
        ];
    }

    /**
     * @return array<string, array{string, class-string, array<string, mixed>, array<string, string>}>
     */
    public static function filteredListings(): array
    {
        return [
            'residents including non-voters' => ['residents', Resident::class,
                ['first_name' => 'Pagination Match', 'gender' => 'Female', 'is_voter' => false],
                ['search' => 'Pagination Match', 'gender' => 'Female', 'is_voter' => '0']],
            'households' => ['households', Household::class,
                ['household_head' => 'Pagination Match'], ['search' => 'Pagination Match']],
            'certificates' => ['certificates', Certificate::class,
                ['purpose' => 'Pagination Match', 'certificate_type' => 'Barangay Clearance'],
                ['search' => 'Pagination Match', 'type' => 'Barangay Clearance']],
            'blotters' => ['blotters', Blotter::class,
                ['complainant' => 'Pagination Match', 'status' => 'Pending'],
                ['search' => 'Pagination Match', 'status' => 'Pending']],
            'officials' => ['officials', Official::class,
                ['name' => 'Pagination Match', 'position' => 'Barangay Kagawad'],
                ['search' => 'Pagination Match', 'position' => 'Barangay Kagawad']],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nonMatchingAttributes(string $resource): array
    {
        return match ($resource) {
            'residents' => ['first_name' => 'Pagination Match', 'gender' => 'Female', 'is_voter' => true],
            'households' => ['household_head' => 'Excluded Household', 'address' => 'Excluded Address'],
            'certificates' => ['purpose' => 'Pagination Match', 'certificate_type' => 'Certificate of Residency'],
            'blotters' => ['complainant' => 'Pagination Match', 'status' => 'Settled'],
            'officials' => ['name' => 'Pagination Match', 'position' => 'Barangay Captain'],
        };
    }
}
