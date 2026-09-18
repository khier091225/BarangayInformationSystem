<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoWorkspaceTest extends TestCase
{
    public function test_login_page_has_a_credential_free_entry_form(): void
    public function test_dashboard_loads_directly_on_root(): void
    {
        $this->get(route('login'))
        $this->get('/')
            ->assertOk()
            ->assertSee('Enter dashboard')
            ->assertSee('No credentials required. Sample data only.')
            ->assertDontSee('type="password"', false)
            ->assertDontSee('name="username"', false);
    }

    public function test_dashboard_requires_entering_the_demo_workspace(): void
    {
        $this->get(route('dashboard'))->assertRedirectToRoute('login');
    }

    public function test_entering_without_credentials_opens_a_demo_session(): void
    {
        $this->post(route('demo.enter'))
            ->assertRedirectToRoute('dashboard')
            ->assertSessionHas('demo_workspace', true);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Sample data')
            ->assertSee('Recent certificate requests')
            ->assertSee('Illustrative community snapshot')
            ->assertSee('Added residents last until you reload.')
            ->assertSee('id="workspace-main" tabindex="-1"', false)
            ->assertSee('id="records-filter"', false)
            ->assertSee('View chart data')
            ->assertSee('id="first-name-error"', false);
    }

        $this->assertGuest();
    public function test_dashboard_path_redirects_to_root(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_existing_demo_session_skips_the_login_page(): void
    public function test_login_and_logout_routes_no_longer_exist(): void
    {
        $this->withSession(['demo_workspace' => true])
            ->get(route('login'))
            ->assertRedirectToRoute('dashboard');
        $this->get('/login')->assertNotFound();
        $this->post('/login')->assertNotFound();
        $this->post('/logout')->assertNotFound();
    }

    public function test_signing_out_clears_demo_access(): void
    public function test_dashboard_does_not_contain_public_website_or_signout(): void
    {
        $this->withSession(['demo_workspace' => true])
            ->post(route('logout'))
            ->assertRedirectToRoute('login')
            ->assertSessionMissing('demo_workspace');

        $this->get(route('dashboard'))->assertRedirectToRoute('login');
        $this->get('/')
            ->assertDontSee('Public website')
            ->assertDontSee('title="Sign out"', false)
            ->assertDontSee('data-demo-entry', false);
    }
}
