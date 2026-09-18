<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoWorkspaceTest extends TestCase
{
    public function test_login_page_has_a_credential_free_entry_form(): void
    {
        $this->get(route('login'))
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
            ->assertSee('Sample data')
            ->assertSee('Recent certificate requests')
            ->assertSee('Illustrative community snapshot')
            ->assertSee('Added residents last until you reload.')
            ->assertSee('id="workspace-main" tabindex="-1"', false)
            ->assertSee('id="records-filter"', false)
            ->assertSee('View chart data')
            ->assertSee('id="first-name-error"', false);

        $this->assertGuest();
    }

    public function test_existing_demo_session_skips_the_login_page(): void
    {
        $this->withSession(['demo_workspace' => true])
            ->get(route('login'))
            ->assertRedirectToRoute('dashboard');
    }

    public function test_signing_out_clears_demo_access(): void
    {
        $this->withSession(['demo_workspace' => true])
            ->post(route('logout'))
            ->assertRedirectToRoute('login')
            ->assertSessionMissing('demo_workspace');

        $this->get(route('dashboard'))->assertRedirectToRoute('login');
    }
}
