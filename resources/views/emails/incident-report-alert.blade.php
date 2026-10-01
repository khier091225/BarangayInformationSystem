@php($logoUrl = $message->embed(public_path('images/Logo_kay-anlog.jpg')))
<x-email.layout
    title="New incident report"
    eyebrow="Community response"
    :preheader="'Report '.$referenceNumber.' is ready for your team to review.'"
    :logo-url="$logoUrl"
>
    <p style="margin: 0 0 20px; color: #556658;">A resident has submitted an incident report for your team's attention. Review the details below and coordinate the next steps with the barangay admin.</p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" aria-label="Incident details" style="margin: 0 0 28px; background-color: #eaf5eb; border: 1px solid #c2e2c7; border-radius: 8px; font-size: 13px; line-height: 1.6; table-layout: fixed;">
        <tr>
            <th class="email-detail-label" scope="row" width="104" align="left" valign="top" style="padding: 16px 12px 8px 16px; color: #556658; font-weight: 400;">Reference</th>
            <td style="padding: 16px 16px 8px 0; color: #276747; font-weight: 700;">{{ $referenceNumber }}</td>
        </tr>
        <tr>
            <th class="email-detail-label" scope="row" align="left" valign="top" style="padding: 0 12px 8px 16px; color: #556658; font-weight: 400;">Category</th>
            <td style="padding: 0 16px 8px 0;">{{ $categoryLabel }}</td>
        </tr>
        <tr>
            <th class="email-detail-label" scope="row" align="left" valign="top" style="padding: 0 12px 8px 16px; color: #556658; font-weight: 400;">When</th>
            <td style="padding: 0 16px 8px 0;">{{ $occurredAt }}<br><span style="color: #637263; font-size: 11px;">Philippine time</span></td>
        </tr>
        <tr>
            <th class="email-detail-label" scope="row" align="left" valign="top" style="padding: 0 12px 16px 16px; color: #556658; font-weight: 400;">Location</th>
            <td style="padding: 0 16px 16px 0;">{{ $location }}</td>
        </tr>
    </table>

    <h2 style="margin: 0 0 10px; color: #1e3a29; font-size: 16px; line-height: 1.4;">What happened:</h2>
    <p style="margin: 0 0 24px; color: #556658;">{!! nl2br(e($description)) !!}</p>

    @if ($identityConfidential)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 0 24px;">
            <tr>
                <td style="padding: 16px 18px; background-color: #fff2d5; border-left: 3px solid #74500b; border-radius: 0 8px 8px 0; color: #74500b; font-size: 13px; line-height: 1.6;">
                    <strong>Confidential report</strong><br>
                    The resident requested that their identity remain confidential. Share these details only with the duty team handling the report.
                </td>
            </tr>
        </table>
    @endif

    <x-email.button :href="route('incident-reports.index')">Open incident reports</x-email.button>

    <p style="margin: 0; padding-top: 20px; border-top: 1px solid #e1e7de; color: #637263; font-size: 12px;">Sign in with your staff account to view reports. For follow-up, use reference <strong>{{ $referenceNumber }}</strong>. Resident identity and attachments are not included in this email.</p>
</x-email.layout>
