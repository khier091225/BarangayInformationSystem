<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_credentials_redirect_with_role_without_authenticating_the_session(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', ['role' => 'admin']))
            ->assertSessionMissing('role');

        $this->assertGuest();
        $this->get(route('dashboard', ['role' => 'admin']))->assertOk();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
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

    #[DataProvider('unauthorizedQueries')]
    public function test_missing_or_invalid_query_role_redirects_with_a_warning(array $query): void
    {
        $this->get(route('dashboard', $query))
            ->assertRedirect(route('login'))
            ->assertSessionHas('warning', 'You do not have permission to access this page. Please sign in.');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('You do not have permission to access this page. Please sign in.');
    }

    /**
     * @return array<string, array{array<string, string|array<string>>}>
     */
    public static function unauthorizedQueries(): array
    {
        return [
            'missing' => [[]],
            'wrong role' => [['role' => 'resident']],
            'wrong case' => [['role' => 'Admin']],
            'empty' => [['role' => '']],
            'array' => [['role' => ['admin']]],
            'old parameter' => [['user' => 'admin']],
        ];
    }

    public function test_session_or_form_role_cannot_replace_the_query_parameter(): void
    {
        $this->withSession(['role' => 'admin'])->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->post(route('residents.store'), ['role' => 'admin'])
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('residents', 0);
    }

    public function test_unauthorized_updates_and_deletes_do_not_change_records(): void
    {
        $resident = Resident::factory()->create(['first_name' => 'Original']);

        $this->put(route('residents.update', $resident), ['first_name' => 'Changed'])
            ->assertRedirect(route('login'));
        $this->delete(route('residents.destroy', $resident))->assertRedirect(route('login'));

        $this->assertSame('Original', $resident->fresh()->first_name);
    }

    public function test_json_requests_without_role_are_forbidden(): void
    {
        $this->getJson(route('residents.index'))->assertForbidden()
            ->assertJson(['message' => 'Unauthorized access.']);
    }

    public function test_management_routes_use_check_role(): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() === '/' || $route->uri() === 'dashboard'
                || preg_match('/^(residents|households|blotters|officials|certificates)(\/|$)/', $route->uri())) {
                $this->assertContains('role:admin', $route->gatherMiddleware(), $route->uri());
            }
        }
    }

    #[DataProvider('listings')]
    public function test_navigation_forms_and_pagination_preserve_role(string $resource, string $model): void
    {
        $model::factory()->count(11)->create();

        $response = $this->get(route($resource.'.index', ['role' => 'admin']))->assertOk();
        $response->assertSee(route($resource.'.create', ['role' => 'admin']))
            ->assertSee('name="role" value="admin"', false);

        $nextPageUrl = $response->viewData($resource)->nextPageUrl();
        $this->assertNotNull($nextPageUrl);
        parse_str(parse_url($nextPageUrl, PHP_URL_QUERY), $query);
        $this->assertSame('admin', $query['role']);
        $this->get($nextPageUrl)->assertOk();

        $this->get(route($resource.'.create', ['role' => 'admin']))->assertOk()
            ->assertSee(route($resource.'.store', ['role' => 'admin']));
    }

    /**
     * @return array<string, array{string, class-string<Model>}>
     */
    public static function listings(): array
    {
        return [
            'residents' => ['residents', Resident::class],
            'households' => ['households', Household::class],
            'blotters' => ['blotters', Blotter::class],
            'officials' => ['officials', Official::class],
            'certificates' => ['certificates', Certificate::class],
        ];
    }
}
