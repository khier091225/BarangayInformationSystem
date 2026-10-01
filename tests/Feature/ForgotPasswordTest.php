<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_forgot_password_form_from_login(): void
    {
        $this->get(route('login'))->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee(route('password.request'), false);

        $this->get(route('password.request'))->assertOk()
            ->assertSee('Forgot your password?')
            ->assertSee('Send reset link')
            ->assertSee('Use your registered email')
            ->assertSee(route('password.email'), false)
            ->assertDontSee('Design-only screen');
    }

    public function test_authenticated_users_are_redirected_away_from_password_recovery(): void
    {
        $staff = User::factory()->create();
        $this->actingAs($staff)->get(route('password.request'))->assertRedirect(route('dashboard'));

        $resident = User::factory()->resident()->create();
        $this->actingAs($resident)->get(route('password.request'))->assertRedirect(route('account'));
    }

    public function test_staff_and_residents_can_request_a_password_reset_link(): void
    {
        Notification::fake();

        $staff = User::factory()->create();
        $resident = User::factory()->resident()->create();

        foreach ([$staff, $resident] as $user) {
            $this->from(route('password.request'))
                ->post(route('password.email'), ['email' => $user->email])
                ->assertRedirect(route('password.request'))
                ->assertSessionHasNoErrors()
                ->assertSessionHas('status', 'A password reset link has been sent to your email address.');

            Notification::assertSentTo($user, ResetPassword::class);
            $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

            $this->get(route('password.request'))
                ->assertSee('A password reset link has been sent to your email address.');
        }
    }

    public function test_unregistered_email_shows_an_error_without_sending_a_reset_link(): void
    {
        Notification::fake();

        $this->followingRedirects()->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'missing@example.test'])
            ->assertSee('This email address is not registered.')
            ->assertSee('missing@example.test')
            ->assertSee('aria-invalid="true"', false)
            ->assertDontSee('auth-alert--success', false);

        Notification::assertNothingSent();
        $this->assertDatabaseEmpty('password_reset_tokens');
    }

    public function test_requesting_another_link_too_soon_shows_an_error_and_keeps_the_existing_token(): void
    {
        $this->freezeTime();
        Notification::fake();
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors([
                'email' => 'A reset link was recently sent. Please wait before requesting another one.',
            ])
            ->assertSessionMissing('status');

        Notification::assertNothingSent();
        $this->assertTrue(Password::tokenExists($user, $token));
    }

    public function test_user_can_reset_their_password_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->resident()->create();
        $originalRememberToken = $user->remember_token;

        $this->post(route('password.email'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function (ResetPassword $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            },
        );

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Create a new password')
            ->assertSee($user->email)
            ->assertSee(route('password.update'), false);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'a-new-secure-password',
            'password_confirmation' => 'a-new-secure-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Your password has been reset. You can now sign in.');

        $user->refresh();

        $this->assertTrue(Hash::check('a-new-secure-password', $user->password));
        $this->assertNotSame($originalRememberToken, $user->remember_token);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'another-secure-password',
            'password_confirmation' => 'another-secure-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_invalid_reset_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'a-new-secure-password',
            'password_confirmation' => 'a-new-secure-password',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_reset_email_requests_are_rate_limited(): void
    {
        Notification::fake();

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post(route('password.email'), ['email' => "missing{$attempt}@example.test"])
                ->assertRedirect();
        }

        $this->post(route('password.email'), ['email' => 'missing4@example.test'])
            ->assertTooManyRequests();
    }
}
