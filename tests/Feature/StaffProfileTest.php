<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_open_their_profile_and_find_it_in_navigation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk()
            ->assertSee('My profile')->assertSee('Change password')
            ->assertSee($user->name)->assertSee($user->email)
            ->assertSee(route('profile.update'), false)
            ->assertSee(route('profile.password.update'), false)
            ->assertDontSee('name="role"', false);
        $this->get(route('dashboard'))->assertOk()->assertSee(route('profile.edit'), false);
    }

    public function test_guests_and_residents_cannot_access_or_modify_staff_profiles(): void
    {
        $staff = User::factory()->create();
        $originalHash = $staff->password;
        $payload = ['id' => $staff->id, 'name' => 'Changed', 'email' => 'changed@example.test', 'current_password' => 'password', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'];

        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->patch(route('profile.update'), $payload)->assertRedirect(route('login'));
        $this->patch(route('profile.password.update'), $payload)->assertRedirect(route('login'));
        $this->patchJson(route('profile.password.update'), $payload)->assertUnauthorized();

        $resident = User::factory()->resident()->create();
        $this->actingAs($resident)->get(route('profile.edit'))->assertRedirect(route('account'));
        $this->patchJson(route('profile.update'), $payload)->assertForbidden();
        $this->patchJson(route('profile.password.update'), $payload)->assertForbidden();
        $this->assertSame($originalHash, $staff->fresh()->password);
        $this->assertSame($staff->email, $staff->fresh()->email);
        $this->assertSame('resident', $resident->fresh()->role);
    }

    public function test_profile_update_changes_only_the_signed_in_staff_name_and_email(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $originalHash = $user->password;

        $this->actingAs($user)->patch(route('profile.update'), [
            'id' => $otherUser->id, 'name' => 'Updated Staff Name', 'email' => ' UPDATED@example.test ',
            'role' => 'resident', 'resident_id' => 999, 'password' => 'InjectedPassword',
            'email_verified_at' => now()->toDateTimeString(),
        ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors()->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Updated Staff Name', $user->name);
        $this->assertSame('updated@example.test', $user->email);
        $this->assertSame('staff', $user->role);
        $this->assertNull($user->resident_id);
        $this->assertNull($user->email_verified_at);
        $this->assertSame($originalHash, $user->password);
        $this->assertSame($otherUser->email, $otherUser->fresh()->email);
        $this->get(route('dashboard'))->assertOk()->assertSee('Updated Staff Name');
    }

    public function test_keeping_the_same_email_preserves_its_verification(): void
    {
        $user = User::factory()->create();
        $verifiedAt = $user->email_verified_at->toDateTimeString();

        $this->actingAs($user)->patch(route('profile.update'), ['name' => 'Changed Name', 'email' => $user->email])
            ->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors();
        $this->assertSame($verifiedAt, $user->fresh()->email_verified_at->toDateTimeString());
    }

    public function test_duplicate_or_invalid_profile_details_are_rejected_and_redisplayed(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $this->from(route('profile.edit'))->patch(route('profile.update'), ['name' => '', 'email' => 'invalid'])
            ->assertRedirect(route('profile.edit'))->assertSessionHasErrors(['name', 'email'], null, 'profile');
        $this->patch(route('profile.update'), ['name' => 'Attempted Name', 'email' => $otherUser->email])
            ->assertSessionHasErrors('email', null, 'profile')->assertSessionHasInput('name', 'Attempted Name');
        $this->withCookie(config('session.cookie'), session()->getId())->get(route('profile.edit'))
            ->assertOk()->assertSee('The email has already been taken.')->assertSee('Attempted Name');

        $this->assertSame($user->email, $user->fresh()->email);
        $this->assertSame($user->name, $user->fresh()->name);
    }

    public function test_password_change_updates_the_hash_rotates_the_session_and_accepts_only_the_new_password(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $oldToken = $user->remember_token;
        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $oldSessionId = session()->getId();

        $this->patch(route('profile.password.update'), [
            'current_password' => 'password', 'password' => 'UpdatedPass123!', 'password_confirmation' => 'UpdatedPass123!',
            'id' => $otherUser->id, 'name' => 'Injected Name', 'email' => 'injected@example.test', 'role' => 'resident',
        ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors()->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('UpdatedPass123!', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertNotSame('UpdatedPass123!', $user->password);
        $this->assertNotSame($oldToken, $user->remember_token);
        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertSame('staff', $user->role);
        $this->assertNotSame('Injected Name', $user->name);
        $this->assertNotSame('injected@example.test', $user->email);
        $this->assertTrue(Hash::check('password', $otherUser->fresh()->password));
        $this->assertAuthenticatedAs($user);
        $this->get(route('profile.edit'))->assertOk()->assertDontSee('UpdatedPass123!');

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'UpdatedPass123!'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[DataProvider('invalidPasswords')]
    public function test_invalid_password_changes_keep_the_existing_password_and_never_flash_secrets(array $payload, array $errorFields): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->actingAs($user)->from(route('profile.edit'))->patch(route('profile.password.update'), $payload)
            ->assertRedirect(route('profile.edit'))->assertSessionHasErrors($errorFields, null, 'password')
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
            'missing current password' => [[...$valid, 'current_password' => ''], ['current_password']],
            'short new password' => [[...$valid, 'password' => 'short', 'password_confirmation' => 'short'], ['password']],
            'confirmation mismatch' => [[...$valid, 'password_confirmation' => 'SomethingElse123'], ['password']],
            'missing confirmation' => [[...$valid, 'password_confirmation' => ''], ['password', 'password_confirmation']],
            'same password' => [[...$valid, 'password' => 'password', 'password_confirmation' => 'password'], ['password']],
            'long password' => [[...$valid, 'password' => str_repeat('x', 73), 'password_confirmation' => str_repeat('x', 73)], ['password']],
            'long multibyte password' => [[...$valid, 'password' => str_repeat('é', 37), 'password_confirmation' => str_repeat('é', 37)], ['password']],
            'unsupported null byte' => [[...$valid, 'password' => "new\0password123", 'password_confirmation' => "new\0password123"], ['password']],
            'array instead of password' => [[...$valid, 'password' => ['invalid']], ['password']],
        ];
    }

    public function test_password_checks_are_rate_limited(): void
    {
        $this->actingAs(User::factory()->create());
        $payload = ['current_password' => 'incorrect', 'password' => 'ChangedPass123!', 'password_confirmation' => 'ChangedPass123!'];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->patchJson(route('profile.password.update'), $payload)->assertUnprocessable();
        }

        $this->patchJson(route('profile.password.update'), $payload)->assertTooManyRequests();
    }
}
