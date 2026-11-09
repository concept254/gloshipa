<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', config('gloshipa.name'))</title>
</head>
<body style="margin:0;padding:0;background:#f5efe3;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">@yield('preheader')</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5efe3;padding:28px 12px;">
    <tr><td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
            <tr>
                <td style="background:#0e2033;border-radius:16px 16px 0 0;padding:22px 32px;font-family:Arial,Helvetica,sans-serif;">
                    <span style="font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.3px;">Glo<span style="color:#ff7a2f;"> Shipa</span></span>
                </td>
            </tr>
            <tr><td style="height:5px;background:#ff7a2f;line-height:5px;font-size:0;">&nbsp;</td></tr>
            <tr>
                <td style="background:#ffffff;padding:34px 32px 30px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#223a52;">
                    @yield('content')
                </td>
            </tr>
            <tr>
                <td style="background:#fbf8f2;border-radius:0 0 16px 16px;padding:20px 32px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#627d97;">
                    {{ config('gloshipa.name') }} &middot; {{ config('gloshipa.contact.address') }}<br>
                    <a href="mailto:{{ config('gloshipa.contact.email') }}" style="color:#0e8579;">{{ config('gloshipa.contact.email') }}</a>
                    &middot; {{ config('gloshipa.contact.phone') }}
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
