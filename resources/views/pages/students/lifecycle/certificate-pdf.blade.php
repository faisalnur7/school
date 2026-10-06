<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $certificate->name }} - {{ $student->full_name_en }}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap');
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { size: {{ data_get($certificate->layoutSettings(), 'page.width', 210) }}mm {{ data_get($certificate->layoutSettings(), 'page.height', 297) }}mm; margin: 0; }
    body {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: 13px;
        line-height: 1.85;
        color: {{ data_get($certificate->layoutSettings(), 'typography.font_color', '#111827') }};
        background: #fff;
    }

    .certificate-header { position: relative; z-index: 2; margin-bottom: 24px; color: #111827; font-family: Arial, Helvetica, sans-serif; line-height: 1.15; }
    .certificate-header-slogan { min-height: 18px; margin-bottom: 5px; text-align: right; font-family: 'Patrick Hand', cursive; font-size: 18px; font-weight: 400; letter-spacing: .04em; }
    .certificate-header-main { display: table; width: 100%; padding: 5px 0 8px; border-bottom: 1px solid #111827; }
    .certificate-header-logo { display: table-cell; width: 48px; height: 48px; object-fit: contain; vertical-align: bottom; }
    .certificate-header-names { display: table-cell; padding: 0 10px; vertical-align: bottom; }
    .certificate-header-bangla { font-family: 'Noto Sans Bengali', 'SolaimanLipi', sans-serif; font-size: 24px; font-weight: 800; line-height: 1.05; white-space: nowrap; }
    .certificate-header-english { margin-top: 3px; font-size: 14px; font-weight: 800; letter-spacing: .04em; white-space: nowrap; }
    .certificate-header-meta { display: table-cell; width: 31%; padding-left: 10px; border-left: 1px solid #111827; font-size: 8px; line-height: 1.35; vertical-align: bottom; }
    .certificate-header-meta div { overflow-wrap: anywhere; }

    .sheet {
        height: {{ data_get($certificate->layoutSettings(), 'page.height', 297) }}mm;
        min-height: 0;
        position: relative;
        box-sizing: border-box;
        padding: {{ data_get($certificate->layoutSettings(), 'margins.top', 28) }}mm {{ data_get($certificate->layoutSettings(), 'margins.right', 16) }}mm {{ data_get($certificate->layoutSettings(), 'margins.bottom', 18) }}mm {{ data_get($certificate->layoutSettings(), 'margins.left', 16) }}mm;
        overflow: hidden;
    }

    .title-wrap {
        text-align: {{ data_get($certificate->layoutSettings(), 'typography.title_text_align', 'center') }};
        margin-bottom: 76px;
        transform: translate({{ data_get($certificate->layoutSettings(), 'positions.title.x', 0) }}mm, {{ data_get($certificate->layoutSettings(), 'positions.title.y', 0) }}mm);
    }

    .title-box {
        display: inline-block;
        border: {{ data_get($certificate->layoutSettings(), 'typography.title_border_width', 1) }}px {{ data_get($certificate->layoutSettings(), 'typography.title_border_style', 'solid') }} {{ data_get($certificate->layoutSettings(), 'typography.title_border_color', '#4b5563') }};
        background-color: {{ data_get($certificate->layoutSettings(), 'typography.title_background', '#eef2e3') }};
        padding: {{ data_get($certificate->layoutSettings(), 'typography.title_padding_top', 9) }}px {{ data_get($certificate->layoutSettings(), 'typography.title_padding_right', 42) }}px {{ data_get($certificate->layoutSettings(), 'typography.title_padding_bottom', 9) }}px {{ data_get($certificate->layoutSettings(), 'typography.title_padding_left', 42) }}px;
    }

    .title-box h1 {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'typography.title_size', 18) }}px;
        letter-spacing: {{ data_get($certificate->layoutSettings(), 'typography.title_letter_spacing', 0.01) }}px;
        color: {{ data_get($certificate->layoutSettings(), 'typography.title_color', '#111827') }};
        font-weight: {{ data_get($certificate->layoutSettings(), 'typography.title_font_weight', 500) }};
        text-align: {{ data_get($certificate->layoutSettings(), 'typography.title_text_align', 'center') }};
        text-transform: {{ data_get($certificate->layoutSettings(), 'typography.title_text_transform', 'uppercase') }};
    }

    .content {
        max-width: 100%;
        margin: 0 auto;
        min-height: 550px;
        font-size: {{ data_get($certificate->layoutSettings(), 'typography.body_size', 17) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'typography.font_weight', 400) }};
        line-height: {{ data_get($certificate->layoutSettings(), 'typography.line_height', 2.05) }};
        color: {{ data_get($certificate->layoutSettings(), 'typography.font_color', '#111827') }};
        text-align: {{ data_get($certificate->layoutSettings(), 'typography.text_align', 'justify') }};
        text-justify: inter-word;
        transform: translate({{ data_get($certificate->layoutSettings(), 'positions.body.x', 0) }}mm, {{ data_get($certificate->layoutSettings(), 'positions.body.y', 0) }}mm);
    }

    .content p {
        margin-bottom: 12px;
        text-align: {{ data_get($certificate->layoutSettings(), 'typography.text_align', 'justify') }};
        text-justify: inter-word;
    }

    .content strong,
    .content b,
    .content em {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'typography.placeholder_font_size', 17) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'typography.placeholder_font_weight', 700) }};
        color: {{ data_get($certificate->layoutSettings(), 'typography.placeholder_color', '#b45309') }};
        font-style: italic;
    }

    .spacer {
        height: 0;
    }

    .bottom {
        display: table;
        width: 100%;
        margin-top: 56px;
        transform: translate({{ data_get($certificate->layoutSettings(), 'positions.bottom.x', 0) }}mm, {{ data_get($certificate->layoutSettings(), 'positions.bottom.y', 0) }}mm);
    }

    .watermark { position: absolute; z-index: 0; top: 42%; left: 50%; width: {{ data_get($certificate->layoutSettings(), 'watermark.size', 55) }}%; max-width: none; max-height: none; object-fit: contain; opacity: {{ data_get($certificate->layoutSettings(), 'watermark.opacity', .12) }}; transform: translate(calc(-50% + {{ data_get($certificate->layoutSettings(), 'positions.watermark.x', 0) }}mm), calc(-50% + {{ data_get($certificate->layoutSettings(), 'positions.watermark.y', 0) }}mm)); }

    .reason,
    .signature {
        display: table-cell;
        vertical-align: bottom;
        width: 50%;
    }

    .reason {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.reason.font_size', 15) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.reason.font_weight', 400) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.reason.color', '#111827') }};
        text-align: {{ data_get($certificate->layoutSettings(), 'footer.reason.text_align', 'left') }};
    }

    .reason-title {
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.reason.font_weight', 400) }};
        margin-bottom: 6px;
    }

    .reason-value {
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.reason.font_weight', 400) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.reason.color', '#111827') }};
    }

    .signature {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.principal.font_size', 16) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.principal.font_weight', 400) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.principal.color', '#111827') }};
        text-align: {{ data_get($certificate->layoutSettings(), 'footer.principal.text_align', 'center') }};
    }
    .signature { text-align: center !important; }

    .sig-line {
        width: 180px;
        border-top: 1px solid #555;
        margin: 0 auto 8px;
    }

    .sig-title {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.principal_label.font_size', data_get($certificate->layoutSettings(), 'footer.principal.font_size', 16)) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.principal_label.font_weight', data_get($certificate->layoutSettings(), 'footer.principal.font_weight', 400)) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.principal_label.color', data_get($certificate->layoutSettings(), 'footer.principal.color', '#111827')) }};
        text-align: {{ data_get($certificate->layoutSettings(), 'footer.principal_label.text_align', data_get($certificate->layoutSettings(), 'footer.principal.text_align', 'center')) }};
        line-height: 1.2;
    }

    .sig-name,
    .sig-name {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.principal.font_size', 16) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.principal.font_weight', 400) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.principal.color', '#111827') }};
        line-height: 1.45;
    }

    .sig-phone {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.contact_number.font_size', 14) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.contact_number.font_weight', 400) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.contact_number.color', '#111827') }};
        text-align: {{ data_get($certificate->layoutSettings(), 'footer.contact_number.text_align', 'center') }};
        line-height: 1.45;
    }

    .sig-school {
        font-family: {{ data_get($certificate->layoutSettings(), 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($certificate->layoutSettings(), 'footer.school_name.font_size', data_get($certificate->layoutSettings(), 'footer.principal.font_size', 16)) }}px;
        font-weight: {{ data_get($certificate->layoutSettings(), 'footer.school_name.font_weight', data_get($certificate->layoutSettings(), 'footer.principal.font_weight', 400)) }};
        color: {{ data_get($certificate->layoutSettings(), 'footer.school_name.color', data_get($certificate->layoutSettings(), 'footer.principal.color', '#111827')) }};
        text-align: {{ data_get($certificate->layoutSettings(), 'footer.school_name.text_align', data_get($certificate->layoutSettings(), 'footer.principal.text_align', 'center')) }};
        line-height: 1.45;
    }
</style>
</head>
<body>
    <div class="sheet">
        @include('pages.students.lifecycle.partials.certificate-header', ['setting' => $setting, 'headerLayout' => $certificate->layoutSettings()])
        @if(!empty($setting->logo) && data_get($certificate->layoutSettings(), 'visibility.watermark', true))<img class="watermark" src="{{ asset($setting->logo) }}" alt="">@endif
        <div class="title-wrap" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.title', true) ? 'block' : 'none' }};">
            <div class="title-box">
                <h1>{{ $certificate->name }}</h1>
            </div>
        </div>

        <div class="content" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.body', true) ? 'block' : 'none' }};">
            {!! $certificateTextHtml ?? '' !!}
        </div>

        <div class="spacer"></div>

        <div class="bottom">
            <div class="reason" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.reason', true) ? 'table-cell' : 'none' }};">
                <div class="reason-title">Reason for Leaving School:</div>
                <div class="reason-value">{{ $leavingReason ?? 'No reason provided' }}</div>
            </div>

            <div class="signature" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.principal', true) ? 'table-cell' : 'none' }};">
                <div class="sig-line"></div>
                <div class="sig-title">{{ $principal['designation'] }}</div>
                <div class="sig-name" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.principal_name', true) ? 'block' : 'none' }};">
                    @if($principal['name'] !== '')
                        ({{ $principal['name'] }})
                    @endif
                </div>
                <div class="sig-school" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.school_name', true) ? 'block' : 'none' }};">{{ $principal['school_name'] }}</div>
                @if($principal['phone'] !== '')
                    <div class="sig-phone" style="display: {{ data_get($certificate->layoutSettings(), 'visibility.contact_number', true) ? 'block' : 'none' }};">{{ $principal['phone'] }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
