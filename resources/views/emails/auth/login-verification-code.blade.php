<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#0f172a;">
    <div style="max-width:560px; margin:0 auto; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px;">
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-bottom:14px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:72px; vertical-align:middle;">
                        <div style="width:58px; height:58px; border:1px solid #cbd5e1; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; overflow:hidden;">
                            <img src="{{ $schoolLogoUrl }}" alt="School Logo" style="max-width:100%; max-height:100%; object-fit:contain;">
                        </div>
                    </td>
                    <td style="vertical-align:middle;">
                        <div style="font-size:20px; font-weight:700; color:#0f172a; line-height:1.2;">{{ $schoolName }}</div>
                        <div style="font-size:13px; color:#475569; line-height:1.35;">{{ $schoolAddress }}</div>
                        @if(!empty($schoolContacts))
                            <div style="font-size:13px; color:#334155; margin-top:2px;">{{ $schoolContacts }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <h2 style="margin:0 0 10px; font-size:20px;">Login Verification</h2>
        <p style="margin:0 0 12px; color:#334155;">Hi {{ $user->name }},</p>
        <p style="margin:0 0 16px; color:#334155;">Use this 6-digit code to complete your login:</p>

        <div style="font-size:32px; letter-spacing:8px; font-weight:700; color:#1d4ed8; margin:8px 0 16px;">{{ $code }}</div>

        <p style="margin:0 0 8px; color:#475569;">This code will expire in 10 minutes.</p>
        <p style="margin:0; color:#475569;">If you did not try to sign in, please ignore this email.</p>

        <hr style="border:none; border-top:1px solid #e2e8f0; margin:18px 0 12px;">
        <p style="margin:0 0 6px; color:#475569; font-size:13px;"><strong>Address:</strong> {{ $schoolAddress }}</p>
        <p style="margin:0 0 6px; color:#475569; font-size:13px;"><strong>Email:</strong> {{ $schoolSupportEmail }}</p>
        <p style="margin:0; color:#334155; font-size:13px;">Regards,<br>{{ $schoolSignature }}</p>
    </div>
</body>
</html>
