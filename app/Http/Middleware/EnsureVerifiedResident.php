<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedResident
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'resident') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('warning', 'This page is only available to verified residents.');
        }

        if ($request->user()->resident_id === null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resident verification required.'], 403);
            }

            return redirect()->route('account')
                ->with('warning', 'Verify your resident record before submitting a request.');
        }

        return $next($request);
    }
}
