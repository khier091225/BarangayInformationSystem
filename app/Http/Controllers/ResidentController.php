<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveResidentRequest;
use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    /**
     * Display a listing of residents.
     */
    public function index(Request $request)
    {

        /*
        $query = Resident::with('household');

        // Search by name, address, or contact number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by voter status
        if ($request->filled('is_voter')) {
            $query->where('is_voter', $request->is_voter === '1');
        }

        $residents = $query->latest()->paginate(10)->withQueryString();
        */

        $residents = Resident::latest()->paginate(10);

        return view('residents.index', compact('residents'));
    }

    /**
     * Show the form for creating a new resident.
     */
    public function create()
    {
        $households = Household::orderBy('household_number')->get();

        return view('residents.create', compact('households'));
    }

    /**
     * Store a newly created resident in storage.
     */
    public function store(SaveResidentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_voter'] = $request->boolean('is_voter');

        Resident::create($validated);

        return redirect()->route('residents.index')
            ->with('success', 'Resident registered successfully!');
    }

    /**
     * Display the specified resident profile.
     */
    public function show(Resident $resident)
    {
        $resident->load(['household', 'certificates']);

        return view('residents.show', compact('resident'));
    }

    /**
     * Show the form for editing the specified resident.
     */
    public function edit(Resident $resident)
    {
        $households = Household::orderBy('household_number')->get();

        return view('residents.edit', compact('resident', 'households'));
    }

    /**
     * Update the specified resident in storage.
     */
    public function update(SaveResidentRequest $request, Resident $resident): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_voter'] = $request->boolean('is_voter');

        $resident->update($validated);

        return redirect()->route('residents.index')
            ->with('success', 'Resident details updated successfully!');
    }

    /**
     * Remove the specified resident from storage.
     */
    public function destroy(Resident $resident)
    {
        $resident->delete();

        return redirect()->route('residents.index')
            ->with('success', 'Resident record deleted successfully!');
    }
}
