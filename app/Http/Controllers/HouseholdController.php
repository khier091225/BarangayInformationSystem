<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'household_head' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $creationYear = now('Asia/Manila')->year;

        $household = DB::transaction(function () use ($validated, $creationYear): Household {
            $household = Household::create([
                ...$validated,
                'household_number' => 'PENDING-'.Str::uuid(),
            ]);

            $baseNumber = sprintf('HH-%d-%06d', $creationYear, $household->id);
            $householdNumber = $baseNumber;
            $suffix = 1;

            while (Household::where('household_number', $householdNumber)->exists()) {
                $householdNumber = $baseNumber.'-'.$suffix;
                $suffix++;
            }

            $household->update(['household_number' => $householdNumber]);

            return $household;
        });

        return redirect()->route('households.index')
            ->with('success', "Household {$household->household_number} added successfully!");
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

    public function update(Request $request, Household $household): RedirectResponse
    {
        $validated = $request->validate([
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
