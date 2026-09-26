<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_credentials_authenticate_and_open_protected_pages(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk()->assertSee($user->name);
        $this->get(route('households.index'))->assertOk();
        $this->get(route('dashboard', ['role' => 'resident']))->assertOk();
    }

    public function test_login_returns_to_the_requested_page(): void
    {
        $user = User::factory()->create();

        $this->get(route('residents.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('warning', 'Please sign in to access barangay records.');
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('residents.index'));
    }

    public function test_invalid_credentials_keep_email_but_never_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', $user->email)
            ->assertSessionMissing('_old_input.password');

        $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('login'))
            ->assertSee('The email address or password is incorrect.')
            ->assertSee($user->email)
            ->assertDontSee('incorrect-password');
        $this->assertGuest();
    }

    public function test_login_requires_valid_email_and_password(): void
    {
        $this->from(route('login'))->post(route('login.store'), ['email' => 'invalid'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_failed_login_attempts_are_throttled(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), ['email' => 'unknown@example.test', 'password' => 'incorrect'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), ['email' => 'unknown@example.test', 'password' => 'incorrect'])
            ->assertStatus(429);
    }

    #[DataProvider('protectedRoutes')]
    public function test_guests_cannot_bypass_login_with_a_role_parameter(string $method, string $route): void
    {
        $response = $this->{$method}(route($route, ['role' => 'admin']));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function protectedRoutes(): array
    {
        return [
            'dashboard' => ['get', 'dashboard'],
            'residents' => ['get', 'residents.index'],
            'households' => ['get', 'households.index'],
            'blotters' => ['get', 'blotters.index'],
            'officials' => ['get', 'officials.index'],
            'certificates' => ['get', 'certificates.index'],
            'record creation' => ['post', 'residents.store'],
        ];
    }

    public function test_guests_cannot_modify_existing_records(): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Original']);

        $this->put(route('residents.update', [$resident, 'role' => 'admin']), ['first_name' => 'Changed'])
            ->assertRedirect(route('login'));
        $this->delete(route('residents.destroy', [$resident, 'role' => 'admin']))
            ->assertRedirect(route('login'));

        $this->assertSame('Original', $resident->fresh()->first_name);
    }

    public function test_json_guests_receive_unauthorized_response(): void
    {
        $this->getJson(route('residents.index', ['role' => 'admin']))->assertUnauthorized();
    }

    public function test_logout_clears_authentication(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('name="_token"', false);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_users_skip_the_login_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('login'))->assertRedirect(route('dashboard'));
        $this->post(route('login.store'))->assertRedirect(route('dashboard'));
    }

    public function test_all_management_routes_use_the_session_middleware(): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() === '/' || $route->uri() === 'dashboard' || $route->uri() === 'logout'
                || preg_match('/^(residents|households|blotters|officials|certificates)(\/|$)/', $route->uri())) {
                $this->assertContains('staff.session', $route->gatherMiddleware(), $route->uri());
            }
        }
    }

    public function test_navigation_and_pagination_do_not_require_role_parameters(): void
    {
        $this->actingAs(User::factory()->create());
        Household::factory()->count(11)->create();

        $response = $this->get(route('households.index'))->assertOk();
        $response->assertSee(route('households.create'))
            ->assertDontSee('name="role"', false)
            ->assertDontSee('?role=admin');

        $nextPageUrl = $response->viewData('households')->nextPageUrl();
        $this->assertNotNull($nextPageUrl);
        parse_str(parse_url($nextPageUrl, PHP_URL_QUERY), $query);
        $this->assertSame('2', $query['page']);
        $this->assertArrayNotHasKey('role', $query);
        $this->get($nextPageUrl)->assertOk();

        $this->get(route('households.create'))->assertOk()
            ->assertSee(route('households.store'));
    }
}
