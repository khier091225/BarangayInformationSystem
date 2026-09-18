<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    /**
     * Display a listing of residents.
     */
    public function index(Request $request)
    {
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

        $residents = $query->latest()->paginate(10);

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_id' => 'nullable|exists:households,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
        ]);

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
    public function update(Request $request, Resident $resident)
    {
        $validated = $request->validate([
            'household_id' => 'nullable|exists:households,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
        ]);

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
