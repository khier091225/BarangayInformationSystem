<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

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
    public function create(): View
    {
        return view('officials.create');
    }

    /**
     * Store a newly created official in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        unset($validated['photo']);
        $photoPath = $request->file('photo')?->store('officials', 'public');

        if ($photoPath === false) {
            abort(500, 'The official photo could not be saved. Please try again.');
        }

        if ($photoPath !== null) {
            $validated['image_path'] = $photoPath;
        }

        try {
            Official::create($validated);
        } catch (Throwable $exception) {
            if ($photoPath !== null) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official added successfully!');
    }

    /**
     * Show the form for editing the specified official.
     */
    public function edit(Official $official): View
    {
        return view('officials.edit', compact('official'));
    }

    /**
     * Update the specified official in storage.
     */
    public function update(Request $request, Official $official): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        unset($validated['photo'], $validated['remove_photo']);
        $photoPath = $request->file('photo')?->store('officials', 'public');

        if ($photoPath === false) {
            abort(500, 'The official photo could not be saved. Please try again.');
        }

        $removePhoto = $request->boolean('remove_photo');
        $previousPhotoPath = $official->image_path;

        if ($photoPath !== null || $removePhoto) {
            $validated['image_path'] = $photoPath;
        }

        try {
            $official->update($validated);
        } catch (Throwable $exception) {
            if ($photoPath !== null) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        if (($photoPath !== null || $removePhoto) && $previousPhotoPath !== null) {
            Storage::disk('public')->delete($previousPhotoPath);
        }

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official updated successfully!');
    }

    /**
     * Remove the specified official from storage.
     */
    public function destroy(Official $official): RedirectResponse
    {
        $photoPath = $official->image_path;
        $official->delete();

        if ($photoPath !== null) {
            Storage::disk('public')->delete($photoPath);
        }

        return redirect()->route('officials.index')
            ->with('success', 'Barangay official removed successfully!');
    }
}
