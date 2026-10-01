@props(['href'])
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 24px 0;">
    <tr>
        <td bgcolor="#276747" style="border-radius: 8px; mso-padding-alt: 14px 24px; text-align: center;">
            <a href="{{ $href }}" {{ $attributes->merge(['style' => 'display: inline-block; border: 1px solid #276747; border-radius: 8px; padding: 14px 24px; color: #ffffff; font-size: 15px; font-weight: 700; line-height: 1.4; text-decoration: none;']) }}>{{ $slot }}</a>
        </td>
    </tr>
</table>
