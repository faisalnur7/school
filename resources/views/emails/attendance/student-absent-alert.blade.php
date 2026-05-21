<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Absent Alert</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f9;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;width:100%;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;padding:16px 20px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width:60px;vertical-align:middle;">
                                        <img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }}" style="max-width:52px;max-height:52px;border-radius:4px;display:block;background:#fff;padding:2px;">
                                    </td>
                                    <td style="vertical-align:middle;padding-left:12px;">
                                        <h2 style="margin:0;color:#ffffff;font-size:20px;">{{ $schoolName }}</h2>
                                        <p style="margin:4px 0 0;color:#d1fae5;font-size:12px;">Attendance Notification</p>
                                        @if(!empty($schoolContacts))
                                            <p style="margin:3px 0 0;color:#ccfbf1;font-size:12px;">{{ $schoolContacts }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 20px;">
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.6;">Dear Parent,</p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.6;">
                                This is to inform you that <strong>{{ $studentName }}</strong> was marked <strong>absent</strong>.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">
                                <tr>
                                    <td style="padding:10px 12px;font-size:14px;border-bottom:1px solid #e5e7eb;"><strong>Date:</strong> {{ $attendanceDate }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;font-size:14px;border-bottom:1px solid #e5e7eb;"><strong>Class:</strong> {{ $className }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;font-size:14px;"><strong>Section:</strong> {{ $sectionName }}</td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0;font-size:14px;line-height:1.6;">
                                Please contact the school office if you need further details.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 20px;background:#f8fafc;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 6px;color:#475569;font-size:12px;font-weight:700;">Contact Details</p>
                            @if(!empty($schoolPhone1) || !empty($schoolPhone2))
                                <p style="margin:0 0 4px;color:#475569;font-size:12px;">
                                    <strong>Phone:</strong> {{ implode(' | ', array_filter([$schoolPhone1, $schoolPhone2])) }}
                                </p>
                            @endif
                            @if(!empty($schoolSupportEmail))
                                <p style="margin:0 0 4px;color:#475569;font-size:12px;">
                                    <strong>Email:</strong> {{ $schoolSupportEmail }}
                                </p>
                            @endif
                            @if(!empty($schoolWhatsapp))
                                <p style="margin:0 0 4px;color:#475569;font-size:12px;">
                                    <strong>WhatsApp:</strong> {{ $schoolWhatsapp }}
                                </p>
                            @endif
                            @if(!empty($schoolWebsite))
                                <p style="margin:0;color:#475569;font-size:12px;">
                                    <strong>Website:</strong> {{ $schoolWebsite }}
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
