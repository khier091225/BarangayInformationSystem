<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_directly_on_root(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/')
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Barangay overview')
            ->assertSee('BARANGAY ADMINISTRATION')
            ->assertSee('Total residents')
            ->assertSee('Households')
            ->assertSee('Certificates')
            ->assertSee('Pending blotters')
            ->assertSee('Recently issued certificates')
            ->assertSee('Recent blotter cases')
            ->assertSee('Newly registered residents')
            ->assertSee('No requests waiting')
            ->assertSee('No certificates issued in this period.')
            ->assertViewHas('certificateActivity', fn (array $activity): bool => $activity['total'] === 0
                && $activity['currentMonth'] === 0 && count($activity['months']) === 6)
            ->assertDontSee('Sample data')
            ->assertDontSee('Demo')
            ->assertDontSee('This is a demo')
            ->assertDontSee('Illustrative community snapshot');

        $this->assertStringStartsWith('<!DOCTYPE html>', ltrim($response->getContent()));
        $this->assertMatchesRegularExpression('/<title>\s*Dashboard \| Barangay Information System\s*<\/title>/', $response->getContent());
        $response->assertDontSee('@endsection', false);
    }

    public function test_certificate_chart_uses_issuance_dates_and_includes_empty_months_across_years(): void
    {
        $this->travelTo(now()->setDate(2027, 1, 15)->startOfDay());
        $resident = Resident::factory()->create();

        Certificate::factory()->for($resident)->count(2)->create(['date_issued' => '2026-08-01']);
        Certificate::factory()->for($resident)->create(['date_issued' => '2026-12-31']);
        Certificate::factory()->for($resident)->count(3)->create(['date_issued' => '2027-01-01']);
        Certificate::factory()->for($resident)->create(['date_issued' => '2027-01-15']);
        Certificate::factory()->for($resident)->create(['date_issued' => '2026-07-31']);
        Certificate::factory()->for($resident)->create(['date_issued' => '2027-01-16']);

        $response = $this->actingAs(User::factory()->create())->get('/')->assertOk();
        $activity = $response->viewData('certificateActivity');

        $this->assertSame([2, 0, 0, 0, 1, 4], array_column($activity['months'], 'count'));
        $this->assertSame('August 2026', $activity['months'][0]['fullLabel']);
        $this->assertSame('January 2027', $activity['months'][5]['fullLabel']);
        $this->assertSame(7, $activity['total']);
        $this->assertSame(4, $activity['currentMonth']);
    }

    public function test_review_queue_prioritizes_oldest_pending_requests_and_counts_each_status(): void
    {
        $resident = Resident::factory()->create();
        $pending = ServiceRequest::factory()->for($resident)->count(5)->sequence(
            ['created_at' => now()->subDays(5)],
            ['created_at' => now()->subDays(4)],
            ['created_at' => now()->subDays(3)],
            ['created_at' => now()->subDays(2)],
            ['created_at' => now()->subDay()],
        )->create();
        ServiceRequest::factory()->for($resident)->count(2)->create([
            'status' => ServiceRequest::STATUS_COMPLETED, 'created_at' => now()->subMonth(),
        ]);
        ServiceRequest::factory()->for($resident)->create([
            'status' => ServiceRequest::STATUS_DECLINED, 'created_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs(User::factory()->create())->get('/')->assertOk()
            ->assertSee(route('service-requests.show', $pending->first()))
            ->assertSee(route('service-requests.index', ['status' => 'Pending']), false)
            ->assertSee(route('blotters.index', ['status' => 'Pending']), false);

        $this->assertSame($pending->take(4)->modelKeys(), $response->viewData('pendingRequests')->modelKeys());
        $this->assertSame(['Pending' => 5, 'Completed' => 2, 'Declined' => 1], $response->viewData('requestCounts'));
        $this->assertSame(8, $response->viewData('totalServiceRequestCount'));
        $this->assertSame(5, $response->viewData('pendingServiceRequestCount'));
    }

    public function test_dashboard_path_redirects_to_root(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertRedirect('/');
    }

    public function test_login_is_public_and_logout_requires_authentication(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
        $this->post('/login')->assertSessionHasErrors(['email', 'password']);
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_dashboard_does_not_contain_public_website_or_signout(): void
    {
        $this->actingAs(User::factory()->create())->get('/')
            ->assertDontSee('Public website')
            ->assertDontSee('title="Sign out"', false)
            ->assertDontSee('data-demo-entry', false);
    }
}
