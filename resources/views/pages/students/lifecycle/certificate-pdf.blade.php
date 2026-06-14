<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $certificate->name }} - {{ $student->full_name_en }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { margin: 18mm 16mm; }
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        line-height: 1.85;
        color: #444;
        background: #fff;
    }

    .sheet {
        min-height: 250mm;
        position: relative;
        padding: 8mm 6mm 20mm;
    }

    .title-wrap {
        text-align: center;
        margin-bottom: 34px;
    }

    .title-box {
        display: inline-block;
        border: 1px solid #8f8f8f;
        padding: 8px 22px;
    }

    .title-box h1 {
        font-size: 18px;
        letter-spacing: .8px;
        color: #5f5f5f;
        font-weight: 700;
        text-transform: uppercase;
    }

    .content {
        max-width: 100%;
        margin: 0 auto;
        font-size: 17px;
        line-height: 2.05;
        color: #4a4a4a;
        text-align: justify;
        text-justify: inter-word;
        padding-left: 8mm;
        padding-right: 8mm;
    }

    .content p {
        margin-bottom: 12px;
        text-align: justify;
        text-justify: inter-word;
    }

    .content strong,
    .content b,
    .content em {
        color: #2f2f2f;
        font-weight: 700;
        font-style: italic;
    }

    .spacer {
        height: 76mm;
    }

    .bottom {
        display: table;
        width: 100%;
        margin-top: 10mm;
    }

    .reason,
    .signature {
        display: table-cell;
        vertical-align: bottom;
        width: 50%;
    }

    .reason {
        font-size: 12px;
        color: #555;
    }

    .reason-title {
        font-weight: 700;
        margin-bottom: 6px;
    }

    .reason-value {
        font-weight: 700;
        color: #2f2f2f;
    }

    .signature {
        text-align: center;
        color: #444;
    }

    .sig-line {
        width: 180px;
        border-top: 1px solid #555;
        margin: 0 auto 8px;
    }

    .sig-title {
        font-size: 18px;
        font-weight: 700;
        line-height: 1.2;
    }

    .sig-name,
    .sig-school,
    .sig-phone {
        font-size: 12px;
        line-height: 1.45;
    }
</style>
</head>
<body>
    <div class="sheet">
        <div class="title-wrap">
            <div class="title-box">
                <h1>{{ $certificate->name }}</h1>
            </div>
        </div>

        <div class="content">
            {!! $certificateTextHtml ?? '' !!}
        </div>

        <div class="spacer"></div>

        <div class="bottom">
            <div class="reason">
                <div class="reason-title">Reason for Leaving School:</div>
                <div class="reason-value">{{ $leavingReason ?? 'No reason provided' }}</div>
            </div>

            <div class="signature">
                <div class="sig-line"></div>
                <div class="sig-title">{{ $setting->principal_designation ?: 'Principal' }}</div>
                <div class="sig-name">
                    @if(!empty($setting->principal_name))
                        ({{ $setting->principal_name }})
                    @endif
                </div>
                <div class="sig-school">{{ $setting->principal_school_name ?: $setting->name }}</div>
                @if(!empty($setting->principal_phone))
                    <div class="sig-phone">{{ $setting->principal_phone }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
