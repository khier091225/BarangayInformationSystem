<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateIncidentReportRequest;
use App\Models\IncidentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffIncidentReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in([
                IncidentReport::STATUS_SUBMITTED,
                IncidentReport::STATUS_ASSIGNED,
                IncidentReport::STATUS_RESPONDING,
                IncidentReport::STATUS_RESOLVED,
                IncidentReport::STATUS_CLOSED,
            ])],
            'team' => ['nullable', Rule::in(array_keys(IncidentReport::TEAM_LABELS))],
            'search' => 'nullable|string|max:255',
        ]);
        $status = $filters['status'] ?? IncidentReport::STATUS_SUBMITTED;
        $team = $filters['team'] ?? null;
        $search = trim($filters['search'] ?? '');
        $query = IncidentReport::query()->with(['assignee', 'resident'])->where('status', $status);

        if ($team !== null) {
            $query->where('suggested_team', $team);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $reports = $query->oldest()->paginate(10)->withQueryString();
        $statusCounts = IncidentReport::query()->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        return view('incident-reports.staff.index', compact('reports', 'status', 'team', 'search', 'statusCounts'));
    }

    public function show(Request $request, IncidentReport $incidentReport): View
    {
        $incidentReport->load(['resident', 'assignee', 'updates.user']);
        $canViewIdentity = ! $incidentReport->keep_identity_confidential
            || $incidentReport->assigned_to === $request->user()->id;

        return view('incident-reports.staff.show', compact('incidentReport', 'canViewIdentity'));
    }

    public function update(UpdateIncidentReportRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $validated = $request->validated();
        $updated = DB::transaction(function () use ($request, $incidentReport, $validated): bool {
            $report = IncidentReport::query()->lockForUpdate()->findOrFail($incidentReport->id);

            if ($validated['action'] === 'accept') {
                if ($report->status !== IncidentReport::STATUS_SUBMITTED) {
                    return false;
                }

                $report->update([
                    'status' => IncidentReport::STATUS_ASSIGNED,
                    'assigned_team' => $validated['team'],
                    'assigned_to' => $request->user()->id,
                    'assigned_at' => now(),
                ]);
                $message = filled($validated['message'] ?? null)
                    ? $validated['message']
                    : 'Your report has been assigned to '.IncidentReport::TEAM_LABELS[$validated['team']].'.';
            } else {
                abort_unless($report->assigned_to === $request->user()->id, 403);
                $nextStatus = $report->nextStatus();

                if ($nextStatus === null) {
                    return false;
                }

                $timestamp = match ($nextStatus) {
                    IncidentReport::STATUS_RESPONDING => 'responding_at',
                    IncidentReport::STATUS_RESOLVED => 'resolved_at',
                    IncidentReport::STATUS_CLOSED => 'closed_at',
                };
                $report->update(['status' => $nextStatus, $timestamp => now()]);
                $message = filled($validated['message'] ?? null)
                    ? $validated['message']
                    : match ($nextStatus) {
                        IncidentReport::STATUS_RESPONDING => 'Barangay staff are responding to your report.',
                        IncidentReport::STATUS_RESOLVED => 'Barangay staff marked your report as resolved.',
                        IncidentReport::STATUS_CLOSED => 'Your report has been closed.',
                    };
            }

            $report->updates()->create([
                'user_id' => $request->user()->id,
                'status' => $report->status,
                'message' => $message,
            ]);

            return true;
        });

        return redirect()->route('incident-reports.show', $incidentReport)
            ->with($updated ? 'success' : 'warning', $updated
                ? 'Incident report updated. The resident can see the new status and message.'
                : 'This report has already moved to another status.');
    }
}
