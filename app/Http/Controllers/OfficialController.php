<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Http\Request;

class OfficialController extends Controller
{
    /**
     * Display a listing of barangay officials.
     */
    public function index(Request $request)
    {
        /*
        $query = Official::query();

        // Search by name or position
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Display Captain first, then Kagawads, etc.
        $officials = $query->orderBy('name')->paginate(10)->withQueryString();
        */

        $officials = Official::orderby('name')->paginate(10);

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
