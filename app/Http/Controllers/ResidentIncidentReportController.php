<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentReportRequest;
use App\Jobs\SendIncidentReportAlert;
use App\Models\IncidentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ResidentIncidentReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => 'nullable|in:Submitted,Assigned,Responding,Resolved,Closed']);
        $status = $filters['status'] ?? null;
        $query = IncidentReport::query()->where('resident_id', $request->user()->resident_id);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $reports = $query->latest('updated_at')->latest('id')->paginate(10)->withQueryString();

        return view('incident-reports.resident.index', compact('reports', 'status'));
    }

    public function create(): View
    {
        return view('incident-reports.resident.create', ['categories' => IncidentReport::CATEGORY_TEAMS]);
    }

    public function store(StoreIncidentReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $evidence = $request->file('evidence');
        $evidencePath = $evidence?->store('incident-reports', 'local');

        if ($evidence !== null && $evidencePath === false) {
            abort(500, 'The evidence file could not be saved. Please try again.');
        }

        try {
            $report = DB::transaction(function () use ($request, $validated, $evidence, $evidencePath): IncidentReport {
                $report = IncidentReport::create([
                    'resident_id' => $request->user()->resident_id,
                    'category' => $validated['category'],
                    'description' => $validated['description'],
                    'location' => $validated['location'],
                    'occurred_at' => filled($validated['occurred_at'] ?? null)
                        ? Carbon::createFromFormat('Y-m-d\TH:i', $validated['occurred_at'], 'Asia/Manila')->utc()
                        : now(),
                    'keep_identity_confidential' => $validated['keep_identity_confidential'] ?? false,
                    'evidence_path' => $evidencePath,
                    'evidence_original_name' => $evidence?->getClientOriginalName(),
                    'evidence_mime' => $evidence?->getMimeType(),
                    'status' => IncidentReport::STATUS_SUBMITTED,
                    'suggested_team' => IncidentReport::CATEGORY_TEAMS[$validated['category']][1],
                ]);
                $report->updates()->create([
                    'status' => IncidentReport::STATUS_SUBMITTED,
                    'message' => 'Report received for staff review.',
                ]);

                return $report;
            });
        } catch (Throwable $exception) {
            if ($evidencePath) {
                Storage::disk('local')->delete($evidencePath);
            }

            throw $exception;
        }

        if (filled(config("incident_reports.alerts.{$report->suggested_team}.phone"))
            || filled(config("incident_reports.alerts.{$report->suggested_team}.email"))) {
            SendIncidentReportAlert::dispatch($report->id)->onConnection('deferred')->afterCommit();
        }

        return redirect()->route('account.incidents.show', $report)
            ->with('success', "Your report was received. Reference: {$report->reference_number}.");
    }

    public function show(Request $request, IncidentReport $incidentReport): View
    {
        abort_unless($incidentReport->resident_id === $request->user()->resident_id, 404);
        $incidentReport->load(['updates.user', 'assignee']);

        return view('incident-reports.resident.show', compact('incidentReport'));
    }
}
