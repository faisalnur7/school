@php
    $fs = '9px';
    $bold = 'font-weight:bold;';
@endphp

<div style="font-size:{{ $fs }};line-height:1.6;color:#1a1a1a">
    <div style="{{ $bold }}font-size:11px;color:#1e3a5f">{{ $student->full_name_en }}</div>
    @if($student->full_name_bn)
        <div style="font-size:10px;color:#444">{{ $student->full_name_bn }}</div>
    @endif

    <div style="margin-top:3px">
        <span style="color:#555">ID:</span>
        <span style="{{ $bold }}">{{ $student->student_cid }}</span>
    </div>

    @if($ai)
        <div>
            <span style="color:#555">Class:</span>
            <span style="{{ $bold }}">{{ $ai->schoolClass?->name_en ?? '—' }}</span>
            @if($ai->section)
                &nbsp;<span style="color:#555">Sec:</span>
                <span style="{{ $bold }}">{{ $ai->section->name_en }}</span>
            @endif
        </div>
        @if($ai->roll)
            <div><span style="color:#555">Roll:</span> <span style="{{ $bold }}">{{ $ai->roll }}</span></div>
        @endif
        @if($ai->group)
            <div><span style="color:#555">Group:</span> <span style="{{ $bold }}">{{ $ai->group->name_en }}</span></div>
        @endif
        <div><span style="color:#555">Session:</span> {{ $ai->academicSession?->name_en ?? '—' }}</div>
    @endif

    @if($student->date_of_birth)
        <div><span style="color:#555">DOB:</span> {{ $student->date_of_birth->format('d M Y') }}</div>
    @endif
    @if($student->blood_group)
        <div><span style="color:#555">Blood:</span> <span style="color:#dc2626;{{ $bold }}">{{ $student->blood_group_text }}</span></div>
    @endif

    @if($setting?->whatsapp_number || $setting?->website)
        <div style="margin-top:4px;padding-top:3px;border-top:1px dashed #ccc;font-size:8px;color:#666">
            @if($setting->whatsapp_number) 📞 {{ $setting->whatsapp_number }} @endif
            @if($setting->website) 🌐 {{ $setting->website }} @endif
        </div>
    @endif
</div>
