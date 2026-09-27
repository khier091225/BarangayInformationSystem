<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Household;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public barangay homepage and e-services portal.
     */
    public function index(): View
    {
        $officials = Official::query()
            ->orderByRaw("CASE 
                WHEN position LIKE '%Captain%' OR position LIKE '%Punong%' THEN 1 
                WHEN position LIKE '%Kagawad%' THEN 2 
                WHEN position LIKE '%SK%' THEN 3 
                WHEN position LIKE '%Secretary%' THEN 4 
                WHEN position LIKE '%Treasurer%' THEN 5 
                ELSE 6 
            END")
            ->orderBy('name')
            ->get();

        $stats = [
            'residents' => Resident::count(),
            'households' => Household::count(),
            'certificates' => Certificate::count(),
            'male' => Resident::where('gender', 'Male')->count(),
            'female' => Resident::where('gender', 'Female')->count(),
            'seniors' => Resident::where('birthdate', '<=', now()->subYears(60))->count(),
            'voters' => Resident::where('is_voter', true)->count(),
        ];

        return view('home', compact('officials', 'stats'));
    }
}
