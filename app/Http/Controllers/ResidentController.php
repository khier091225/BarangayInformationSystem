<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveResidentRequest;
use App\Models\Household;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentController extends Controller
{
    /**
     * Display a listing of residents.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Male,Female',
            'is_voter' => 'nullable|in:0,1',
        ]);

        $query = Resident::with('household');

        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($filters['search']));

            foreach ($terms as $term) {
                $query->where(function (Builder $residentQuery) use ($term): void {
                    $residentQuery->where('first_name', 'like', "%{$term}%")
                        ->orWhere('middle_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%")
                        ->orWhere('contact_number', 'like', "%{$term}%");
                });
            }
        }

        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (isset($filters['is_voter'])) {
            $query->where('is_voter', $filters['is_voter'] === '1');
        }

        $residents = $query->latest()->paginate(10)->withQueryString();

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
