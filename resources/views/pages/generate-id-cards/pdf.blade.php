<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; background:#fff; }

.pair-wrap  { margin-bottom:12px; page-break-inside:avoid; }
.pair-table { border-collapse:collapse; }
.cut-col    { width:14px; text-align:center; vertical-align:middle; color:#bbb; font-size:8px; letter-spacing:3px; }

/* ── Card shell ── */
.card {
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid #ccc;
    position: relative;
    background: #fff;
}
.card-portrait  { width: 200px; }
.card-landscape { width: 310px; }

/* ── Absolute background image ── */
.card-bg-wrap {
    position: absolute;
    top:0; left:0; right:0; bottom:0;
    overflow: hidden;
    border-radius: 12px;
    z-index: 0;
}
.card-bg-img {
    width:100%; height:100%;
    object-fit: cover;
    opacity: 0.08;
}

/* ── All content above bg ── */
.card-inner { position: relative; z-index: 1; }

/* ── Header ── */
.card-header {
    color: #fff;
    padding: 9px 11px 7px;
    text-align: center;
}
.card-header-logo {
    height: 26px;
    vertical-align: middle;
    margin-bottom: 3px;
    filter: brightness(0) invert(1);
}
.card-header-name {
    font-size: 9.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .05em;
    line-height: 1.3;
}
.card-header-slogan {
    font-size: 7px;
    opacity: .8;
    margin-top: 1px;
}
.card-header-badge {
    display: inline-block;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 10px;
    font-size: 7px;
    font-weight: bold;
    letter-spacing: .1em;
    padding: 2px 8px;
    margin-top: 4px;
}

/* ── Landscape header uses table ── */
.lh-table { width:100%; border-collapse:collapse; }
.lh-left  { vertical-align:middle; }
.lh-right { vertical-align:middle; text-align:right; white-space:nowrap; }

/* ── Photo ── */
.photo-portrait {
    width: 68px; height: 82px;
    border-radius: 7px;
    border: 2px solid rgba(255,255,255,.5);
    object-fit: cover;
    display: block;
    margin: 0 auto;
}
.photo-landscape {
    width: 72px; height: 86px;
    border-radius: 7px;
    border: 2px solid rgba(255,255,255,.5);
    object-fit: cover;
    display: block;
}

/* ── Info rows ── */
.info-name    { font-size: 12px; font-weight: bold; line-height: 1.3; }
.info-name-bn { font-size: 9.5px; color: #475569; margin-top: 1px; }
.info-divider { height: 2px; border-radius: 2px; margin: 5px 0; }
.info-row     { font-size: 8.5px; margin-bottom: 2.5px; }
.info-lbl     { color: #94a3b8; font-weight: bold; text-transform: uppercase; font-size: 7.5px; min-width:38px; display:inline-block; }
.info-val     { font-weight: bold; color: #1e293b; }
.info-blood   { color: #dc2626; font-weight: bold; }

/* ── Footer ── */
.card-footer {
    color: rgba(255,255,255,.9);
    font-size: 7.5px;
    padding: 5px 8px;
    text-align: center;
}

/* ── Back card ── */
.back-section  { margin-bottom: 5px; }
.back-title    { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing:.05em; color:#222; border-bottom:1px solid #ccc; padding-bottom:2px; margin-bottom:3px; }
.back-row      { font-size: 8px; margin-bottom: 2px; }
.back-lbl      { color:#666; font-weight:bold; text-transform:uppercase; font-size:7px; min-width:38px; display:inline-block; }
.back-val      { font-weight:bold; color:#111; }
.back-notice   { font-size:7px; color:#aaa; text-align:center; font-style:italic; margin-top:5px; padding-top:4px; border-top:1px dashed #ddd; }
</style>
</head>
<body>
@php
    $isLandscape = $template->orientation === 'landscape';
    $cardClass   = $isLandscape ? 'card-landscape' : 'card-portrait';
    $idColor     = $setting?->id_card_color   ?? '#1e3a5f';
    $secondary   = $setting?->secondary_color ?? '#2563eb';

    // Resolve all paths for mPDF (must be absolute local paths)
    $logoPath = null;
    if ($setting?->logo && file_exists(public_path($setting->logo))) {
        $logoPath = public_path($setting->logo);
    }

    $frontBgPath = null;
    foreach (['front_bg_image', 'background_image'] as $f) {
        if (!empty($template->$f) && file_exists(public_path($template->$f))) {
            $frontBgPath = public_path($template->$f);
            break;
        }
    }

    $backBgPath = null;
    if (!empty($template->back_bg_image) && file_exists(public_path($template->back_bg_image))) {
        $backBgPath = public_path($template->back_bg_image);
    } elseif ($frontBgPath) {
        $backBgPath = $frontBgPath;
    }
@endphp

@foreach($students as $student)
@php
    $ai = $student->academicInformations->first();

    $placeholder = $student->gender == App\Models\Student::FEMALE
        ? public_path('assets/img/female-placeholder.png')
        : public_path('assets/img/male-placeholder.png');

    $photoPath = ($student->image && file_exists(public_path($student->image)))
        ? public_path($student->image)
        : $placeholder;
@endphp

<div class="pair-wrap">
<table class="pair-table">
<tr>

{{-- ══════════ FRONT CARD ══════════ --}}
<td style="vertical-align:top">
<div class="card {{ $cardClass }}">

    {{-- Background --}}
    @if($frontBgPath)
    <div class="card-bg-wrap">
        <img src="{{ $frontBgPath }}" class="card-bg-img">
    </div>
    @endif

    <div class="card-inner">

        {{-- Header --}}
        @if($isLandscape)
        <div class="card-header" style="background:{{ $idColor }}">
            <table class="lh-table">
                <tr>
                    <td class="lh-left">
                        @if($logoPath)<img src="{{ $logoPath }}" class="card-header-logo">@endif
                        <span class="card-header-name">{{ $setting?->name ?? 'School Name' }}</span>
                        @if($setting?->slogan)<div class="card-header-slogan">{{ $setting->slogan }}</div>@endif
                    </td>
                    <td class="lh-right">
                        <span class="card-header-badge">STUDENT ID</span>
                    </td>
                </tr>
            </table>
        </div>
        {{-- Landscape body --}}
        <table style="width:100%;border-collapse:collapse;padding:10px">
            <tr>
                <td style="width:88px;vertical-align:top;padding:10px 0 8px 10px">
                    <img src="{{ $photoPath }}" class="photo-landscape">
                </td>
                <td style="vertical-align:top;padding:10px 10px 8px 10px">
                    <div class="info-name" style="color:{{ $idColor }}">{{ $student->full_name_en }}</div>
                    @if($student->full_name_bn)<div class="info-name-bn">{{ $student->full_name_bn }}</div>@endif
                    <div class="info-divider" style="background:{{ $idColor }}"></div>
                    <div class="info-row"><span class="info-lbl">ID</span><span class="info-val">{{ $student->student_cid }}</span></div>
                    @if($ai)
                        <div class="info-row"><span class="info-lbl">Class</span><span class="info-val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span></div>
                        @if($ai->roll)<div class="info-row"><span class="info-lbl">Roll</span><span class="info-val">{{ $ai->roll }}</span></div>@endif
                        @if($ai->group)<div class="info-row"><span class="info-lbl">Group</span><span class="info-val">{{ $ai->group->name_en }}</span></div>@endif
                        <div class="info-row"><span class="info-lbl">Session</span><span class="info-val">{{ $ai->academicSession?->name_en ?? '—' }}</span></div>
                    @endif
                    @if($student->date_of_birth)<div class="info-row"><span class="info-lbl">DOB</span><span class="info-val">{{ $student->date_of_birth->format('d M Y') }}</span></div>@endif
                    @if($student->blood_group)<div class="info-row"><span class="info-lbl">Blood</span><span class="info-val info-blood">{{ $student->blood_group_text }}</span></div>@endif
                </td>
            </tr>
        </table>

        @else
        {{-- Portrait header --}}
        <div class="card-header" style="background:{{ $idColor }}">
            @if($logoPath)<img src="{{ $logoPath }}" class="card-header-logo"><br>@endif
            <div class="card-header-name">{{ $setting?->name ?? 'School Name' }}</div>
            @if($setting?->slogan)<div class="card-header-slogan">{{ $setting->slogan }}</div>@endif
            <div class="card-header-badge">STUDENT ID CARD</div>
        </div>
        {{-- Portrait photo --}}
        <div style="text-align:center;padding:9px 0 5px">
            <img src="{{ $photoPath }}" class="photo-portrait">
        </div>
        {{-- Portrait info --}}
        <div style="padding:4px 11px 8px">
            <div class="info-name" style="text-align:center;color:{{ $idColor }}">{{ $student->full_name_en }}</div>
            @if($student->full_name_bn)<div class="info-name-bn" style="text-align:center">{{ $student->full_name_bn }}</div>@endif
            <div class="info-divider" style="background:{{ $idColor }}"></div>
            <div class="info-row"><span class="info-lbl">ID</span><span class="info-val">{{ $student->student_cid }}</span></div>
            @if($ai)
                <div class="info-row"><span class="info-lbl">Class</span><span class="info-val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span></div>
                @if($ai->roll)<div class="info-row"><span class="info-lbl">Roll</span><span class="info-val">{{ $ai->roll }}</span></div>@endif
                @if($ai->group)<div class="info-row"><span class="info-lbl">Group</span><span class="info-val">{{ $ai->group->name_en }}</span></div>@endif
                <div class="info-row"><span class="info-lbl">Session</span><span class="info-val">{{ $ai->academicSession?->name_en ?? '—' }}</span></div>
            @endif
            @if($student->date_of_birth)<div class="info-row"><span class="info-lbl">DOB</span><span class="info-val">{{ $student->date_of_birth->format('d M Y') }}</span></div>@endif
            @if($student->blood_group)<div class="info-row"><span class="info-lbl">Blood</span><span class="info-val info-blood">{{ $student->blood_group_text }}</span></div>@endif
        </div>
        @endif

        {{-- Front footer --}}
        <div class="card-footer" style="background:{{ $idColor }}">
            @if($setting?->contact_number_1)📞 {{ $setting->contact_number_1 }}&nbsp;&nbsp;@endif
            @if($setting?->contact_number_2)📞 {{ $setting->contact_number_2 }}&nbsp;&nbsp;@endif
            @if($setting?->website)🌐 {{ $setting->website }}@endif
        </div>

    </div>{{-- /.card-inner --}}
</div>{{-- /.card --}}
</td>

{{-- Cut line --}}
<td class="cut-col">|<br>|<br>|<br>|<br>|<br>|<br>|<br>|</td>

{{-- ══════════ BACK CARD (B&W) ══════════ --}}
<td style="vertical-align:top">
<div class="card {{ $cardClass }}" style="border:1.5px solid #333">

    @if($backBgPath)
    <div class="card-bg-wrap">
        <img src="{{ $backBgPath }}" class="card-bg-img" style="opacity:0.04">
    </div>
    @endif

    <div class="card-inner">

        {{-- Back header B&W --}}
        @if($isLandscape)
        <div class="card-header" style="background:#222">
            <table class="lh-table">
                <tr>
                    <td class="lh-left">
                        <span class="card-header-name">{{ $setting?->name ?? 'School Name' }}</span>
                        @if($setting?->slogan)<div class="card-header-slogan">{{ $setting->slogan }}</div>@endif
                    </td>
                    <td class="lh-right"><span class="card-header-badge">BACK</span></td>
                </tr>
            </table>
        </div>
        @else
        <div class="card-header" style="background:#222">
            <div class="card-header-name">{{ $setting?->name ?? 'School Name' }}</div>
            @if($setting?->slogan)<div class="card-header-slogan">{{ $setting->slogan }}</div>@endif
            <div class="card-header-badge">BACK</div>
        </div>
        @endif

        {{-- Back body --}}
        <div style="padding:8px 11px 5px">

            <div class="back-section">
                <div class="back-title">Student</div>
                <div class="back-row"><span class="back-lbl">Name</span><span class="back-val">{{ $student->full_name_en }}</span></div>
                <div class="back-row"><span class="back-lbl">ID</span><span class="back-val">{{ $student->student_cid }}</span></div>
                @if($ai?->roll)<div class="back-row"><span class="back-lbl">Roll</span><span class="back-val">{{ $ai->roll }}</span></div>@endif
            </div>

            <div class="back-section">
                <div class="back-title">Parent / Guardian</div>
                @if($student->father_name)
                    <div class="back-row"><span class="back-lbl">Father</span><span class="back-val">{{ $student->father_name }}</span></div>
                @endif
                @if($student->mother_name)
                    <div class="back-row"><span class="back-lbl">Mother</span><span class="back-val">{{ $student->mother_name }}</span></div>
                @endif
                @if($student->father_phone || $student->mother_phone)
                    <div class="back-row"><span class="back-lbl">Contacts</span><span class="back-val">{{ implode(', ', array_filter([$student->father_phone, $student->mother_phone])) }}</span></div>
                @endif
                @if($student->present_address)<div class="back-row"><span class="back-lbl">Address</span><span class="back-val">{{ Str::limit($student->present_address, 38) }}</span></div>@endif
            </div>

            <div class="back-section">
                <div class="back-title">School Contact</div>
                @if($setting?->address)<div class="back-row"><span class="back-lbl">Address</span><span class="back-val">{{ Str::limit($setting->address, 40) }}</span></div>@endif
                @if($setting?->contact_number_1 || $setting?->contact_number_2)
                    <div class="back-row"><span class="back-lbl">Contact</span><span class="back-val">{{ implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])) }}</span></div>
                @endif
                @if($setting?->email)<div class="back-row"><span class="back-lbl">Email</span><span class="back-val">{{ $setting->email }}</span></div>@endif
                @if($setting?->website)<div class="back-row"><span class="back-lbl">Web</span><span class="back-val">{{ $setting->website }}</span></div>@endif
            </div>

            <div class="back-notice">If found, please return to the school.</div>
        </div>

        {{-- Back footer B&W --}}
        <div class="card-footer" style="background:#222">
            @if($setting?->eiin)EIIN: {{ $setting->eiin }}&nbsp;&nbsp;@endif
            @if($setting?->whatsapp_number)📱 {{ $setting->whatsapp_number }}@endif
        </div>

    </div>{{-- /.card-inner --}}
</div>{{-- /.card --}}
</td>

</tr>
</table>
</div>{{-- /.pair-wrap --}}

@endforeach
</body>
</html>
