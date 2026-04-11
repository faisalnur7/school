<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 11px; color: #1e293b; }

    .header { background: #1e293b; color: #fff; padding: 12px 16px; display: table; width: 100%; }
    .header .logo-col { display: table-cell; vertical-align: middle; width: 70px; }
    .header .info-col { display: table-cell; vertical-align: middle; padding-left: 12px; }
    .header h1 { font-size: 16px; font-weight: 700; margin: 0; }
    .header p  { font-size: 10px; color: #94a3b8; margin: 2px 0 0; }

    .profile-row { display: table; width: 100%; margin-top: 12px; }
    .avatar-col   { display: table-cell; width: 90px; vertical-align: top; text-align: center; }
    .avatar-col img { width: 80px; height: 80px; border-radius: 50%; border: 2px solid #e2e8f0; }
    .name-col     { display: table-cell; vertical-align: top; padding-left: 12px; }
    .name-col h2  { font-size: 15px; font-weight: 700; margin: 0 0 2px; }
    .name-col .sub { font-size: 10px; color: #64748b; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
    .badge-active   { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #475569; }

    .section { margin-top: 12px; }
    .section-title { background: #f1f5f9; padding: 4px 8px; font-weight: 700; font-size: 11px;
                     color: #334155; border-left: 3px solid #1e293b; margin-bottom: 6px; }
    .two-col { display: table; width: 100%; border-collapse: collapse; }
    .two-col .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
    .two-col .col:last-child { padding-right: 0; padding-left: 8px; }

    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th { background: #f8fafc; padding: 4px 6px; text-align: left; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569; }
    td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; }

    .row-item { display: table; width: 100%; margin-bottom: 3px; }
    .row-item .lbl { display: table-cell; width: 45%; color: #64748b; font-size: 10px; }
    .row-item .val { display: table-cell; font-weight: 600; font-size: 10px; }

    .footer { margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 6px;
              font-size: 9px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="info-col">
        <h1>Student Profile</h1>
        <p>Generated: {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</div>

{{-- Profile Row --}}
<div class="profile-row">
    <div class="avatar-col">
        @php
            $avatarUrl = $student->image
                ? public_path($student->image)
                : (strtolower($student->gender ?? '') === 'female'
                    ? public_path('assets/dist/img/avatar4.png')
                    : public_path('assets/dist/img/avatar.png'));
        @endphp
        <img src="{{ $avatarUrl }}">
    </div>
    <div class="name-col">
        <h2>{{ $student->full_name_en }}</h2>
        <div class="sub">{{ $student->full_name_bn }}</div>
        <div class="sub" style="margin-top:2px">ID: <strong>{{ $student->student_cid }}</strong></div>
        <div style="margin-top:4px">
            <span class="badge {{ $student->status ? 'badge-active' : 'badge-inactive' }}">
                {{ $student->status ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
</div>

{{-- Basic + Academic --}}
<div class="two-col" style="margin-top:12px">
    <div class="col">
        <div class="section-title">Basic Information</div>
        @foreach([
            ['Gender',          $student->gender_text],
            ['Date of Birth',   optional($student->date_of_birth)->format('d M Y') ?? '—'],
            ['Blood Group',     $student->blood_group_text],
            ['Religion',        $student->religion_text],
            ['Birth Cert No',   $student->birth_certificate_number ?? '—'],
            ['Disability',      $student->disable ? 'Yes' : 'No'],
        ] as [$lbl, $val])
        <div class="row-item"><span class="lbl">{{ $lbl }}</span><span class="val">{{ $val }}</span></div>
        @endforeach
    </div>
    <div class="col">
        <div class="section-title">Academic Information</div>
        @php $ai = $student->academicInformations->last(); @endphp
        @foreach([
            ['Session',   $ai?->academicSession?->name_en ?? '—'],
            ['Class',     $ai?->schoolClass?->name_en ?? '—'],
            ['Section',   $ai?->section?->name_en ?? '—'],
            ['Group',     $ai?->group?->name_en ?? '—'],
            ['Roll',      $ai?->roll ?? '—'],
        ] as [$lbl, $val])
        <div class="row-item"><span class="lbl">{{ $lbl }}</span><span class="val">{{ $val }}</span></div>
        @endforeach
    </div>
</div>

{{-- Parents + Guardian --}}
<div class="two-col" style="margin-top:10px">
    <div class="col">
        <div class="section-title">Parents Information</div>
        @foreach([
            ['Father Name',       $student->father_name ?? '—'],
            ['Father Profession', $student->fathersProfession?->name ?? '—'],
            ['Father Phone',      $student->father_phone ?? '—'],
            ['Father NID',        $student->father_nid_number ?? '—'],
            ['Mother Name',       $student->mother_name ?? '—'],
            ['Mother Profession', $student->mothersProfession?->name ?? '—'],
            ['Mother Phone',      $student->mother_phone ?? '—'],
            ['Mother NID',        $student->mother_nid_number ?? '—'],
        ] as [$lbl, $val])
        <div class="row-item"><span class="lbl">{{ $lbl }}</span><span class="val">{{ $val }}</span></div>
        @endforeach
    </div>
    <div class="col">
        <div class="section-title">Guardian & Contact</div>
        @foreach([
            ['Guardian Name',       $student->guardian_name ?? '—'],
            ['Guardian Relation',   $student->guardian_relation ?? '—'],
            ['Guardian Profession', $student->guardianProfession?->name ?? '—'],
            ['Guardian Phone',      $student->guardian_phone ?? '—'],
            ['Guardian Email',      $student->guardian_email ?? '—'],
            ['Present Address',     $student->present_address ?? '—'],
            ['Permanent Address',   $student->permanent_address ?? '—'],
            ['Annual Income',       $student->annual_income ?? '—'],
        ] as [$lbl, $val])
        <div class="row-item"><span class="lbl">{{ $lbl }}</span><span class="val">{{ $val }}</span></div>
        @endforeach
    </div>
</div>

{{-- Academic History --}}
@if($student->academicInformations->count() > 1)
<div class="section">
    <div class="section-title">Academic History</div>
    <table>
        <thead>
            <tr><th>Session</th><th>Class</th><th>Section</th><th>Group</th><th>Roll</th></tr>
        </thead>
        <tbody>
            @foreach($student->academicInformations as $info)
            <tr>
                <td>{{ $info->academicSession?->name_en ?? '—' }}</td>
                <td>{{ $info->schoolClass?->name_en ?? '—' }}</td>
                <td>{{ $info->section?->name_en ?? '—' }}</td>
                <td>{{ $info->group?->name_en ?? '—' }}</td>
                <td>{{ $info->roll ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    {{ config('app.name') }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}
</div>

</body>
</html>
