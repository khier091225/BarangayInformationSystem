<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseholdController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $query = Household::withCount('residents');

        if ($request->filled('search')) {
            $search = $filters['search'];
            $query->where(function (Builder $householdQuery) use ($search): void {
                $householdQuery->where('household_number', 'like', "%{$search}%")
                    ->orWhere('household_head', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $households = $query->latest()->paginate(10)->withQueryString();

        return view('households.index', compact('households'));
    }

    public function create()
    {
        return view('households.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_number' => 'required|string|max:255|unique:households,household_number',
            'household_head' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        Household::create($validated);

        return redirect()->route('households.index')
            ->with('success', 'Household added successfully!');
    }

    public function show(Household $household)
    {
        $household->load('residents');

        return view('households.show', compact('household'));
    }

    public function edit(Household $household)
    {
        return view('households.edit', compact('household'));
    }

    public function update(Request $request, Household $household)
    {
        $validated = $request->validate([
            'household_number' => 'required|string|max:255|unique:households,household_number,'.$household->id,
            'household_head' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $household->update($validated);

        return redirect()->route('households.index')
            ->with('success', 'Household updated successfully!');
    }

    public function destroy(Household $household)
    {
        $household->delete();

        return redirect()->route('households.index')
            ->with('success', 'Household deleted successfully!');
    }
}
