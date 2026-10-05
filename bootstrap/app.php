<?php

use App\Http\Middleware\EnsureStaffSession;
use App\Http\Middleware\EnsureVerifiedResident;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: ['payments/webhook/paymongo']);

        $middleware->alias([
            'staff.session' => EnsureStaffSession::class,
            'resident.verified' => EnsureVerifiedResident::class,
        ]);

        $middleware->redirectUsersTo(fn (Request $request): string => $request->user()->role === 'staff'
            ? route('dashboard')
            : route('account'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['registration_code']);
        $exceptions->render(function (ThrottleRequestsException $exception, Request $request): ?RedirectResponse {
            if (! $request->routeIs('login.store') || $request->expectsJson()) {
                return null;
            }

            $retryAfter = max(1, (int) ($exception->getHeaders()['Retry-After'] ?? 60));
            $email = $request->input('email');

            return redirect()->route('login')
                ->withInput(['email' => is_string($email) ? $email : ''])
                ->with('warning', "Too many sign-in attempts. Please wait {$retryAfter} seconds, then try again.");
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*', 'payments/webhook/*') || $request->expectsJson(),
        );
    })->create();
