<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficialController extends Controller
{
    /**
     * Display a listing of barangay officials.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $query = Official::query();

        if ($request->filled('search')) {
            $search = $filters['search'];
            $query->where(function (Builder $officialQuery) use ($search): void {
                $officialQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }

        $officials = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('officials.index', compact('officials'));
    }

    /**
     * Show the form for creating a new official.
     */
    public function create()
    {
        return view('officials.create');
    }

    /**
     * Store a newly created official in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
        ]);

        Official::create($validated);

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official added successfully!');
    }

    /**
     * Show the form for editing the specified official.
     */
    public function edit(Official $official)
    {
        return view('officials.edit', compact('official'));
    }

    /**
     * Update the specified official in storage.
     */
    public function update(Request $request, Official $official)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
        ]);

        $official->update($validated);

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official updated successfully!');
    }

    /**
     * Remove the specified official from storage.
     */
    public function destroy(Official $official)
    {
        $official->delete();

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official removed successfully!');
    }
}
