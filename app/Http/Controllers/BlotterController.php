<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveBlotterRequest;
use App\Models\Blotter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlotterController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:Pending,Settled,Dismissed',
        ]);

        $query = Blotter::query();

        if ($request->filled('search')) {
            $search = $filters['search'];
            $query->where(function (Builder $blotterQuery) use ($search): void {
                $blotterQuery->where('complainant', 'like', "%{$search}%")
                    ->orWhere('respondent', 'like', "%{$search}%")
                    ->orWhere('incident', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $blotters = $query->latest('incident_date')->paginate(10)->withQueryString();

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

    public function update(SaveBlotterRequest $request, Blotter $blotter): RedirectResponse
    {
        $validated = $request->validated();

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
