<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Testimonial - {{ $student->full_name_en }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { size: A4 portrait; margin: 12mm; }

    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 17px;
        color: #000;
        background: #f5f0e8;
    }

    .page {
        width: 210mm;
        min-height: 297mm;
        margin: 24px auto;
        background: #fff;
        border: 3px double #8B6914;
        padding: 18mm 16mm 30mm;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .page::before { content: ''; position: absolute; inset: 8px; border: 1px solid #c9a84c; pointer-events: none; }

    .school-header { text-align: center; border-bottom: 2px solid #8B6914; padding-bottom: 16px; margin-bottom: 20px; }
    .school-logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 6px; }
    .school-name { font-size: 22px; font-weight: bold; color: #000; letter-spacing: 1px; text-transform: uppercase; }
    .school-address { font-size: 15px; color: #000; margin-top: 3px; }

    .cert-title { text-align: center; margin: 18px 0 20px; }
    .cert-title h2 { font-size: 20px; font-weight: bold; color: #000; text-transform: uppercase; letter-spacing: 3px; border-bottom: 1px solid #000; display: inline-block; padding-bottom: 4px; }
    .cert-title .serial { font-size: 15px; color: #000; margin-top: 6px; }

    .cert-body { line-height: 2.1; font-size: 17px; text-align: justify; }
    .cert-body p { margin-bottom: 14px; }
    .cert-body .field { display: inline-block; border-bottom: 1px solid #000; min-width: 180px; font-weight: bold; padding: 0 4px; }

    .certificate-content {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }

    .signatures {
        display: flex;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 14mm;
    }
    .sig-block { text-align: center; }
    .sig-line { border-top: 1px solid #000; width: 160px; margin: 0 auto 4px; }
    .sig-label { font-size: 13px; color: #000; }

    .footer-note { margin-top: 12px; font-size: 12px; color: #000; text-align: center; border-top: 1px solid #000; padding-top: 10px; }

    .print-bar { width: 210mm; max-width: calc(100vw - 32px); margin: 0 auto 10px; display: flex; gap: 10px; justify-content: flex-end; }
    .print-bar a,
    .print-bar button {
        color: #000 !important;
        background: #fff !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    @media print {
        .print-bar { display: none; }
        body { background: #fff; }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            border: none;
            padding: 18mm 16mm 30mm;
        }
        .page::before { display: none; }
        body { font-size: 17px; color: #000; }
    }
</style>
</head>
<body>
@php($currentStyle = $style ?? 'classic')

@unless($isPdf ?? false)
<div class="print-bar">
    <a href="{{ route('students.testimonial', [$student, 'style' => 'modern']) }}"
        style="background:#4f46e5;color:#fff;padding:6px 14px;border-radius:6px;text-decoration:none;font-family:sans-serif;font-size:12px;">
        ⇄ Switch to Modern
    </a>
    <a href="{{ route('students.testimonial.pdf', ['student' => $student, 'style' => $currentStyle]) }}"
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
    <div class="school-header">
        @if($setting->logo && file_exists(public_path($setting->logo)))
        <img src="{{ asset($setting->logo) }}" class="school-logo" alt="Logo">
        @endif
        <div class="school-name">{{ $setting->name ?? config('app.name') }}</div>
        <div class="school-address">{{ $setting->address ?? '' }}</div>
    </div>

    <div class="cert-title">
        <h2>Testimonial</h2>
        <div class="serial">Ref No: TM-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp; Date: {{ $issueDate }}</div>
    </div>

    <div class="certificate-content">
        <div class="cert-body">
            {!! $certificateTextHtml ?? '' !!}
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

        <div class="footer-note">
            Issued on {{ $issueDate }} &bull; {{ $setting->name ?? config('app.name') }} &bull; This testimonial is issued on request.
        </div>
    </div>
</div>

</body>
</html>
