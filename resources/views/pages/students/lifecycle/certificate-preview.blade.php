@extends('layouts.master')

@section('contents')
@php($layout = $certificate->layoutSettings())
<div class="container-fluid">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-700 via-cyan-700 to-slate-900 p-8 mb-6 no-print">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-18 w-18 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                    <i class="fas fa-certificate text-white text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-white text-3xl font-bold m-0">{{ $certificate->name }}</h3>
                    <p class="text-teal-100 text-base mt-1 mb-0">
                        {{ $certificate->description ?: 'Certificate preview' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('students.certificates', ['search' => $student->student_cid]) }}"
                   class="inline-flex items-center rounded-lg bg-white/10 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Hub
                </a>
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center rounded-lg bg-rose-500 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-rose-600">
                    <i class="fas fa-print mr-2"></i> Print Preview
                </button>
            </div>
        </div>
    </div>

    <div class="bg-slate-100 rounded-3xl p-4 shadow-inner">
        <div class="mx-auto bg-white border border-slate-300 shadow-xl certificate-sheet" style="width: min(100%, {{ data_get($layout, 'page.width', 210) }}mm); height: {{ data_get($layout, 'page.height', 297) }}mm; min-height: 0; padding: {{ data_get($layout, 'margins.top', 28) }}mm {{ data_get($layout, 'margins.right', 16) }}mm {{ data_get($layout, 'margins.bottom', 18) }}mm {{ data_get($layout, 'margins.left', 16) }}mm;">
            @include('pages.students.lifecycle.partials.certificate-header', ['setting' => $setting, 'headerLayout' => $layout])
            @if(!empty($setting->logo) && data_get($layout, 'visibility.watermark', true))<img class="certificate-watermark" src="{{ asset($setting->logo) }}" alt="" style="width: {{ data_get($layout, 'watermark.size', 55) }}%; opacity: {{ data_get($layout, 'watermark.opacity', .12) }}; transform: translate(calc(-50% + {{ data_get($layout, 'positions.watermark.x', 0) }}mm), calc(-50% + {{ data_get($layout, 'positions.watermark.y', 0) }}mm));">@endif
            <div class="mb-10 certificate-title" style="display: {{ data_get($layout, 'visibility.title', true) ? 'block' : 'none' }}; text-align: {{ data_get($layout, 'typography.title_text_align', 'center') }}; transform: translate({{ data_get($layout, 'positions.title.x', 0) }}mm, {{ data_get($layout, 'positions.title.y', 0) }}mm);">
                <div class="inline-block" style="display: table; margin: 0 auto; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'typography.title_size', 18) }}px; color: {{ data_get($layout, 'typography.title_color', '#111827') }}; background-color: {{ data_get($layout, 'typography.title_background', '#eef2e3') }}; font-weight: {{ data_get($layout, 'typography.title_font_weight', 500) }}; text-align: {{ data_get($layout, 'typography.title_text_align', 'center') }}; border: {{ data_get($layout, 'typography.title_border_width', 1) }}px {{ data_get($layout, 'typography.title_border_style', 'solid') }} {{ data_get($layout, 'typography.title_border_color', '#4b5563') }}; padding: {{ data_get($layout, 'typography.title_padding_top', 9) }}px {{ data_get($layout, 'typography.title_padding_right', 42) }}px {{ data_get($layout, 'typography.title_padding_bottom', 9) }}px {{ data_get($layout, 'typography.title_padding_left', 42) }}px; letter-spacing: {{ data_get($layout, 'typography.title_letter_spacing', 0.01) }}px; text-transform: {{ data_get($layout, 'typography.title_text_transform', 'uppercase') }};">
                    <div>
                        {{ $certificate->name }}
                    </div>
                </div>
            </div>

            <div class="certificate-body" style="display: {{ data_get($layout, 'visibility.body', true) ? 'block' : 'none' }}; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; color: {{ data_get($layout, 'typography.font_color', '#111827') }}; font-size: {{ data_get($layout, 'typography.body_size', 17) }}px; font-weight: {{ data_get($layout, 'typography.font_weight', 400) }}; line-height: {{ data_get($layout, 'typography.line_height', 2.05) }}; transform: translate({{ data_get($layout, 'positions.body.x', 0) }}mm, {{ data_get($layout, 'positions.body.y', 0) }}mm);">
                <div class="certificate-preview-text" style="text-align: {{ data_get($layout, 'typography.text_align', 'justify') }};">
                    {!! $certificateTextHtml ?? '' !!}
                </div>
            </div>

            <div class="certificate-bottom flex items-end justify-between gap-8" style="transform: translate({{ data_get($layout, 'positions.bottom.x', 0) }}mm, {{ data_get($layout, 'positions.bottom.y', 0) }}mm);">
                <div class="max-w-xs text-slate-600" style="display: {{ data_get($layout, 'visibility.reason', true) ? 'block' : 'none' }}; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'footer.reason.font_size', 15) }}px; font-weight: {{ data_get($layout, 'footer.reason.font_weight', 400) }}; color: {{ data_get($layout, 'footer.reason.color', '#111827') }}; text-align: {{ data_get($layout, 'footer.reason.text_align', 'left') }};">
                <div class="font-semibold text-slate-800 mb-2" style="font-weight: {{ data_get($layout, 'footer.reason.font_weight', 400) }};">Reason for Leaving School:</div>
                <div class="font-bold text-slate-700" style="font-weight: {{ data_get($layout, 'footer.reason.font_weight', 400) }};">{{ $leavingReason ?? 'No reason provided' }}</div>
            </div>

            <div class="text-right" style="display: {{ data_get($layout, 'visibility.principal', true) ? 'block' : 'none' }}; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'footer.principal.font_size', 16) }}px; font-weight: {{ data_get($layout, 'footer.principal.font_weight', 400) }}; color: {{ data_get($layout, 'footer.principal.color', '#111827') }}; text-align: {{ data_get($layout, 'footer.principal.text_align', 'center') }};">
                <div class="mx-auto mb-3 w-44 border-t border-slate-400"></div>
                <div class="text-2xl font-bold text-slate-800" style="font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'footer.principal_label.font_size', data_get($layout, 'footer.principal.font_size', 16)) }}px; font-weight: {{ data_get($layout, 'footer.principal_label.font_weight', data_get($layout, 'footer.principal.font_weight', 400)) }}; color: {{ data_get($layout, 'footer.principal_label.color', data_get($layout, 'footer.principal.color', '#111827')) }}; text-align: {{ data_get($layout, 'footer.principal_label.text_align', data_get($layout, 'footer.principal.text_align', 'center')) }};">
                    {{ $principal['designation'] }}
                </div>
                <div class="text-sm text-slate-600" style="display: {{ data_get($layout, 'visibility.principal_name', true) ? 'block' : 'none' }};">
                    @if($principal['name'] !== '')
                        ({{ $principal['name'] }})
                    @endif
                </div>
                <div class="text-sm text-slate-600" style="display: {{ data_get($layout, 'visibility.school_name', true) ? 'block' : 'none' }}; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'footer.school_name.font_size', data_get($layout, 'footer.principal.font_size', 16)) }}px; font-weight: {{ data_get($layout, 'footer.school_name.font_weight', data_get($layout, 'footer.principal.font_weight', 400)) }}; color: {{ data_get($layout, 'footer.school_name.color', data_get($layout, 'footer.principal.color', '#111827')) }}; text-align: {{ data_get($layout, 'footer.school_name.text_align', data_get($layout, 'footer.principal.text_align', 'center')) }};">
                    {{ $principal['school_name'] }}
                </div>
                @if($principal['phone'] !== '')
                    <div class="text-sm text-slate-600" style="display: {{ data_get($layout, 'visibility.contact_number', true) ? 'block' : 'none' }}; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; font-size: {{ data_get($layout, 'footer.contact_number.font_size', 14) }}px; font-weight: {{ data_get($layout, 'footer.contact_number.font_weight', 400) }}; color: {{ data_get($layout, 'footer.contact_number.color', '#111827') }}; text-align: {{ data_get($layout, 'footer.contact_number.text_align', 'center') }};">{{ $principal['phone'] }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;700;800&display=swap');
    .certificate-preview-text strong {
        font-weight: 700;
        font-style: italic;
        font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }};
        font-size: {{ data_get($layout, 'typography.placeholder_font_size', 17) }}px;
        font-weight: {{ data_get($layout, 'typography.placeholder_font_weight', 700) }};
        color: {{ data_get($layout, 'typography.placeholder_color', '#b45309') }};
    }

    .certificate-preview-text {
        text-align: justify;
        text-justify: inter-word;
    }

    .certificate-preview-text p {
        text-align: {{ data_get($layout, 'typography.text_align', 'justify') }} !important;
        text-justify: inter-word;
        margin-bottom: 12px;
    }

    .certificate-header { position: relative; z-index: 2; margin-bottom: 24px; color: #111827; font-family: Arial, Helvetica, sans-serif; line-height: 1.15; }
    .certificate-header-slogan { min-height: 18px; margin-bottom: 5px; text-align: right; font-family: 'Patrick Hand', cursive; font-size: 18px; font-weight: 400; letter-spacing: .04em; }
    .certificate-header-main { display: flex; align-items: flex-end; gap: 10px; padding: 5px 0 8px; border-bottom: 1px solid #111827; }
    .certificate-header-logo { flex: 0 0 auto; width: 48px; height: 48px; object-fit: contain; }
    .certificate-header-names { flex: 1 1 auto; min-width: 0; }
    .certificate-header-bangla { font-family: "Noto Sans Bengali", sans-serif; font-size: clamp(18px, 2.2vw, 29px); font-weight: 800; line-height: 1.05; white-space: nowrap; }
    .certificate-header-english { margin-top: 3px; font-size: clamp(11px, 1.25vw, 17px); font-weight: 800; letter-spacing: .04em; white-space: nowrap; }
    .certificate-header-meta { flex: 0 0 31%; min-width: 145px; padding-left: 10px; border-left: 1px solid #111827; font-size: 9px; line-height: 1.35; }
    .certificate-header-meta div { overflow-wrap: anywhere; }

    .certificate-sheet { position: relative; overflow: hidden; box-sizing: border-box; font-family: {{ data_get($layout, 'typography.font_family', 'Georgia, Times New Roman, serif') }}; color: {{ data_get($layout, 'typography.font_color', '#111827') }}; }
    .certificate-sheet > * { position: relative; z-index: 1; }
    .certificate-watermark { position: absolute !important; z-index: 0 !important; top: 42%; left: 50%; max-width: none; max-height: none; object-fit: contain; pointer-events: none; }
    .certificate-title { margin-bottom: 76px !important; }
    .certificate-body { min-height: 550px; }
    .certificate-bottom { display: flex; align-items: flex-end; justify-content: space-between; gap: 2rem; margin-top: 56px; }
    .certificate-bottom > .max-w-xs { max-width: 46%; }
    .certificate-bottom > .text-right { min-width: 44%; text-align: center !important; }

    .certificate-body {
        width: 100%;
        max-width: none;
    }

    @page {
        size: {{ data_get($layout, 'page.width', 210) }}mm {{ data_get($layout, 'page.height', 297) }}mm;
        margin: 0;
    }

    @media print {
        .no-print { display: none !important; }
        html, body {
            background: #fff !important;
            width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            min-width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            height: auto !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        .container-fluid {
            padding: 0 !important;
            width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            min-width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            max-width: none !important;
            margin: 0 !important;
        }
        .container-fluid > * {
            width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            min-width: {{ data_get($layout, 'page.width', 210) }}mm !important;
        }
        .certificate-sheet {
            box-shadow: none !important;
            border: 0 !important;
            margin: 0 auto !important;
            width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            height: {{ data_get($layout, 'page.height', 297) }}mm !important;
            min-height: 0 !important;
            padding: {{ data_get($layout, 'margins.top', 28) }}mm {{ data_get($layout, 'margins.right', 16) }}mm {{ data_get($layout, 'margins.bottom', 18) }}mm {{ data_get($layout, 'margins.left', 16) }}mm !important;
        }
        .certificate-header-bangla { font-size: 23px; }
        .certificate-header-english { font-size: 13px; }
        .certificate-header-meta { font-size: 8px; }
        .bg-slate-100 {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            width: {{ data_get($layout, 'page.width', 210) }}mm !important;
            min-width: {{ data_get($layout, 'page.width', 210) }}mm !important;
        }
        .certificate-preview-text {
            width: 100% !important;
            max-width: none !important;
            text-align: justify !important;
            text-justify: inter-word !important;
        }
        .certificate-body {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .certificate-preview-text p {
            text-align: justify !important;
            text-justify: inter-word !important;
            margin-bottom: 10px !important;
        }
        .certificate-bottom { margin-top: 56px !important; }
    }
</style>
@endsection
