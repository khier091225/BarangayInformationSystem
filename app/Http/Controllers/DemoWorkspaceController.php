<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DemoWorkspaceController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard');
    }
}
