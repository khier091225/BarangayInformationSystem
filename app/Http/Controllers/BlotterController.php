<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use Illuminate\Http\Request;

class BlotterController extends Controller
{
    public function index(Request $request)
    {
        $query = Blotter::query();

        // Search by complainant, respondent, or incident details
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('complainant', 'like', "%{$search}%")
                    ->orWhere('respondent', 'like', "%{$search}%")
                    ->orWhere('incident', 'like', "%{$search}%");
            });
        }

        // Filter by status (Pending, Settled, Dismissed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $blotters = $query->latest('incident_date')->paginate(10);

        return view('blotters.index', compact('blotters'));
    }

    public function create()
    {
        return view('blotters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complainant' => 'required|string|max:255',
            'respondent' => 'required|string|max:255',
            'incident' => 'required|string',
            'incident_date' => 'required|date',
            'status' => 'required|in:Pending,Settled,Dismissed',
        ]);

        Blotter::create($validated);

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter report recorded successfully!');
    }

    public function show(Blotter $blotter)
    {
        return view('blotters.show', compact('blotter'));
    }

    public function edit(Blotter $blotter)
    {
        return view('blotters.edit', compact('blotter'));
    }

    public function update(Request $request, Blotter $blotter)
    {
        $validated = $request->validate([
            'complainant' => 'required|string|max:255',
            'respondent' => 'required|string|max:255',
            'incident' => 'required|string',
            'incident_date' => 'required|date',
            'status' => 'required|in:Pending,Settled,Dismissed',
        ]);

        $blotter->update($validated);

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter record updated successfully!');
    }

    public function destroy(Blotter $blotter)
    {
        $blotter->delete();

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter record deleted successfully!');
    }
}
