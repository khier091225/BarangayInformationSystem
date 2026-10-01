@props(['title', 'eyebrow', 'preheader', 'logoUrl'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }} | Barangay Kay-Anlog</title>
    <style>
        @media only screen and (max-width: 620px) {
            .email-page { padding: 16px 12px !important; }
            .email-header, .email-content { padding: 28px 24px !important; }
            .email-title { font-size: 27px !important; }
            .email-detail-label { width: 90px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; width: 100%; background-color: #f8faf7; color: #1e3a29; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; mso-hide: all;">{{ $preheader }}</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f8faf7">
        <tr>
            <td class="email-page" align="center" style="padding: 40px 20px;">
                <!--[if mso]><table role="presentation" width="600" align="center"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; border: 1px solid #e1e7de; border-radius: 16px; overflow: hidden; background-color: #ffffff;">
                    <tr>
                        <td class="email-header" bgcolor="#123c35" style="padding: 28px 36px; border-radius: 15px 15px 0 0; border-bottom: 4px solid #276747;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="64" valign="middle">
                                        <img src="{{ $logoUrl }}" alt="Barangay Kay-Anlog seal" width="52" height="52" style="display: block; width: 52px; height: 52px; border-radius: 50%; background-color: #ffffff; border: 2px solid #bed7ca;">
                                    </td>
                                    <td valign="middle">
                                        <p style="margin: 0; color: #ffffff; font-size: 19px; font-weight: 700; line-height: 1.35;">Barangay Kay-Anlog</p>
                                        <p style="margin: 5px 0 0; color: #bed7ca; font-size: 12px; line-height: 1.5; letter-spacing: 0.5px;">Barangay Information System</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-content" style="padding: 36px; font-size: 15px; line-height: 1.7; overflow-wrap: anywhere; word-break: break-word;">
                            <p style="margin: 0 0 10px; color: #276747; font-size: 11px; font-weight: 700; letter-spacing: 1.8px; text-transform: uppercase;">{{ $eyebrow }}</p>
                            <h1 class="email-title" style="margin: 0 0 24px; color: #1e3a29; font-size: 30px; font-weight: 700; line-height: 1.2; letter-spacing: -0.6px;">{{ $title }}</h1>
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 22px 28px; border-top: 1px solid #e1e7de; background-color: #f3f7f1; border-radius: 0 0 15px 15px; text-align: center;">
                            <p style="margin: 0; color: #276747; font-size: 12px; font-weight: 700; line-height: 1.6;">Your community, connected.</p>
                            <p style="margin: 5px 0 0; color: #637263; font-size: 11px; line-height: 1.6;">An automated message from Barangay Kay-Anlog.</p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
                <p style="margin: 20px 0 0; color: #637263; font-size: 11px; line-height: 1.6;">Barangay Kay-Anlog &middot; Calamba City, Laguna</p>
            </td>
        </tr>
    </table>
</body>
</html>
