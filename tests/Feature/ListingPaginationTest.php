<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ListingPaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $attributes
     * @param  array<string, string>  $filters
     */
    #[DataProvider('filteredListings')]
    public function test_pagination_links_keep_search_and_filters(string $resource, string $model, array $attributes, array $filters): void
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
        foreach ($filters as $key => $value) {
            $this->assertSame($value, $query[$key]);
        }

        $nextPage = $this->get($nextPageUrl)->assertOk()->viewData($resource);
        $this->assertSame(11, $nextPage->total());
        $this->assertCount(1, $nextPage->items());
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
