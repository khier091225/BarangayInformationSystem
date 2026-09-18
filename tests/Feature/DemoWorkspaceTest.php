<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoWorkspaceTest extends TestCase
{
    public function test_dashboard_loads_directly_on_root(): void
    {
        $this->get('/')
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

    public function test_dashboard_path_redirects_to_root(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_login_and_logout_routes_no_longer_exist(): void
    {
        $this->get('/login')->assertNotFound();
        $this->post('/login')->assertNotFound();
        $this->post('/logout')->assertNotFound();
    }

    public function test_dashboard_does_not_contain_public_website_or_signout(): void
    {
        $this->get('/')
            ->assertDontSee('Public website')
            ->assertDontSee('title="Sign out"', false)
            ->assertDontSee('data-demo-entry', false);
    }
}
