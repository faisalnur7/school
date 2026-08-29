@php
    $data = $application->applicant_data ?? [];
    $school = \App\Models\SchoolSetting::current();
    $logoPath = !empty($school->logo) && file_exists(public_path($school->logo))
        ? public_path($school->logo)
        : null;
    $gender = \App\Models\Student::GENDERS[(int) ($data['gender'] ?? $application->gender)] ?? '-';
    $religion = \App\Models\Student::RELIGIONS[(int) ($data['religion'] ?? $application->religion)] ?? '-';
    $bloodGroup = \App\Models\Student::BLOOD_GROUPS[(int) ($data['blood_group'] ?? $application->blood_group)] ?? '-';
    $guardianType = (int) ($data['guardian_type'] ?? $application->guardian_type ?? 1);
    $guardianLabel = [1 => 'Father', 2 => 'Mother', 3 => 'Other'][$guardianType] ?? 'Father';
    $image = $application->image ?? data_get($data, 'image');
    $imagePath = !empty($image) && file_exists(public_path($image))
        ? public_path($image)
        : null;
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* A4 is configured by AdmissionController; mPDF loops when size is repeated here. */
        body { color: #000; font-family: Arial; font-size: 10px; margin: 0; }
        .card { height: 273mm; padding: 0; width: 100%; }
        .admission-information { border: 1.5px solid #000; box-sizing: border-box; height: 142mm; padding: 9px 12px 7px; page-break-inside: avoid; }
        .header { border-bottom: 1.5px solid #000; padding-bottom: 8px; }
        .header-table, .data-table, .candidate-table { border-collapse: collapse; width: 100%; }
        .logo-cell { width: 70px; }
        .logo { height: 48px; object-fit: contain; width: 48px; }
        .student-photo { border: 1px solid #000; height: 58px; object-fit: cover; width: 45px; }
        .header-copy { text-align: center; }
        .header h1 { color: #000; font-size: 19px; margin: 0; }
        .header p { font-size: 10px; margin: 3px 0; }
        .address { color: #000; font-size: 10px !important; }
        .title { background: #f1f1f1; border-left: 4px solid #000; color: #000; font-size: 11px; font-weight: bold; margin: 6px 0 0; padding: 5px 8px; }
        .data-table td { border-bottom: 1px solid #555; padding: 4px 7px; vertical-align: top; width: 50%; }
        .label { color: #000; display: inline; font-size: 10px; font-weight: bold; padding-right: 10px; }
        .label:after { content: ''; }
        .value { display: inline; font-size: 10px; font-weight: bold; }
        .instructions { background: #fff; border: 1px solid #000; color: #000; line-height: 13px; padding: 7px; white-space: normal; }
        .instructions ul { list-style-type: disc; margin: 3px 0; padding-left: 18px; }
        .instructions ol { list-style-type: decimal; margin: 3px 0; padding-left: 18px; }
        .instructions li { display: list-item; }
        .cut-mark { border-top: 1px dashed #000; color: #000; font-size: 9px; letter-spacing: 1px; margin: 8px 0 7px; text-align: center; }
        .cut-mark span { background: #fff; padding: 0 8px; position: relative; top: -6px; }
        .admit-card-section { background: #fff; border: 2px solid #000; box-sizing: border-box; height: 104mm; padding: 7px; page-break-inside: avoid; }
        .candidate-strip { background: #fff; margin-top: 9px; padding: 0; }
        .candidate-strip h2 { background: #f1f1f1; border-bottom: 2px solid #000; color: #000; font-size: 12px; margin: 0 -7px 7px; padding: 6px 8px; }
        .candidate-strip .candidate-table { background: #fff; border: 1px solid #555; }
        .candidate-table td { border-bottom: 1px solid #555; padding: 5px 7px; width: 42%; }
        .candidate-table .photo-cell { border-bottom: 0; text-align: right; vertical-align: top; width: 82px; }
        .candidate-table .label { font-size: 9px; }
        .candidate-table .value { color: #000; font-size: 10px; }
        .student-header { background: #fff; border: 1px solid #000; margin-top: 0; padding: 8px; }
        .student-header-table { border-collapse: collapse; width: 100%; }
        .student-header-copy { text-align: center; }
        .student-header h1 { color: #000; font-size: 16px; margin: 0; }
        .student-header p { font-size: 9px; margin: 2px 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="admission-information">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if($logoPath)
                            <img class="logo" src="{{ $logoPath }}" alt="Logo">
                        @endif
                    </td>
                    <td class="header-copy">
                        <h1>{{ $school->name ?? config('app.name') }}</h1>
                        <p>Admission Application · Office Copy</p>
                        <p>{{ $school->address ?? '' }}</p>
                    </td>
                    <td class="logo-cell" style="text-align: right;">
                        @if($imagePath)
                            <img class="student-photo" src="{{ $imagePath }}" alt="Student photo">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="title">Examination details</div>
        <table class="data-table">
            <tr>
                <td><span class="label">Admission exam :</span> <span class="value">{{ $application->exam?->name ?? '-' }}</span></td>
                <td><span class="label">Exam date :</span> <span class="value">{{ $application->exam?->exam_date?->format('d M Y') ?? '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Venue :</span> <span class="value">{{ $application->exam?->venue ?? '-' }}</span></td>
                <td><span class="label">Reporting time :</span> <span class="value">{{ $application->exam?->reporting_time ?? '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Application number :</span> <span class="value">{{ $application->application_number }}</span></td>
                <td><span class="label">Applied class :</span> <span class="value">{{ $application->schoolClass?->name_en ?? '-' }}</span></td>
            </tr>
        </table>

        <div class="title">Parents and guardian ({{ $guardianLabel }})</div>
        <table class="data-table">
            <tr>
                <td><span class="label">Father / phone :</span> <span class="value">{{ $data['father_name'] ?? $application->father_name ?? '-' }} / {{ $data['father_phone'] ?? $application->father_phone ?? '-' }}</span></td>
                <td><span class="label">Mother / phone :</span> <span class="value">{{ $data['mother_name'] ?? $application->mother_name ?? '-' }} / {{ $data['mother_phone'] ?? $application->mother_phone ?? '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Selected guardian :</span> <span class="value">{{ $guardianLabel === 'Other' ? ($data['guardian_name'] ?? $application->guardian_name ?? '-') : ($data[($guardianType === 1 ? 'father' : 'mother') . '_name'] ?? $application->{($guardianType === 1 ? 'father' : 'mother') . '_name'} ?? '-') }}</span></td>
                <td><span class="label">Guardian contact :</span> <span class="value">{{ $guardianLabel === 'Other' ? ($data['guardian_phone'] ?? $application->guardian_phone ?? '-') : ($data[($guardianType === 1 ? 'father' : 'mother') . '_phone'] ?? $application->{($guardianType === 1 ? 'father' : 'mother') . '_phone'} ?? '-') }}</span></td>
            </tr>
        </table>

        <div class="title">Address and payment</div>
        <table class="data-table">
            <tr>
                <td><span class="label">Present address :</span> <span class="value">{{ $data['present_address'] ?? $application->present_address ?? '-' }}</span></td>
                <td><span class="label">Permanent address :</span> <span class="value">{{ $data['permanent_address'] ?? $application->permanent_address ?? '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Payment status :</span> <span class="value">{{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}</span></td>
                <td><span class="label">Payment amount / reference :</span> <span class="value">{{ $application->payment?->amount !== null ? number_format((float) $application->payment->amount, 2) : '-' }} / {{ $application->payment?->payment_reference ?? '-' }}</span></td>
            </tr>
        </table>

        <div class="title">Applicant information</div>
        <table class="data-table">
            <tr>
                <td><span class="label">Student name :</span> <span class="value">{{ $data['full_name_en'] ?? $application->full_name_en ?? '-' }}</span></td>
                <td><span class="label">Name in Bangla :</span> <span class="value">{{ $data['full_name_bn'] ?? $application->full_name_bn ?? '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Date of birth :</span> <span class="value">{{ $data['date_of_birth'] ?? '-' }}</span></td>
                <td><span class="label">Gender :</span> <span class="value">{{ $gender }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Religion :</span> <span class="value">{{ $religion }}</span></td>
                <td><span class="label">Blood group :</span> <span class="value">{{ $bloodGroup }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Father phone :</span> <span class="value">{{ $data['father_phone'] ?? $application->father_phone ?? '-' }}</span></td>
                <td><span class="label">Mother phone :</span> <span class="value">{{ $data['mother_phone'] ?? $application->mother_phone ?? '-' }}</span></td>
            </tr>
        </table>

        @if($application->exam?->instructions)
            <div class="title">Instruction</div>
            <div class="instructions">{!! strip_tags($application->exam->instructions, '<p><br><strong><b><em><i><u><ul><ol><li><blockquote>') !!}</div>
        @endif
        </div>

        <div class="cut-mark"><span>CUT HERE - ADMIT CARD</span></div>

        <div class="admit-card-section">
        <div class="student-header">
            <table class="student-header-table">
                <tr>
                    <td class="logo-cell">
                        @if($logoPath)
                            <img class="logo" src="{{ $logoPath }}" alt="Logo">
                        @endif
                    </td>
                    <td class="student-header-copy">
                        <h1>{{ $school->name ?? config('app.name') }}</h1>
                        <p>Admission Examination Admit Card</p>
                        <p>Application No: <b>{{ $application->application_number }}</b></p>
                        <p>{{ $school->address ?? '' }}</p>
                    </td>
                    <td class="logo-cell"></td>
                </tr>
            </table>
        </div>

        <div class="candidate-strip">
            <h2>Admit Card · Student Copy</h2>
            <table class="candidate-table">
                <tr>
                    <td><span class="label">Student name :</span> <span class="value">{{ $data['full_name_en'] ?? $application->full_name_en ?? '-' }}</span></td>
                    <td><span class="label">Roll number :</span> <span class="value">{{ $application->admitCard?->roll_number ?? '-' }}</span></td>
                    <td class="photo-cell" rowspan="4">
                        @if($imagePath)
                            <img src="{{ $imagePath }}" alt="Student photo" style="height: 70px; width: 54px; object-fit: cover;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span class="label">Applied class :</span> <span class="value">{{ $application->schoolClass?->name_en ?? '-' }}</span></td>
                    <td><span class="label">Unique ID :</span> <span class="value">{{ $application->admitCard?->candidate_id ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td><span class="label">Exam date :</span> <span class="value">{{ $application->exam?->exam_date?->format('d M Y') ?? '-' }}</span></td>
                    <td><span class="label">Admit card number :</span> <span class="value">{{ $application->admitCard?->admit_card_number ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td><span class="label">Venue :</span> <span class="value">{{ $application->exam?->venue ?? '-' }}</span></td>
                    <td><span class="label">Reporting time :</span> <span class="value">{{ $application->exam?->reporting_time ?? '-' }}</span></td>
                </tr>
            </table>
            <div class="instructions" style="margin-top: 8px;">{!! strip_tags($application->exam?->instructions ?? 'Bring this admit card and arrive before the reporting time.', '<p><br><strong><b><em><i><u><ul><ol><li><blockquote>') !!}</div>
        </div>
        </div>
    </div>
</body>
</html>
