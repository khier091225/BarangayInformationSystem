<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\View\View;

class DemoWorkspaceController extends Controller
{
    public function dashboard(): View
    {
        $residentCount = Resident::count();
        $householdCount = Household::count();
        $certificateCount = Certificate::count();
        $pendingBlotterCount = Blotter::where('status', 'Pending')->count();
        $totalBlotterCount = Blotter::count();
        $officialCount = Official::count();

        $recentCertificates = Certificate::with('resident')->latest()->take(5)->get();
        $recentBlotters = Blotter::latest()->take(5)->get();
        $recentResidents = Resident::with('household')->latest()->take(5)->get();

        return view('dashboard', compact(
            'residentCount',
            'householdCount',
            'certificateCount',
            'pendingBlotterCount',
            'totalBlotterCount',
            'officialCount',
            'recentCertificates',
            'recentBlotters',
            'recentResidents'
        ));
    }
}
