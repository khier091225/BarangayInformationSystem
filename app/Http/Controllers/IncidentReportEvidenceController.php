<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentReportEvidenceController extends Controller
{
    public function __invoke(Request $request, IncidentReport $incidentReport): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'staff' || ($user->role === 'resident' && $user->resident_id === $incidentReport->resident_id), 404);
        abort_if($user->role === 'staff' && $incidentReport->keep_identity_confidential && $incidentReport->assigned_to !== $user->id, 404);
        abort_unless($incidentReport->evidence_path && Storage::disk('local')->exists($incidentReport->evidence_path), 404);

        return Storage::disk('local')->download(
            $incidentReport->evidence_path,
            $incidentReport->evidence_original_name ?? 'incident-evidence',
        );
    }
}
