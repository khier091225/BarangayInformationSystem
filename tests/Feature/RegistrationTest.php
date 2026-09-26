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

    public function test_public_registration_creates_a_limited_account_and_signs_in(): void
    {
        $this->get(route('login'))->assertOk()->assertSee(route('register'));
        $this->get(route('register'))->assertOk()
            ->assertSee('Create an account')
            ->assertSee(route('login'));

        $this->post(route('register.store'), [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'staff',
        ])->assertRedirect(route('account'));

        $user = User::query()->where('email', 'juan@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('resident', $user->role);
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertSame(0, Resident::query()->count());
        $this->get(route('account'))->assertOk()->assertSee('Juan Dela Cruz');
    }

    public function test_resident_accounts_cannot_open_staff_records_even_with_a_role_parameter(): void
    {
        $user = User::factory()->resident()->create();
        $this->actingAs($user);

        $this->get(route('dashboard', ['role' => 'staff']))
            ->assertRedirect(route('account'))
            ->assertSessionHas('warning', 'This page is only available to barangay staff.');
        $this->get(route('residents.index', ['role' => 'admin']))->assertRedirect(route('account'));
        $this->post(route('residents.store', ['role' => 'staff']), [])->assertRedirect(route('account'));
        $this->getJson(route('residents.index', ['role' => 'staff']))->assertForbidden();
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_registration_rejects_duplicate_email_and_invalid_password(): void
    {
        User::factory()->create(['email' => 'existing@example.test']);

        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Another Person',
            'email' => 'existing@example.test',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password'])
            ->assertSessionHasInput('name', 'Another Person')
            ->assertSessionMissing('_old_input.password');

        $this->assertSame(1, User::query()->count());
        $this->assertGuest();
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
}
