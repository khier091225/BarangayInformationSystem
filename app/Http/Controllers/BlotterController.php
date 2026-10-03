<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveBlotterRequest;
use App\Http\Requests\UpdateBlotterStatusRequest;
use App\Models\Blotter;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlotterController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(Blotter::STATUSES)],
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

        Blotter::create([
            'complainant' => $validated['complainant'],
            'respondent' => trim($validated['respondent'] ?? '') ?: 'Unknown',
            'incident' => $validated['incident'],
            'incident_date' => $validated['incident_date'] ?? today('Asia/Manila')->toDateString(),
            'status' => Blotter::STATUS_PENDING,
        ]);

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter report recorded successfully!');
    }

    public function show(Blotter $blotter): View
    {
        $blotter->load(['assignee', 'updates.user', 'serviceRequest']);

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

    public function updateStatus(UpdateBlotterStatusRequest $request, Blotter $blotter): RedirectResponse
    {
        $validated = $request->validated();
        $updated = DB::transaction(function () use ($request, $blotter, $validated): bool {
            $record = Blotter::query()->lockForUpdate()->findOrFail($blotter->id);
            $action = $validated['action'];

            if ($action === 'accept') {
                if ($record->status !== Blotter::STATUS_PENDING) {
                    return false;
                }

                $record->update([
                    'status' => Blotter::STATUS_ACCEPTED,
                    'assigned_to' => $request->user()->id,
                    'accepted_at' => now(),
                ]);
                $message = filled($validated['message'] ?? null)
                    ? $validated['message']
                    : 'Your blotter case has been accepted for barangay mediation.';
            } else {
                abort_unless($record->assigned_to === $request->user()->id, 403);

                $expectedStatus = match ($action) {
                    'schedule' => Blotter::STATUS_ACCEPTED,
                    'start' => Blotter::STATUS_SCHEDULED,
                    'settle', 'dismiss' => Blotter::STATUS_MEDIATION,
                };

                if ($record->status !== $expectedStatus) {
                    return false;
                }

                $changes = match ($action) {
                    'schedule' => [
                        'status' => Blotter::STATUS_SCHEDULED,
                        'hearing_at' => Carbon::createFromFormat('!Y-m-d\TH:i', $validated['hearing_at'], 'Asia/Manila')->utc(),
                    ],
                    'start' => [
                        'status' => Blotter::STATUS_MEDIATION,
                        'mediation_started_at' => now(),
                    ],
                    'settle' => [
                        'status' => Blotter::STATUS_SETTLED,
                        'closed_at' => now(),
                    ],
                    'dismiss' => [
                        'status' => Blotter::STATUS_DISMISSED,
                        'closed_at' => now(),
                    ],
                };
                $record->update($changes);

                $message = filled($validated['message'] ?? null)
                    ? $validated['message']
                    : match ($action) {
                        'schedule' => 'Barangay mediation has been scheduled for '.$record->hearing_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A').'.',
                        'start' => 'Barangay mediation is now in progress.',
                        'settle' => 'The blotter case has been marked as settled.',
                        'dismiss' => 'The blotter case has been dismissed.',
                    };
            }

            $record->updates()->create([
                'user_id' => $request->user()->id,
                'status' => $record->status,
                'message' => $message,
            ]);

            return true;
        });

        return redirect()->route('blotters.show', $blotter)
            ->with($updated ? 'success' : 'warning', $updated
                ? 'Blotter case progress updated successfully.'
                : 'This blotter case has already moved to another status.');
    }

    public function destroy(Blotter $blotter): RedirectResponse
    {
        if (ServiceRequest::query()->where('blotter_id', $blotter->id)->exists()) {
            return redirect()->route('blotters.index')
                ->with('warning', 'This blotter is linked to a completed resident request and cannot be deleted here.');
        }

        $blotter->delete();

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter record deleted successfully!');
    }
}
