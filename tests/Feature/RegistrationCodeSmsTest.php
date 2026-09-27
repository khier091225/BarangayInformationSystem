<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationCodeSmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.philsms.url' => 'https://dashboard.philsms.com/api/v3',
            'services.philsms.token' => 'test-token',
            'services.philsms.sender_id' => 'Barangay',
        ]);
        Http::preventStrayRequests();
    }

    #[DataProvider('validMobileNumbers')]
    public function test_code_is_sent_to_the_stored_mobile_number_and_can_be_used_to_register(string $number): void
    {
        $this->freezeSecond();
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response(['status' => 'success', 'data' => ['uid' => 'sms-123']])]);
        $resident = Resident::factory()->create(['contact_number' => $number]);

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), [
                'identity_confirmed' => '1',
                'contact_number' => '09999999999',
                'recipient' => '09999999999',
            ])->assertRedirect(route('residents.show', $resident))
            ->assertSessionHasNoErrors();

        $code = session('registration_code');
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://dashboard.philsms.com/api/v3/sms/send'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('Accept', 'application/json')
            && $request['recipient'] === '639171234567'
            && $request['sender_id'] === 'Barangay'
            && $request['type'] === 'plain'
            && str_contains($request['message'], $code)
            && strlen($request['message']) <= 160);

        $resident->refresh();
        $this->assertSame('submitted', $resident->registration_code_sms_status);
        $this->assertSame(hash('sha256', str_replace('-', '', $code)), $resident->registration_code_hash);
        $this->assertTrue($resident->registration_code_expires_at->equalTo(now()->addDay()));
        $this->assertTrue($resident->registration_code_issued_at->equalTo(now()));

        $this->get(route('residents.show', $resident))->assertOk()
            ->assertSee('SMS submitted')->assertSee('+639171234567')->assertSee($code)
            ->assertDontSee('test-token');
        $this->get(route('residents.show', $resident))->assertOk()->assertDontSee($code);

        $this->post(route('logout'));
        $this->post(route('register.store'), [
            'registration_code' => $code,
            'email' => 'resident@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('account'));

        $this->assertSame($resident->id, User::query()->where('email', 'resident@example.test')->firstOrFail()->resident_id);
        $this->assertNull($resident->fresh()->registration_code_hash);
        $this->assertNull($resident->fresh()->registration_code_sms_status);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validMobileNumbers(): array
    {
        return [
            'local' => ['09171234567'],
            'country code' => ['639171234567'],
            'international' => ['+639171234567'],
            'formatted' => ['(+63) 917-123-4567'],
        ];
    }

    #[DataProvider('invalidMobileNumbers')]
    public function test_invalid_mobile_numbers_do_not_send_or_replace_an_existing_code(?string $number): void
    {
        Http::fake();
        $oldHash = hash('sha256', 'AAAABBBBCCCCDDDD');
        $resident = Resident::factory()->create([
            'contact_number' => $number,
            'registration_code_hash' => $oldHash,
            'registration_code_expires_at' => now()->addHour(),
        ]);

        $this->actingAs(User::factory()->create())
            ->from(route('residents.show', $resident))
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident))
            ->assertSessionHasErrors('registration_code')
            ->assertSessionMissing('registration_code');

        $this->assertSame($oldHash, $resident->fresh()->registration_code_hash);
        Http::assertNothingSent();
        $this->get(route('residents.show', $resident))->assertOk()->assertSee('Add a valid Philippine mobile number');
    }

    /**
     * @return array<string, array{?string}>
     */
    public static function invalidMobileNumbers(): array
    {
        return [
            'missing' => [null],
            'blank' => [''],
            'landline' => ['0281234567'],
            'too short' => ['0917123456'],
            'international outside Philippines' => ['+14155552671'],
            'multiple recipients' => ['09171234567,09181234567'],
            'letters' => ['09171234567 ext123'],
        ];
    }

    #[DataProvider('missingSettings')]
    public function test_missing_sms_configuration_does_not_issue_a_code(string $setting): void
    {
        config([$setting => '']);
        Http::fake();
        $resident = Resident::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from(route('residents.show', $resident))
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertSessionHasErrors('registration_code')->assertSessionMissing('registration_code');

        $this->assertNull($resident->fresh()->registration_code_hash);
        Http::assertNothingSent();
        $this->get(route('residents.show', $resident))->assertOk()->assertSee('SMS sending is not configured');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function missingSettings(): array
    {
        return [
            'URL' => ['services.philsms.url'],
            'token' => ['services.philsms.token'],
            'sender' => ['services.philsms.sender_id'],
        ];
    }

    public function test_repeated_sends_are_blocked_for_one_minute_even_from_another_staff_account(): void
    {
        $this->freezeTime();
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response(['status' => 'success'])]);
        $resident = Resident::factory()->create();
        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertSessionHasNoErrors();
        $firstHash = $resident->fresh()->registration_code_hash;

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertSessionHasErrors('registration_code');
        $this->assertSame($firstHash, $resident->fresh()->registration_code_hash);
        Http::assertSentCount(1);

        $this->travel(61)->seconds();
        $this->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident));
        $this->assertNotSame($firstHash, $resident->fresh()->registration_code_hash);
        Http::assertSentCount(2);
    }

    #[DataProvider('unsuccessfulResponses')]
    public function test_provider_failures_are_not_reported_as_success_and_keep_the_code_valid(
        array|string $body, int $httpStatus, string $expectedStatus, string $expectedMessage,
    ): void {
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::response($body, $httpStatus)]);
        $resident = Resident::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident))->assertSessionHas('registration_code');

        $code = session('registration_code');
        $this->assertSame($expectedStatus, $resident->fresh()->registration_code_sms_status);
        $this->assertNotNull(Resident::findAvailableRegistrationCode(str_replace('-', '', $code)));
        Http::assertSentCount(1);
        $this->get(route('residents.show', $resident))->assertOk()
            ->assertSee($expectedMessage)->assertSee($code)->assertDontSee('SMS submitted')
            ->assertDontSee('private-provider-error');
    }

    /**
     * @return array<string, array{array<string, string>|string, int, string, string}>
     */
    public static function unsuccessfulResponses(): array
    {
        return [
            'provider rejection' => [['status' => 'error', 'message' => 'private-provider-error'], 200, 'failed', 'SMS could not be sent'],
            'invalid credentials' => [['message' => 'private-provider-error'], 401, 'failed', 'SMS could not be sent'],
            'rate limit' => [['message' => 'private-provider-error'], 429, 'failed', 'SMS could not be sent'],
            'provider outage' => ['private-provider-error', 503, 'unconfirmed', 'SMS submission not confirmed'],
            'HTTP timeout' => ['', 408, 'unconfirmed', 'SMS submission not confirmed'],
            'unexpected response' => ['<html>private-provider-error</html>', 200, 'unconfirmed', 'SMS submission not confirmed'],
            'redirect' => ['', 302, 'unconfirmed', 'SMS submission not confirmed'],
        ];
    }

    public function test_connection_timeout_keeps_the_code_usable_without_automatically_retrying(): void
    {
        Http::fake(['https://dashboard.philsms.com/api/v3/sms/send' => Http::failedConnection()]);
        $resident = Resident::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident))->assertSessionHas('registration_code');

        $this->assertSame('unconfirmed', $resident->fresh()->registration_code_sms_status);
        $this->assertNotNull(Resident::findAvailableRegistrationCode(str_replace('-', '', session('registration_code'))));
        $this->get(route('residents.show', $resident))->assertOk()->assertSee('SMS submission not confirmed');
    }

    public function test_residents_with_accounts_do_not_receive_registration_sms(): void
    {
        Http::fake();
        $resident = Resident::factory()->create();
        User::factory()->resident()->create(['resident_id' => $resident->id]);

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertSessionHasErrors('registration_code');

        Http::assertNothingSent();
        $this->assertNull($resident->fresh()->registration_code_hash);
    }

    public function test_a_late_provider_response_does_not_change_a_newer_codes_status(): void
    {
        $resident = Resident::factory()->create();
        $newerHash = hash('sha256', 'EEEEFFFF11112222');
        Http::fake(function () use ($resident, $newerHash): PromiseInterface {
            $resident->forceFill([
                'registration_code_hash' => $newerHash,
                'registration_code_sms_status' => 'unconfirmed',
            ])->save();

            return Http::response(['status' => 'success']);
        });

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident));

        $this->assertNull(session('registration_code'));
        $this->assertSame($newerHash, $resident->fresh()->registration_code_hash);
        $this->assertSame('unconfirmed', $resident->fresh()->registration_code_sms_status);
    }
}
