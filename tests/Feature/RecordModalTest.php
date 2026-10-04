<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RecordModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function createModules(): array
    {
        return [
            'residents' => ['residents', 'resident-create-dialog', 'first_name'],
            'officials' => ['officials', 'official-create-dialog', 'name'],
            'blotters' => ['blotters', 'blotter-create-dialog', 'complainant'],
            'certificates' => ['certificates', 'certificate-create-dialog', 'resident_id'],
        ];
    }

    /**
     * @return array<string, array{string, class-string<Model>, string}>
     */
    public static function editModules(): array
    {
        return [
            'residents' => ['residents', Resident::class, 'first_name'],
            'officials' => ['officials', Official::class, 'name'],
            'blotters' => ['blotters', Blotter::class, 'complainant'],
        ];
    }

    #[DataProvider('createModules')]
    public function test_create_modal_and_direct_page_share_the_store_form(string $module, string $dialogId, string $requiredField): void
    {
        $this->get(route($module.'.index'))->assertOk()
            ->assertSee('href="'.route($module.'.create').'"', false)
            ->assertSee('id="'.$dialogId.'"', false)
            ->assertSee('action="'.route($module.'.store').'"', false)
            ->assertSee('name="'.$requiredField.'"', false);

        $this->get(route($module.'.create'))->assertOk()
            ->assertSee('action="'.route($module.'.store').'"', false)
            ->assertSee('name="'.$requiredField.'"', false)
            ->assertDontSee('data-record-dialog', false);
    }

    #[DataProvider('createModules')]
    public function test_invalid_create_submission_reopens_its_modal(string $module, string $dialogId, string $requiredField): void
    {
        $this->from(route($module.'.index'))->post(route($module.'.store'), [
            '_record_form' => $module.'.create',
            $requiredField => '',
        ])->assertRedirect(route($module.'.index'))
            ->assertSessionHasErrors($requiredField);

        $response = $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route($module.'.index'));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<dialog id="'.$dialogId.'"[^>]*data-open-on-load/', $response->getContent());
        $this->assertSame(1, substr_count($response->getContent(), 'data-open-on-load'));
        $this->assertInvalidFieldsDescribeTheirErrors($response->getContent());
    }

    #[DataProvider('editModules')]
    public function test_edit_modal_and_direct_page_share_the_update_form(string $module, string $modelClass, string $requiredField): void
    {
        $record = $modelClass::factory()->create();
        $dialogId = substr($module, 0, -1).'-edit-dialog-'.$record->getKey();

        $this->get(route($module.'.index'))->assertOk()
            ->assertSee('href="'.route($module.'.edit', $record).'"', false)
            ->assertSee('id="'.$dialogId.'"', false)
            ->assertSee('action="'.route($module.'.update', $record).'"', false)
            ->assertSee('name="'.$requiredField.'"', false);

        $this->get(route($module.'.edit', $record))->assertOk()
            ->assertSee('action="'.route($module.'.update', $record).'"', false)
            ->assertSee('name="'.$requiredField.'"', false)
            ->assertDontSee('data-record-dialog', false);
    }

    #[DataProvider('editModules')]
    public function test_invalid_edit_reopens_only_the_selected_record(string $module, string $modelClass, string $requiredField): void
    {
        $record = $modelClass::factory()->create();
        $modelClass::factory()->create();
        $dialogId = substr($module, 0, -1).'-edit-dialog-'.$record->getKey();

        $this->from(route($module.'.index'))->put(route($module.'.update', $record), [
            '_record_form' => $module.'.edit.'.$record->getKey(),
            $requiredField => '',
        ])->assertRedirect(route($module.'.index'))
            ->assertSessionHasErrors($requiredField);

        $response = $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route($module.'.index'));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<dialog id="'.$dialogId.'"[^>]*data-open-on-load/', $response->getContent());
        $this->assertSame(1, substr_count($response->getContent(), 'data-open-on-load'));
        $this->assertInvalidFieldsDescribeTheirErrors($response->getContent());
    }

    public function test_resident_profile_provides_one_prefilled_edit_modal_for_both_edit_links(): void
    {
        $household = Household::factory()->create();
        $resident = Resident::factory()->for($household)->create();
        $dialogId = 'resident-edit-dialog-'.$resident->getKey();

        $response = $this->get(route('residents.show', $resident))->assertOk();

        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $this->assertCount(1, $xpath->query('//dialog[@id="'.$dialogId.'"]'));
        $this->assertCount(2, $xpath->query('//a[@aria-controls="'.$dialogId.'" and @aria-haspopup="dialog"]'));
        $this->assertCount(1, $xpath->query('//dialog//form[@action="'.route('residents.update', $resident).'"]'));
        $this->assertSame($resident->first_name, $xpath->query('//dialog//input[@name="first_name"]')->item(0)->getAttribute('value'));
        $selectedHousehold = $xpath->query('//dialog//select[@name="household_id"]/option[@selected]');
        $this->assertCount(1, $selectedHousehold);
        $this->assertSame((string) $household->id, $selectedHousehold->item(0)->getAttribute('value'));
    }

    public function test_invalid_resident_profile_edit_reopens_the_modal_and_keeps_entered_values(): void
    {
        $resident = Resident::factory()->create();
        $originalAttributes = $resident->fresh()->getAttributes();
        $profileUrl = route('residents.show', $resident);

        $this->from($profileUrl)->put(route('residents.update', $resident), [
            '_record_form' => 'residents.edit.'.$resident->getKey(),
            '_return_to' => 'residents.show',
            'first_name' => '',
            'middle_name' => 'Retained name',
        ])->assertRedirect($profileUrl)->assertSessionHasErrors('first_name');

        $response = $this->withCookie(config('session.cookie'), session()->getId())->get($profileUrl)->assertOk();
        $this->assertMatchesRegularExpression('/<dialog id="resident-edit-dialog-'.$resident->getKey().'"[^>]*data-open-on-load/', $response->getContent());
        $response->assertSee('value="Retained name"', false);
        $this->assertInvalidFieldsDescribeTheirErrors($response->getContent());
        $this->assertSame($originalAttributes, $resident->fresh()->getAttributes());
    }

    private function assertInvalidFieldsDescribeTheirErrors(string $html): void
    {
        $document = new DOMDocument;
        $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $invalidFields = $xpath->query('//*[@aria-invalid="true"]');
        $this->assertGreaterThan(0, $invalidFields->length);

        foreach ($invalidFields as $field) {
            $description = trim($field->getAttribute('aria-describedby'));
            $this->assertNotSame('', $description, 'Invalid field '.$field->getAttribute('id').' must describe its error.');
            $hasError = false;

            foreach (preg_split('/\s+/', $description) as $descriptionId) {
                $descriptions = $xpath->query('//*[@id="'.$descriptionId.'"]');
                $this->assertCount(1, $descriptions, 'Description '.$descriptionId.' must identify exactly one element.');
                $element = $descriptions->item(0);

                if (str_contains(' '.$element->getAttribute('class').' ', ' form-error ') && trim($element->textContent) !== '') {
                    $hasError = true;
                }
            }

            $this->assertTrue($hasError, 'Invalid field '.$field->getAttribute('id').' must describe a visible validation message.');
        }
    }
}
