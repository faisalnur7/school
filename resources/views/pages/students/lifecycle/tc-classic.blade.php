<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transfer Certificate - {{ $student->full_name_en }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 13px; color: #1a1a1a; background: #f5f0e8; }
    .page { max-width: 780px; margin: 30px auto; background: #fff; border: 3px double #8B6914; padding: 40px 50px; position: relative; }
    .page::before { content: ''; position: absolute; inset: 8px; border: 1px solid #c9a84c; pointer-events: none; }

    .school-header { text-align: center; border-bottom: 2px solid #8B6914; padding-bottom: 16px; margin-bottom: 20px; }
    .school-logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 6px; }
    .school-name { font-size: 22px; font-weight: bold; color: #5a3e00; letter-spacing: 1px; text-transform: uppercase; }
    .school-address { font-size: 11px; color: #666; margin-top: 3px; }
    .school-eiin { font-size: 11px; color: #666; }

    .cert-title { text-align: center; margin: 18px 0 20px; }
    .cert-title h2 { font-size: 20px; font-weight: bold; color: #5a3e00; text-transform: uppercase; letter-spacing: 3px; border-bottom: 1px solid #c9a84c; display: inline-block; padding-bottom: 4px; }
    .cert-title .serial { font-size: 11px; color: #888; margin-top: 6px; }

    .cert-body { line-height: 2.2; font-size: 13px; }
    .cert-body .field { display: inline-block; border-bottom: 1px solid #333; min-width: 200px; font-weight: bold; padding: 0 4px; }
    .cert-body .field-sm { min-width: 80px; }
    .cert-body .field-lg { min-width: 280px; }

    .cert-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 12px; }
    .cert-table td { padding: 5px 8px; border: 1px solid #c9a84c; }
    .cert-table td:first-child { width: 45%; color: #555; font-style: italic; }
    .cert-table td:last-child { font-weight: bold; }

    .conduct-row { margin: 14px 0; font-size: 13px; }

    .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
    .sig-block { text-align: center; }
    .sig-line { border-top: 1px solid #333; width: 160px; margin: 0 auto 4px; }
    .sig-label { font-size: 11px; color: #555; }

    .footer-note { margin-top: 20px; font-size: 10px; color: #888; text-align: center; border-top: 1px solid #e0d5c0; padding-top: 10px; }

    .print-bar { max-width: 780px; margin: 0 auto 10px; display: flex; gap: 10px; justify-content: flex-end; }
    @media print { .print-bar { display: none; } body { background: #fff; } .page { margin: 0; border: none; } .page::before { display: none; } }
</style>
</head>
<body>
@php($currentStyle = $style ?? 'classic')

@unless($isPdf ?? false)
<div class="print-bar">
    <a href="{{ route('students.tc', [$student, 'style' => 'modern']) }}"
        style="background:#4f46e5;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⇄ Switch to Modern
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
    {{-- Header --}}
    <div class="school-header">
        @if($setting->logo && file_exists(public_path($setting->logo)))
        <img src="{{ asset($setting->logo) }}" class="school-logo" alt="Logo">
        @endif
        <div class="school-name">{{ $setting->name ?? config('app.name') }}</div>
        <div class="school-address">{{ $setting->address ?? '' }}</div>
        @if($setting->eiin)
        <div class="school-eiin">EIIN: {{ $setting->eiin }}</div>
        @endif
    </div>

    {{-- Title --}}
    <div class="cert-title">
        <h2>Transfer Certificate</h2>
        <div class="serial">Serial No: TC-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp; Date: {{ $issueDate }}</div>
    </div>

    <div class="cert-body" style="margin-top:10px; margin-bottom:10px;">
        {!! $certificateTextHtml ?? '' !!}
    </div>

    <table class="cert-table">
        <tr><td>Student ID (CID)</td><td>{{ $student->student_cid }}</td></tr>
        <tr><td>Date of Birth</td><td>{{ $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '—' }}</td></tr>
        <tr><td>Birth Certificate No.</td><td>{{ $student->birth_certificate_number ?? '—' }}</td></tr>
        <tr><td>Religion</td><td>{{ $student->religion_text }}</td></tr>
        <tr><td>Blood Group</td><td>{{ $student->blood_group_text }}</td></tr>
        <tr><td>Last Class Studied</td><td>{{ $academicInfo?->schoolClass?->name_en ?? '—' }}</td></tr>
        <tr><td>Section</td><td>{{ $academicInfo?->section?->name_en ?? '—' }}</td></tr>
        <tr><td>Academic Session</td><td>{{ $academicInfo?->academicSession?->name_en ?? '—' }}</td></tr>
        <tr><td>Roll Number</td><td>{{ $academicInfo?->roll ?? '—' }}</td></tr>
        <tr><td>Date of Leaving</td><td>{{ $academicInfo?->checkout_date ? $academicInfo->checkout_date->format('d F Y') : $issueDate }}</td></tr>
        <tr><td>Reason for Leaving</td><td>{{ $academicInfo?->academic_status ? ucfirst($academicInfo->academic_status) : 'Transfer' }}</td></tr>
    </table>

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

    <div class="footer-note">
        This certificate is issued on {{ $issueDate }} and is valid only with the official seal of the institution.
    </div>
</div>

</body>
</html>
