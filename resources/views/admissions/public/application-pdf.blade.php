@php
    $data = $application->applicant_data ?? [];
    $guardianType = (int) ($data['guardian_type'] ?? $application->guardian_type ?? 1);
    $guardianLabel = [1 => 'Father', 2 => 'Mother', 3 => 'Other'][$guardianType] ?? 'Father';
    $prefix = $guardianType === 1 ? 'father' : 'mother';
    $gender = \App\Models\Student::GENDERS[(int) ($data['gender'] ?? $application->gender)] ?? '-';
    $religion = \App\Models\Student::RELIGIONS[(int) ($data['religion'] ?? $application->religion)] ?? '-';
    $bloodGroup = \App\Models\Student::BLOOD_GROUPS[(int) ($data['blood_group'] ?? $application->blood_group)] ?? '-';
    $school = \App\Models\SchoolSetting::current();
    $logoPath = !empty($school->logo) && file_exists(public_path($school->logo))
        ? public_path($school->logo)
        : null;
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            color: #172033;
            font-family: Arial;
            font-size: 10px;
        }

        .wrap {
            border: 1px solid #cbd5e1;
            padding: 22px;
        }

        .head {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 14px;
        }

        .head-table {
            border-collapse: collapse;
            width: 100%;
        }

        .head-logo {
            height: 58px;
            object-fit: contain;
            width: 58px;
        }

        .head-copy {
            text-align: center;
        }

        .head h1 {
            color: #0f766e;
            font-size: 20px;
            margin: 0;
        }

        .head p {
            font-size: 12px;
            margin: 4px 0;
        }

        .address {
            color: #475569;
            font-size: 12px;
            margin: 5px auto 0;
            max-width: 420px;
        }

        .section {
            border: 1px solid #dbe3ee;
            margin-top: 16px;
            padding: 10px;
        }

        .section h2 {
            border-bottom: 1px solid #dbe3ee;
            color: #0f766e;
            font-size: 12px;
            margin: 0 0 8px;
            padding: 0 0 6px;
        }

        .grid {
            border-collapse: collapse;
            width: 100%;
        }

        .grid td {
            border-bottom: 1px solid #edf1f5;
            padding: 8px;
            vertical-align: top;
            width: 50%;
        }

        .label {
            color: #000;
            display: inline;
            font-size: 14px;
            font-weight: bold;
            line-height: 13px;
            padding-right: 14px;
            white-space: nowrap;
        }

        .label:after {
            content: ':';
        }

        .value {
            display: inline;
            font-size: 14px;
            font-weight: 400;
            line-height: 13px;
            margin-left: 10px;
        }

        .value ul {
            list-style-type: disc;
            margin: 3px 0;
            padding-left: 18px;
        }

        .value ol {
            list-style-type: decimal;
            margin: 3px 0;
            padding-left: 18px;
        }

        .value li {
            display: list-item;
        }

        .status {
            color: #0f766e;
            display: inline;
            font-size: 10px;
            font-weight: bold;
            line-height: 13px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <table class="head-table">
                <tr>
                    @if($logoPath)
                        <td style="width: 70px">
                            <img class="head-logo" src="{{ $logoPath }}" alt="Logo">
                        </td>
                    @else
                        <td style="width: 70px"></td>
                    @endif
                    <td class="head-copy">
                        <h1>{{ $school->name ?? config('app.name') }}</h1>
                        <p>Admission Application</p>
                        <p>Application No: <b>{{ $application->application_number }}</b></p>
                        <p class="address">{{ $school->address ?? '' }}</p>
                    </td>
                    <td style="width: 70px"></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Application details</h2>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Admission exam</span>
                        <span class="value">{{ $application->exam?->name ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Exam date</span>
                        <span class="value">{{ $application->exam?->exam_date?->format('d M Y') ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Applied class</span>
                        <span class="value">{{ $application->schoolClass?->name_en ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Application status</span>
                        <span class="value">{{ ucfirst(str_replace('_', ' ', $application->application_status ?: $application->status)) }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Basic information</h2>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Full name</span>
                        <span class="value">{{ $data['full_name_en'] ?? $application->full_name_en ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Name in Bangla</span>
                        <span class="value">{{ $data['full_name_bn'] ?? $application->full_name_bn ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Date of birth</span>
                        <span class="value">{{ $data['date_of_birth'] ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Birth certificate number</span>
                        <span class="value">{{ $data['birth_certificate_number'] ?? $application->birth_certificate_number ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Gender</span>
                        <span class="value">{{ $gender }}</span>
                    </td>
                    <td>
                        <span class="label">Religion / Blood group</span>
                        <span class="value">{{ $religion }} / {{ $bloodGroup }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Parents and guardian ({{ $guardianLabel }})</h2>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Father / phone</span>
                        <span class="value">{{ $data['father_name'] ?? $application->father_name ?? '-' }} / {{ $data['father_phone'] ?? $application->father_phone ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Mother / phone</span>
                        <span class="value">{{ $data['mother_name'] ?? $application->mother_name ?? '-' }} / {{ $data['mother_phone'] ?? $application->mother_phone ?? '-' }}</span>
                    </td>
                </tr>
                @if($guardianType === 1 || $guardianType === 2)
                    <tr>
                        <td>
                            <span class="label">Selected guardian</span>
                            <span class="value">{{ $data[$prefix . '_name'] ?? $application->{$prefix . '_name'} ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="label">Guardian contact</span>
                            <span class="value">{{ $data[$prefix . '_phone'] ?? $application->{$prefix . '_phone'} ?? '-' }} / {{ $data[$prefix . '_email'] ?? $application->{$prefix . '_email'} ?? '-' }}</span>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>
                            <span class="label">Guardian name / relationship</span>
                            <span class="value">{{ $data['guardian_name'] ?? $application->guardian_name ?? '-' }} / {{ $data['guardian_relation'] ?? $application->guardian_relation ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="label">Guardian phone / email</span>
                            <span class="value">{{ $data['guardian_phone'] ?? $application->guardian_phone ?? '-' }} / {{ $data['guardian_email'] ?? $application->guardian_email ?? '-' }}</span>
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <h2>Address and payment</h2>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Present address</span>
                        <span class="value">{{ $data['present_address'] ?? $application->present_address ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Permanent address</span>
                        <span class="value">{{ $data['permanent_address'] ?? $application->permanent_address ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Payment status</span>
                        <span class="status">{{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}</span>
                    </td>
                    <td>
                        <span class="label">Payment amount / reference</span>
                        <span class="value">{{ $application->payment?->amount !== null ? number_format((float) $application->payment->amount, 2) : '-' }} / {{ $application->payment?->payment_reference ?? '-' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($application->exam?->instructions)
            <div class="section">
                <h2>Instruction</h2>
                <div class="value">{!! strip_tags($application->exam->instructions, '<p><br><strong><b><em><i><u><ul><ol><li><blockquote>') !!}</div>
            </div>
        @endif
    </div>
</body>
</html>
