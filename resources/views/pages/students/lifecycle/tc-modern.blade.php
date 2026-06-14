<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transfer Certificate - {{ $student->full_name_en }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 13px; color: #1e293b; background: #f1f5f9; }
    .page { max-width: 800px; margin: 30px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.12); }

    .header { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); color: #fff; padding: 32px 40px; display: flex; align-items: center; gap: 20px; }
    .header-logo { width: 72px; height: 72px; object-fit: contain; background: rgba(255,255,255,.15); border-radius: 12px; padding: 6px; }
    .header-logo-placeholder { width: 72px; height: 72px; background: rgba(255,255,255,.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
    .header-info h1 { font-size: 20px; font-weight: 700; letter-spacing: .5px; }
    .header-info p { font-size: 11px; opacity: .8; margin-top: 3px; }

    .cert-badge { background: #fff; margin: 0 40px; margin-top: -1px; border-radius: 0 0 12px 12px; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .cert-badge h2 { font-size: 16px; font-weight: 700; color: #1e3a5f; text-transform: uppercase; letter-spacing: 2px; }
    .cert-badge .serial { font-size: 11px; color: #64748b; }

    .body { padding: 28px 40px; }
    .intro { background: #f8fafc; border-left: 4px solid #2563eb; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px; line-height: 1.8; font-size: 13px; color: #334155; }
    .intro strong { color: #1e3a5f; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
    .info-row { display: contents; }
    .info-row .label { background: #f8fafc; padding: 9px 14px; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
    .info-row .value { background: #fff; padding: 9px 14px; font-size: 13px; color: #1e293b; font-weight: 500; border-bottom: 1px solid #e2e8f0; }

    .conduct-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; font-size: 13px; color: #166534; }

    .closing { font-size: 13px; color: #475569; line-height: 1.8; margin-bottom: 32px; }

    .signatures { display: flex; justify-content: space-between; padding-top: 20px; border-top: 1px solid #e2e8f0; }
    .sig-block { text-align: center; }
    .sig-line { width: 140px; border-top: 2px solid #cbd5e1; margin: 0 auto 6px; }
    .sig-label { font-size: 11px; color: #64748b; font-weight: 600; }

    .footer { background: #f8fafc; padding: 12px 40px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .footer p { font-size: 10px; color: #94a3b8; }

    .print-bar { max-width: 800px; margin: 0 auto 12px; display: flex; gap: 10px; justify-content: flex-end; }
    @media print { .print-bar { display: none; } body { background: #fff; } .page { margin: 0; border-radius: 0; box-shadow: none; } }
</style>
</head>
<body>
@php($currentStyle = $style ?? 'modern')

@unless($isPdf ?? false)
<div class="print-bar">
    <a href="{{ route('students.tc', [$student, 'style' => 'classic']) }}"
        style="background:#78716c;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⇄ Switch to Classic
    </a>
    <a href="{{ route('students.tc.pdf', ['student' => $student, 'style' => $currentStyle]) }}"
        style="background:#dc2626;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⬇ Download PDF
    </a>
    <button onclick="window.print()"
        style="background:#059669;color:#fff;padding:6px 14px;border-radius:6px;border:none;cursor:pointer;font-family:sans-serif;font-size:12px;">
        🖨 Print
    </button>
</div>
@endunless

<div class="page">
    <div class="header">
        @if($setting->logo && file_exists(public_path($setting->logo)))
        <img src="{{ asset($setting->logo) }}" class="header-logo" alt="Logo">
        @else
        <div class="header-logo-placeholder">🏫</div>
        @endif
        <div class="header-info">
            <h1>{{ $setting->name ?? config('app.name') }}</h1>
            <p>{{ $setting->address ?? '' }}</p>
            @if($setting->eiin)<p>EIIN: {{ $setting->eiin }}</p>@endif
        </div>
    </div>

    <div class="cert-badge">
        <h2>Transfer Certificate</h2>
        <span class="serial">No: TC-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp; {{ $issueDate }}</span>
    </div>

    <div class="body">
        <div class="narrative">
            {!! $certificateTextHtml ?? '' !!}
        </div>

        <div class="info-grid">
            @foreach([
                ['Student ID', $student->student_cid],
                ['Date of Birth', $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '—'],
                ['Birth Certificate No.', $student->birth_certificate_number ?? '—'],
                ['Religion', $student->religion_text],
                ['Blood Group', $student->blood_group_text],
                ['Last Class Studied', $academicInfo?->schoolClass?->name_en ?? '—'],
                ['Section', $academicInfo?->section?->name_en ?? '—'],
                ['Academic Session', $academicInfo?->academicSession?->name_en ?? '—'],
                ['Roll Number', $academicInfo?->roll ?? '—'],
                ['Date of Leaving', $academicInfo?->checkout_date ? $academicInfo->checkout_date->format('d F Y') : $issueDate],
                ['Reason for Leaving', $academicInfo?->academic_status ? ucfirst($academicInfo->academic_status) : 'Transfer'],
            ] as [$label, $value])
            <div class="info-row">
                <div class="label">{{ $label }}</div>
                <div class="value">{{ $value }}</div>
            </div>
            @endforeach
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Class Teacher</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Office Seal & Date</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Headmaster / Principal</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Issued on {{ $issueDate }}</p>
        <p>Valid only with official seal &bull; {{ $setting->name ?? config('app.name') }}</p>
    </div>
</div>

</body>
</html>
