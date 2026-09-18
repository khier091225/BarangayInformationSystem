<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemoWorkspaceController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    public function dashboard(): View
    {
        if ($request->session()->get('demo_workspace') === true) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function enter(Request $request): RedirectResponse
    {
        $request->session()->regenerate();
        $request->session()->put('demo_workspace', true);

        return redirect()->route('dashboard');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('demo_workspace') !== true) {
            return redirect()->route('login');
        }

        return view('dashboard');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
