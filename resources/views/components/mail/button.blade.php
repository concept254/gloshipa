@props(['url'])
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td style="border-radius:10px;background:#ff7a2f;">
            <a href="{{ $url }}" style="display:inline-block;padding:13px 26px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#08141f;text-decoration:none;border-radius:10px;">{{ $slot }}</a>
        </td>
    </tr>
</table>
