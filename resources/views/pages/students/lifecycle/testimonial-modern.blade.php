<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Testimonial - {{ $student->full_name_en }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 13px; color: #1e293b; background: #f1f5f9; }
    .page { max-width: 800px; margin: 30px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.12); }

    .header { background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%); color: #fff; padding: 32px 40px; display: flex; align-items: center; gap: 20px; }
    .header-logo { width: 72px; height: 72px; object-fit: contain; background: rgba(255,255,255,.15); border-radius: 12px; padding: 6px; }
    .header-logo-placeholder { width: 72px; height: 72px; background: rgba(255,255,255,.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
    .header-info h1 { font-size: 20px; font-weight: 700; letter-spacing: .5px; }
    .header-info p { font-size: 11px; opacity: .8; margin-top: 3px; }

    .cert-badge { background: #fff; margin: 0 40px; border-radius: 0 0 12px 12px; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .cert-badge h2 { font-size: 16px; font-weight: 700; color: #4c1d95; text-transform: uppercase; letter-spacing: 2px; }
    .cert-badge .serial { font-size: 11px; color: #64748b; }

    .body { padding: 28px 40px; }

    .student-card { display: flex; align-items: center; gap: 16px; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; }
    .student-avatar { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #7c3aed, #4c1d95); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px; font-weight: 700; flex-shrink: 0; }
    .student-name { font-size: 17px; font-weight: 700; color: #4c1d95; }
    .student-sub { font-size: 12px; color: #7c3aed; margin-top: 2px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
    .info-row { display: contents; }
    .info-row .label { background: #f8fafc; padding: 9px 14px; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
    .info-row .value { background: #fff; padding: 9px 14px; font-size: 13px; color: #1e293b; font-weight: 500; border-bottom: 1px solid #e2e8f0; }

    .narrative { background: #f8fafc; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px; line-height: 1.9; color: #334155; font-size: 13px; }
    .narrative p { margin-bottom: 10px; }
    .narrative p:last-child { margin-bottom: 0; }

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

<div class="print-bar">
    <a href="{{ route('students.testimonial', [$student, 'style' => 'classic']) }}"
        style="background:#78716c;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⇄ Switch to Classic
    </a>
    <a href="{{ route('students.testimonial.pdf', $student) }}"
        style="background:#dc2626;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⬇ Download PDF
    </a>
    <button onclick="window.print()"
        style="background:#059669;color:#fff;padding:6px 14px;border-radius:6px;border:none;cursor:pointer;font-family:sans-serif;font-size:12px;">
        🖨 Print
    </button>
</div>

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
        <h2>Testimonial</h2>
        <span class="serial">Ref: TM-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp; {{ $issueDate }}</span>
    </div>

    <div class="body">
        <div class="student-card">
            <div class="student-avatar">{{ strtoupper(substr($student->full_name_en, 0, 2)) }}</div>
            <div>
                <div class="student-name">{{ $student->full_name_en }}</div>
                @if($student->full_name_bn)<div class="student-sub">{{ $student->full_name_bn }}</div>@endif
                <div class="student-sub">CID: {{ $student->student_cid }}</div>
            </div>
        </div>

        <div class="info-grid">
            @foreach([
                ['Date of Birth', $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '—'],
                ['Father\'s Name', $student->father_name ?? '—'],
                ['Mother\'s Name', $student->mother_name ?? '—'],
                ['Religion', $student->religion_text],
                ['Last Class Attended', $academicInfo?->schoolClass?->name_en ?? '—'],
                ['Academic Session', $academicInfo?->academicSession?->name_en ?? '—'],
                ['Roll Number', $academicInfo?->roll ?? '—'],
                ['Section', $academicInfo?->section?->name_en ?? '—'],
            ] as [$label, $value])
            <div class="info-row">
                <div class="label">{{ $label }}</div>
                <div class="value">{{ $value }}</div>
            </div>
            @endforeach
        </div>

        <div class="narrative">
            <p>
                This is to certify that <strong>{{ $student->full_name_en }}</strong>, son/daughter of
                <strong>{{ $student->father_name ?? '—' }}</strong>, was a bonafide student of
                <strong>{{ $setting->name ?? config('app.name') }}</strong>.
            </p>
            <p>
                During his/her period of study, his/her conduct and behaviour were found to be
                <strong>satisfactory</strong>. He/She was regular in attendance and participated actively
                in academic and co-curricular activities.
            </p>
            <p>
                We wish him/her every success in life and recommend him/her for any purpose for which
                this testimonial may be required.
            </p>
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
        <p>Issued on request &bull; {{ $setting->name ?? config('app.name') }}</p>
    </div>
</div>

</body>
</html>
