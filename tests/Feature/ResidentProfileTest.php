<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResidentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_resident_can_open_profile_and_find_it_in_navigation(): void
    {
        [$resident, $user] = $this->verifiedResident();

        $this->actingAs($user)->get(route('account.profile.edit'))->assertOk()
            ->assertSee('My profile')->assertSee('Change password')
            ->assertSee($resident->full_name)->assertSee($user->email)
            ->assertSee($resident->contact_number)->assertSee($resident->address)
            ->assertSee('Profile details are read-only')
            ->assertSee(route('account.profile.password.update'), false)
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="email"', false);

        $this->get(route('account'))->assertOk()
            ->assertSee(route('account.profile.edit'), false)
            ->assertSee('My profile');
    }

    public function test_guests_staff_and_unverified_residents_cannot_access_resident_profile(): void
    {
        $payload = ['current_password' => 'password', 'password' => 'ChangedPass123!', 'password_confirmation' => 'ChangedPass123!'];

        $this->get(route('account.profile.edit'))->assertRedirect(route('login'));
        $this->patch(route('account.profile.password.update'), $payload)->assertRedirect(route('login'));

        $staff = User::factory()->create();
        $staffHash = $staff->password;
        $this->actingAs($staff)->get(route('account.profile.edit'))->assertRedirect(route('dashboard'));
        $this->patchJson(route('account.profile.password.update'), $payload)->assertForbidden();
        $this->assertSame($staffHash, $staff->fresh()->password);

        $unverifiedResident = User::factory()->resident()->create();
        $unverifiedHash = $unverifiedResident->password;
        $this->actingAs($unverifiedResident)->get(route('account.profile.edit'))->assertRedirect(route('account'));
        $this->patchJson(route('account.profile.password.update'), $payload)->assertForbidden();
        $this->assertSame($unverifiedHash, $unverifiedResident->fresh()->password);
    }

    public function test_resident_can_change_only_their_password(): void
    {
        [$resident, $user] = $this->verifiedResident();
        $otherUser = User::factory()->create();
        $oldToken = $user->remember_token;
        $original = $user->only(['name', 'email', 'role', 'resident_id']);

        $this->actingAs($user)->get(route('account.profile.edit'))->assertOk();
        $oldSessionId = session()->getId();

        $this->patch(route('account.profile.password.update'), [
            'current_password' => 'password',
            'password' => 'UpdatedPass123!',
            'password_confirmation' => 'UpdatedPass123!',
            'id' => $otherUser->id,
            'name' => 'Changed Resident Name',
            'email' => 'changed@example.test',
            'role' => 'staff',
            'resident_id' => $otherUser->id,
        ])->assertRedirect(route('account.profile.edit'))
            ->assertSessionHasNoErrors()->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('UpdatedPass123!', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertNotSame($oldToken, $user->remember_token);
        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertSame($original, $user->only(['name', 'email', 'role', 'resident_id']));
        $this->assertSame($resident->full_name, $resident->fresh()->full_name);
        $this->assertTrue(Hash::check('password', $otherUser->fresh()->password));
        $this->assertAuthenticatedAs($user);

        $this->patch(route('account.profile.edit'), ['name' => 'No update route'])->assertMethodNotAllowed();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'UpdatedPass123!'])->assertRedirect(route('account'));
        $this->assertAuthenticatedAs($user);
    }

    #[DataProvider('invalidPasswords')]
    public function test_invalid_password_changes_keep_the_existing_password(array $payload, array $errorFields): void
    {
        [, $user] = $this->verifiedResident();
        $originalHash = $user->password;

        $this->actingAs($user)->from(route('account.profile.edit'))
            ->patch(route('account.profile.password.update'), $payload)
            ->assertRedirect(route('account.profile.edit'))
            ->assertSessionHasErrors($errorFields, null, 'password')
            ->assertSessionMissing('_old_input.current_password')
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');

        $this->assertSame($originalHash, $user->fresh()->password);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @return array<string, array{array<string, string|array<string>>, list<string>}>
     */
    public static function invalidPasswords(): array
    {
        $valid = ['current_password' => 'password', 'password' => 'ChangedPass123!', 'password_confirmation' => 'ChangedPass123!'];

        return [
            'incorrect current password' => [[...$valid, 'current_password' => 'wrong-password'], ['current_password']],
            'short new password' => [[...$valid, 'password' => 'short', 'password_confirmation' => 'short'], ['password']],
            'confirmation mismatch' => [[...$valid, 'password_confirmation' => 'SomethingElse123'], ['password']],
            'same password' => [[...$valid, 'password' => 'password', 'password_confirmation' => 'password'], ['password']],
            'array instead of password' => [[...$valid, 'password' => ['invalid']], ['password']],
        ];
    }

    public function test_resident_password_checks_are_rate_limited(): void
    {
        [, $user] = $this->verifiedResident();
        $this->actingAs($user);
        $payload = ['current_password' => 'incorrect', 'password' => 'ChangedPass123!', 'password_confirmation' => 'ChangedPass123!'];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->patchJson(route('account.profile.password.update'), $payload)->assertUnprocessable();
        }

        $this->patchJson(route('account.profile.password.update'), $payload)->assertTooManyRequests();
    }

    /**
     * @return array{Resident, User}
     */
    private function verifiedResident(): array
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Jannyca',
            'middle_name' => 'Donaire',
            'last_name' => 'Ilaida',
            'contact_number' => '09912197679',
            'address' => 'Zone 1, Barangay Sample',
        ]);
        $user = User::factory()->resident()->create([
            'resident_id' => $resident->id,
            'name' => $resident->full_name,
            'email' => 'jannyca@example.test',
        ]);

        return [$resident, $user];
    }
}
