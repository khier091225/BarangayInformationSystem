@php($logoUrl = $message->embed(public_path('images/Logo_kay-anlog.jpg')))
<x-email.layout
    title="Reset your password"
    eyebrow="Account security"
    preheader="Set a new password for your Barangay Kay-Anlog account."
    :logo-url="$logoUrl"
>
    <p style="margin: 0 0 16px; font-weight: 700;">Hello {{ $name }},</p>
    <p style="margin: 0 0 20px; color: #556658;">We received a request to reset the password for your Barangay Information System account. Use the button below to choose a new password.</p>

    <x-email.button :href="$resetUrl">Reset password</x-email.button>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0 0 24px;">
        <tr>
            <td style="padding: 16px 18px; background-color: #eaf5eb; border-left: 3px solid #276747; border-radius: 0 8px 8px 0; color: #1e3a29; font-size: 13px; line-height: 1.6;">
                <strong>This link expires in {{ $expiresIn }} minutes.</strong><br>
                For your security, keep this email private. The link can only be used once.
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 24px; color: #556658; font-size: 13px;">If you did not request this, you can safely ignore this email. Your password will stay the same.</p>
    <p style="margin: 0; padding-top: 20px; border-top: 1px solid #e1e7de; color: #637263; font-size: 12px;">Having trouble with the button? Copy and paste this link into your browser:</p>
    <p style="margin: 8px 0 0; font-size: 12px; line-height: 1.7; word-break: break-all; overflow-wrap: anywhere;"><a href="{{ $resetUrl }}" style="color: #276747; text-decoration: underline; word-break: break-all;">{{ $resetUrl }}</a></p>
</x-email.layout>
