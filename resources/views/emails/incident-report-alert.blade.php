NEW BARANGAY REPORT: {{ $categoryLabel }}
Reference: {{ $referenceNumber }}
When: {{ $occurredAt }} (Philippine time)
Where: {!! $location !!}

What happened:
{!! $description !!}

@if ($identityConfidential)
The resident requested that their identity remain confidential. Share these details only with the duty team handling the report.

@endif
For follow-up, coordinate with the barangay admin using the reference number. Resident identity and attachments are not included in this email.
