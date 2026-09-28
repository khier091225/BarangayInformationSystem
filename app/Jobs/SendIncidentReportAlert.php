<?php

namespace App\Jobs;

use App\Http\PhilSms;
use App\Mail\IncidentReportAlert;
use App\Models\IncidentReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendIncidentReportAlert implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $incidentReportId) {}

    public function handle(PhilSms $sms): void
    {
        $report = IncidentReport::query()->find($this->incidentReportId);

        if ($report === null) {
            return;
        }

        $report->loadMissing('resident');
        $recipients = config("incident_reports.alerts.{$report->suggested_team}", []);
        $phone = $sms->normalizePhoneNumber($recipients['phone'] ?? null);
        $email = $recipients['email'] ?? null;
        $location = $report->location;
        $description = $report->description;

        foreach ([$report->resident?->full_name, $report->resident?->contact_number] as $identityDetail) {
            if (filled($identityDetail)) {
                $location = str_ireplace($identityDetail, '[resident]', $location);
                $description = str_ireplace($identityDetail, '[resident]', $description);
            }
        }

        $occurredAt = $report->occurred_at->timezone('Asia/Manila')->format('M j, Y g:i A');
        $message = "NEW BARANGAY REPORT {$report->reference_number}\n"
            .$report->categoryLabel()." | {$occurredAt}\n"
            .'Where: '.Str::limit(Str::squish($location), 70)."\n"
            .'Details: '.Str::limit(Str::squish($description), 120);
        $smsStatus = filled($recipients['phone'] ?? null) ? 'failed' : 'not_configured';
        $emailStatus = filled($email) ? 'failed' : 'not_configured';

        if ($phone !== null && $sms->isConfigured()) {
            $smsStatus = $sms->send($phone, $message);
        }

        if (filled($email)) {
            try {
                Mail::to($email)->send(new IncidentReportAlert(
                    $report->reference_number,
                    $report->categoryLabel(),
                    $location,
                    $occurredAt,
                    $description,
                    $report->keep_identity_confidential,
                ));
                $emailStatus = 'submitted';
            } catch (Throwable) {
                $emailStatus = 'failed';
            }
        }

        $report->forceFill([
            'alert_sms_status' => $smsStatus,
            'alert_email_status' => $emailStatus,
        ])->save();
    }
}
