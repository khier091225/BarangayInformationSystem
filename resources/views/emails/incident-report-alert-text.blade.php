BARANGAY KAY-ANLOG
Barangay Information System

NEW INCIDENT REPORT: {!! $categoryLabel !!}
Reference: {!! $referenceNumber !!}
When: {!! $occurredAt !!} (Philippine time)
Where: {!! $location !!}

What happened:
{!! $description !!}

@if ($identityConfidential)
CONFIDENTIAL REPORT
The resident requested that their identity remain confidential. Share these details only with the duty team handling the report.

@endif
Open incident reports: {!! route('incident-reports.index') !!}

Sign in with your staff account to view reports. For follow-up, coordinate with the barangay admin using the reference number. Resident identity and attachments are not included in this email.

Your community, connected.
An automated message from Barangay Kay-Anlog.
