<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .header-table td { border: 0; padding: 0; vertical-align: middle; }
    .logo-cell { width: 80px; }
    .logo { width: 64px; height: 64px; object-fit: contain; }
    .school-name { font-size: 18px; font-weight: 700; margin-bottom: 2px; text-align: center; }
    .school-meta { text-align: center; font-size: 10px; color: #444; line-height: 1.45; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 6px; text-align: left; }
    td { padding: 5px 6px; border-bottom: 1px solid #ddd; vertical-align: top; }
    .text-center { text-align: center; }
    .small { font-size: 9px; color: #555; }
</style>
</head>
<body>
@php
    $logoPath = !empty($setting?->logo) ? public_path($setting->logo) : null;
@endphp

@if($setting)
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo" alt="School Logo">
            @endif
        </td>
        <td>
            <div class="school-name">{{ $setting->name }}</div>
            <div class="school-meta">
                @if($setting->address)
                    <div>{{ $setting->address }}</div>
                @endif
                @if($setting->email || $setting->contact_number_1 || $setting->contact_number_2)
                    <div>
                        @if($setting->email) Email: {{ $setting->email }} @endif
                        @if($setting->contact_number_1) | Phone: {{ $setting->contact_number_1 }} @endif
                        @if($setting->contact_number_2) , {{ $setting->contact_number_2 }} @endif
                    </div>
                @endif
                @if($setting->slogan)
                    <div>{{ $setting->slogan }}</div>
                @endif
            </div>
        </td>
        <td class="logo-cell"></td>
    </tr>
</table>
@endif

<h2>Student List</h2>
@if(!empty($filterHeading['class']) || !empty($filterHeading['section']) || !empty($filterHeading['group']) || !empty($filterHeading['session']))
<p class="sub" style="margin-bottom: 4px;">
    @if(!empty($filterHeading['session']))
        Session: {{ $filterHeading['session'] }}
    @endif
    @if(!empty($filterHeading['class']))
        &nbsp;|&nbsp; Class: {{ $filterHeading['class'] }}
    @endif
    @if(!empty($filterHeading['section']))
        &nbsp;|&nbsp; Section: {{ $filterHeading['section'] }}
    @endif
    @if(!empty($filterHeading['group']))
        &nbsp;|&nbsp; Group: {{ $filterHeading['group'] }}
    @endif
</p>
@endif
<p class="sub">
    Total Students: {{ $students->count() }}
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            @foreach($selectedColumns as $column)
                <th>{{ $pdfColumnOptions[$column] ?? ucfirst(str_replace('_', ' ', $column)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
            @php
                $academicInformation = $student->academicInformations->last();
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                @foreach($selectedColumns as $column)
                    <td>
                        @switch($column)
                            @case('student_cid')
                                {{ $student->student_cid ?? '—' }}
                                @break

                            @case('roll')
                                {{ $academicInformation?->roll ?? '—' }}
                                @break

                            @case('full_name_en')
                                <strong>{{ $student->full_name_en ?? '—' }}</strong>
                                @break

                            @case('full_name_bn')
                                {{ $student->full_name_bn ?? '—' }}
                                @break

                            @case('class')
                                {{ $academicInformation?->schoolClass?->name_en ?? '—' }}
                                @break

                            @case('section')
                                {{ $academicInformation?->section?->name_en ?? '—' }}
                                @break

                            @case('group')
                                {{ $academicInformation?->group?->name_en ?? '—' }}
                                @break

                            @case('gender')
                                {{ $student->gender_text ?? '—' }}
                                @break

                            @case('religion')
                                {{ $student->religion_text ?? '—' }}
                                @break

                            @case('date_of_birth')
                                {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '—' }}
                                @break

                            @case('blood_group')
                                {{ $student->blood_group_text ?? '—' }}
                                @break

                            @case('father_name')
                                {{ $student->father_name ?? '—' }}
                                @break

                            @case('mother_name')
                                {{ $student->mother_name ?? '—' }}
                                @break

                            @case('father_phone')
                                {{ $student->father_phone ?? '—' }}
                                @break

                            @case('mother_phone')
                                {{ $student->mother_phone ?? '—' }}
                                @break

                            @case('guardian_phone')
                                {{ $student->guardian_phone ?? '—' }}
                                @break
                            
                            @case('present_address')
                                {{ $student->present_address ?? '—' }}
                                @break

                            @case('status')
                                {{ $student->status ? 'Active' : 'Inactive' }}
                                @break

                            @default
                                —
                        @endswitch
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
