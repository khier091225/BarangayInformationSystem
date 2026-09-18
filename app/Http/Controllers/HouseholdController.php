<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function index(Request $request)
    {
        // Kasama ang bilang ng mga residente (members) sa bawat bahay
        $query = Household::withCount('residents');

        // Search para sa household number, head, o address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('household_number', 'like', "%{$search}%")
                    ->orWhere('household_head', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $households = $query->latest()->paginate(10);

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
