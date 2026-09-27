<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgotPasswordDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_forgot_password_design_from_login(): void
    {
        $this->get(route('login'))->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee(route('password.request'), false);

        $this->get(route('password.request'))->assertOk()
            ->assertSee('Forgot your password?')
            ->assertSee('Design-only screen')
            ->assertSee('Email delivery is not connected yet')
            ->assertSee('data-password-recovery-preview', false)
            ->assertSee(route('login'), false)
            ->assertDontSee('name="password"', false);
    }

    public function test_authenticated_users_are_redirected_away_from_forgot_password_design(): void
    {
        $staff = User::factory()->create();
        $this->actingAs($staff)->get(route('password.request'))->assertRedirect(route('dashboard'));

        $resident = User::factory()->resident()->create();
        $this->actingAs($resident)->get(route('password.request'))->assertRedirect(route('account'));
    }

    public function test_design_does_not_expose_a_password_reset_submission_endpoint(): void
    {
        $route = app('router')->getRoutes()->getByName('password.request');

        $this->assertNotNull($route);
        $this->assertSame(['GET', 'HEAD'], $route->methods());
        $this->post('/forgot-password', ['email' => 'resident@example.test'])->assertMethodNotAllowed();
    }
}
