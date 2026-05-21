<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f9;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="700" cellspacing="0" cellpadding="0" style="max-width:700px;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
<tr><td style="background:#1f2937;padding:14px 18px;">
<table role="presentation" width="100%"><tr>
<td style="width:56px;"><img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }}" style="max-width:50px;max-height:50px;background:#fff;padding:2px;border-radius:4px;"></td>
<td style="padding-left:10px;">
<div style="font-size:18px;color:#fff;font-weight:700;">{{ $schoolName }}</div>
@if(!empty($schoolContacts))<div style="font-size:12px;color:#cbd5e1;">{{ $schoolContacts }}</div>@endif
</td>
</tr></table>
</td></tr>
<tr><td style="padding:20px 18px;">
<p style="margin:0 0 10px;font-size:14px;">Dear Parent,</p>
<p style="margin:0 0 12px;font-size:14px;">Please find the result details for <strong>{{ $student->full_name_en }}</strong> (ID: {{ $student->student_cid ?? $student->id }}).</p>
<p style="margin:0 0 12px;font-size:14px;"><strong>Report:</strong> {{ $reportTitle }}</p>
@if(!empty($meta))
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;background:#f8fafc;margin-bottom:12px;">
@foreach($meta as $k => $v)
<tr><td style="padding:8px 10px;font-size:13px;border-bottom:1px solid #e5e7eb;"><strong>{{ $k }}:</strong> {{ $v }}</td></tr>
@endforeach
</table>
@endif
@if(!empty($rows))
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;">
<thead>
<tr>
@foreach(array_keys($rows[0]) as $head)
<th style="padding:8px;border:1px solid #e5e7eb;background:#f1f5f9;font-size:12px;text-align:left;">{{ $head }}</th>
@endforeach
</tr>
</thead>
<tbody>
@foreach($rows as $r)
<tr>
@foreach($r as $cell)
<td style="padding:8px;border:1px solid #e5e7eb;font-size:12px;">{{ $cell }}</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endif
</td></tr>
<tr><td style="padding:12px 18px;background:#f8fafc;border-top:1px solid #e5e7eb;font-size:12px;color:#475569;">
@if(!empty($schoolSupportEmail))<div><strong>Email:</strong> {{ $schoolSupportEmail }}</div>@endif
@if(!empty($schoolWhatsapp))<div><strong>WhatsApp:</strong> {{ $schoolWhatsapp }}</div>@endif
@if(!empty($schoolWebsite))<div><strong>Website:</strong> {{ $schoolWebsite }}</div>@endif
</td></tr>
</table>
</td></tr></table>
</body>
</html>
