<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_directly_on_root(): void
    {
        $response = $this->get('/?role=admin')
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Management Dashboard')
            ->assertSee('BARANGAY ADMINISTRATION')
            ->assertSee('Total Residents')
            ->assertSee('Households')
            ->assertSee('Certificates')
            ->assertSee('Pending Blotters')
            ->assertSee('Recent Certificate Requests')
            ->assertSee('Recent Blotter Cases')
            ->assertSee('Newly Registered Residents')
            ->assertDontSee('Sample data')
            ->assertDontSee('Demo')
            ->assertDontSee('This is a demo')
            ->assertDontSee('Illustrative community snapshot');

        $this->assertStringStartsWith('<!DOCTYPE html>', ltrim($response->getContent()));
        $this->assertMatchesRegularExpression('/<title>\s*Dashboard \| Barangay Information System\s*<\/title>/', $response->getContent());
        $response->assertDontSee('@endsection', false);
    }

    public function test_dashboard_path_redirects_to_root(): void
    {
        $this->get('/dashboard?role=admin')->assertRedirect('/?role=admin');
    }

    public function test_login_is_public_and_no_logout_session_is_required(): void
    {
        $this->get('/login')->assertOk()->assertSee('Staff sign in');
        $this->post('/login')->assertSessionHasErrors(['email', 'password']);
        $this->post('/logout')->assertNotFound();
    }

    public function test_dashboard_does_not_contain_public_website_or_signout(): void
    {
        $this->get('/?role=admin')
            ->assertDontSee('Public website')
            ->assertDontSee('title="Sign out"', false)
            ->assertDontSee('data-demo-entry', false);
    }
}
