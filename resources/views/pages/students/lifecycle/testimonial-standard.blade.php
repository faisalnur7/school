<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Testimonial - {{ $student->full_name_en }}</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #f8fafc; }
    .print-bar { max-width: 820px; margin: 12px auto 8px; display: flex; gap: 10px; justify-content: flex-end; }
    .page { max-width: 820px; margin: 0 auto 24px; background: #fff; border: 1px solid #d1d5db; padding: 32px; }
    .header { text-align: center; border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 20px; }
    .title { text-align: center; margin: 14px 0 18px; font-size: 22px; letter-spacing: 1px; font-weight: 700; text-transform: uppercase; }
    .meta { text-align: center; font-size: 12px; margin-bottom: 20px; color: #4b5563; }
    .body p { line-height: 1.8; margin-bottom: 12px; text-align: justify; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
    td:first-child { width: 36%; background: #f9fafb; font-weight: 600; }
    .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
    .sig { width: 180px; text-align: center; }
    .line { border-top: 1px solid #222; margin-bottom: 6px; }
    @media print { .print-bar { display: none; } body { background: #fff; } .page { border: 0; margin: 0; padding: 20px; } }
</style>
</head>
<body>
<div class="print-bar">
    <a href="{{ route('students.testimonial', [$student, 'style' => 'classic']) }}" style="background:#4f46e5;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;">Classic</a>
    <a href="{{ route('students.testimonial', [$student, 'style' => 'modern']) }}" style="background:#0ea5e9;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;">Modern</a>
    <a href="{{ route('students.testimonial.pdf', $student) }}" style="background:#dc2626;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;">PDF</a>
    <button onclick="window.print()" style="background:#059669;color:#fff;padding:6px 12px;border-radius:6px;border:none;cursor:pointer;">Print</button>
</div>

<div class="page">
    <div class="header">
        <div style="font-size:24px;font-weight:700;">{{ $setting->name ?? config('app.name') }}</div>
        <div>{{ $setting->address ?? '' }}</div>
    </div>

    <div class="title">Testimonial</div>
    <div class="meta">Reference No: TM-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }} | Issue Date: {{ $issueDate }}</div>

    <div class="body">
        <p>
            This is to certify that <strong>{{ $student->full_name_en }}</strong>
            @if($student->full_name_bn) ({{ $student->full_name_bn }}) @endif,
            son/daughter of <strong>{{ $student->father_name ?? 'N/A' }}</strong> and
            <strong>{{ $student->mother_name ?? 'N/A' }}</strong>, was a student of this institution.
        </p>
    </div>

    <table>
        <tr><td>Student CID</td><td>{{ $student->student_cid ?? '—' }}</td></tr>
        <tr><td>Date of Birth</td><td>{{ $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '—' }}</td></tr>
        <tr><td>Academic Session</td><td>{{ $academicInfo?->academicSession?->name_en ?? '—' }}</td></tr>
        <tr><td>Last Class</td><td>{{ $academicInfo?->schoolClass?->name_en ?? '—' }}</td></tr>
        <tr><td>Section</td><td>{{ $academicInfo?->section?->name_en ?? '—' }}</td></tr>
        <tr><td>Group</td><td>{{ $academicInfo?->group?->name_en ?? '—' }}</td></tr>
        <tr><td>Roll</td><td>{{ $academicInfo?->roll ?? '—' }}</td></tr>
        <tr><td>Checkout Type</td><td>{{ $academicInfo?->academic_status ? ucfirst($academicInfo->academic_status) : '—' }}</td></tr>
        <tr><td>Checkout Date</td><td>{{ $academicInfo?->checkout_date ? $academicInfo->checkout_date->format('d F Y') : '—' }}</td></tr>
    </table>

    <div class="body" style="margin-top: 12px;">
        <p>During the tenure in this institution, the student showed satisfactory conduct and character.</p>
        <p>This testimonial is issued on request for official use.</p>
    </div>

    <div class="signatures">
        <div class="sig"><div class="line"></div>Class Teacher</div>
        <div class="sig"><div class="line"></div>Office Seal</div>
        <div class="sig"><div class="line"></div>Principal</div>
    </div>
</div>
</body>
</html>
