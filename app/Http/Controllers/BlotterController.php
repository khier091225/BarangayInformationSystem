<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveBlotterRequest;
use App\Models\Blotter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlotterController extends Controller
{
    public function index(Request $request)
    {
        /*
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

        $blotters = $query->latest('incident_date')->paginate(10)->withQueryString();
        */

        $blotters = Blotter::latest('incident_date')->paginate(10)->appends(['role' => 'admin']);

        return view('blotters.index', compact('blotters'));
    }

    public function create()
    {
        return view('blotters.create');
    }

    public function store(SaveBlotterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Blotter::create($validated);

        return redirect()->route('blotters.index', ['role' => 'admin'])
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

    public function update(SaveBlotterRequest $request, Blotter $blotter): RedirectResponse
    {
        $validated = $request->validated();

        $blotter->update($validated);

        return redirect()->route('blotters.index', ['role' => 'admin'])
            ->with('success', 'Blotter record updated successfully!');
    }

    public function destroy(Blotter $blotter)
    {
        $blotter->delete();

        return redirect()->route('blotters.index', ['role' => 'admin'])
            ->with('success', 'Blotter record deleted successfully!');
    }
}
