<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_a_code_from_staff_and_links_the_resident_record(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Dela Cruz',
        ]);
        $code = $this->issueCodeFor($resident);

        $this->get(route('login'))->assertOk()->assertSee(route('register'));
        $this->get(route('register'))->assertOk()
            ->assertSee('Create an account')
            ->assertSee('Registration code')
            ->assertSee(route('login'));

        $this->post(route('register.store'), [
            'registration_code' => strtolower($code),
            'name' => 'Imposter Name',
            'email' => 'juan@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'staff',
        ])->assertRedirect(route('account'));

        $user = User::query()->where('email', 'juan@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('resident', $user->role);
        $this->assertSame($resident->id, $user->resident_id);
        $this->assertSame('Juan Dela Cruz', $user->name);
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertSame(1, Resident::query()->count());
        $this->assertNull($resident->fresh()->registration_code_hash);
        $this->get(route('account'))->assertOk()->assertSee('Juan Dela Cruz');
    }

    public function test_staff_must_confirm_identity_before_issuing_a_code(): void
    {
        $resident = Resident::factory()->create();

        $this->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->resident()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('account'));

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), [])
            ->assertSessionHasErrors('identity_confirmed');

        $this->assertNull($resident->fresh()->registration_code_hash);

        $this->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident))
            ->assertSessionHas('registration_code');

        $this->get(route('residents.show', $resident))->assertOk()
            ->assertSee('Give this code to the resident now')
            ->assertSee(session('registration_code'));
    }

    public function test_resident_accounts_cannot_open_staff_records_even_with_a_role_parameter(): void
    {
        $this->actingAs(User::factory()->resident()->create());

        $this->get(route('dashboard', ['role' => 'staff']))
            ->assertRedirect(route('account'))
            ->assertSessionHas('warning', 'This page is only available to barangay staff.');
        $this->get(route('residents.index', ['role' => 'admin']))->assertRedirect(route('account'));
        $this->post(route('residents.store', ['role' => 'staff']), [])->assertRedirect(route('account'));
        $this->getJson(route('residents.index', ['role' => 'staff']))->assertForbidden();
    }

    public function test_registration_rejects_duplicate_email_and_invalid_password(): void
    {
        $resident = Resident::factory()->create();
        $code = $this->issueCodeFor($resident);
        User::factory()->create(['email' => 'existing@example.test']);

        $this->from(route('register'))->post(route('register.store'), [
            'registration_code' => $code,
            'email' => 'existing@example.test',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password'])
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.registration_code');

        $this->assertNull($resident->fresh()->user);
        $this->assertGuest();
    }

    public function test_missing_invalid_and_expired_codes_cannot_create_accounts(): void
    {
        $resident = Resident::factory()->create();
        $code = $this->issueCodeFor($resident);
        $details = [
            'email' => 'juan@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ];

        $this->post(route('register.store'), $details)->assertSessionHasErrors('registration_code');
        $this->post(route('register.store'), $details + ['registration_code' => '0000-0000-0000-0000'])
            ->assertSessionHasErrors('registration_code');

        $this->travel(25)->hours();
        $this->post(route('register.store'), $details + ['registration_code' => $code])
            ->assertSessionHasErrors('registration_code');
        $this->assertNull($resident->fresh()->user);
        $this->assertGuest();
    }

    public function test_reissuing_a_code_invalidates_the_previous_code(): void
    {
        $resident = Resident::factory()->create();
        $firstCode = $this->issueCodeFor($resident);
        $secondCode = $this->issueCodeFor($resident);
        $details = [
            'email' => 'juan@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ];

        $this->assertNotSame($firstCode, $secondCode);
        $this->post(route('register.store'), $details + ['registration_code' => $firstCode])
            ->assertSessionHasErrors('registration_code');
        $this->post(route('register.store'), $details + ['registration_code' => $secondCode])
            ->assertRedirect(route('account'));
    }

    public function test_used_code_cannot_create_another_account_or_be_reissued(): void
    {
        $resident = Resident::factory()->create();
        $code = $this->issueCodeFor($resident);
        $this->post(route('register.store'), [
            'registration_code' => $code,
            'email' => 'first@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('account'));

        $this->post(route('logout'));
        $this->post(route('register.store'), [
            'registration_code' => $code,
            'email' => 'second@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertSessionHasErrors('registration_code');

        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertSessionHasErrors('registration_code');

        $this->delete(route('residents.destroy', $resident))
            ->assertRedirect(route('residents.show', $resident))
            ->assertSessionHas('warning');
        $this->assertModelExists($resident);
    }

    public function test_existing_unlinked_resident_account_can_be_verified_with_a_code(): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos']);
        $code = $this->issueCodeFor($resident);
        $user = User::factory()->resident()->create(['name' => 'Old Name']);

        $this->actingAs($user)->get(route('account'))
            ->assertOk()->assertSee('not linked to a verified resident record');
        $this->post(route('account.verify'), ['registration_code' => $code])
            ->assertRedirect(route('account'));

        $this->assertSame($resident->id, $user->fresh()->resident_id);
        $this->assertSame($resident->full_name, $user->fresh()->name);
        $this->assertNull($resident->fresh()->registration_code_hash);
        $this->get(route('account'))->assertOk()->assertSee('linked to a verified resident record');
        $this->actingAs($user->fresh());
        $this->post(route('account.verify'), ['registration_code' => $code])->assertForbidden();
    }

    public function test_resident_login_returns_to_account_and_can_logout(): void
    {
        $user = User::factory()->resident()->create();

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('account'));

        $this->get(route('login'))->assertRedirect(route('account'));
        $this->get(route('register'))->assertRedirect(route('account'));
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('account'))->assertRedirect(route('login'));
    }

    private function issueCodeFor(Resident $resident): string
    {
        $this->actingAs(User::factory()->create())
            ->post(route('residents.registration-code.store', $resident), ['identity_confirmed' => '1'])
            ->assertRedirect(route('residents.show', $resident))
            ->assertSessionHas('registration_code');

        $code = session('registration_code');
        $this->post(route('logout'))->assertRedirect(route('login'));

        return $code;
    }
}
