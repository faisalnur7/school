@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $logoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $hasLogo = $logoPath && file_exists($logoPath);
    $principalSignature = !empty($school->principal_signature)
        ? public_path($school->principal_signature)
        : null;
    $hasPrincipalSignature = $principalSignature && file_exists($principalSignature);
    $formatMark = static fn ($value) => rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.');
    $resultsPerPage = in_array((int) ($resultsPerPage ?? 2), [1, 2, 3], true) ? (int) $resultsPerPage : 2;
@endphp

<style>
    .tutorial-print-layout { color: #000; font-family: Arial, sans-serif; }
    .tutorial-print-card { width: 100%; margin: 0 0 8px; border: 1px solid #cbd6d2; border-radius: 8px; overflow: hidden; background: transparent; page-break-inside: avoid; break-inside: avoid; }
    .tutorial-print-header { width: 100%; height: 66px; box-sizing: border-box; padding: 8px 12px 0; background: transparent; border-bottom: 3px solid #c9a13b; color: #000; }
    .tutorial-print-header-table { width: 100%; height: 55px; border-collapse: collapse; table-layout: fixed; }
    .tutorial-print-header-table td { border: 0; padding: 0; vertical-align: middle; }
    .tutorial-print-header-logo-cell { width: 15%; text-align: left; }
    .tutorial-print-header-logo { display: block; width: 38px; height: 38px; object-fit: contain; }
    .tutorial-print-header-text-cell { width: 70%; text-align: center; }
    .tutorial-print-header-spacer-cell { width: 15%; }
    .tutorial-print-school { color: #000; font-family: Georgia, 'Times New Roman', serif; font-size: 18px; font-weight: 700; line-height: 1.05; white-space: nowrap; }
    .tutorial-print-exam { margin-top: 3px; color: #000; font-size: 10px; letter-spacing: 1px; line-height: 1.05; text-transform: uppercase; }
    .tutorial-print-title { margin-top: 2px; color: #000; font-family: Georgia, 'Times New Roman', serif; font-size: 14px; font-weight: 700; line-height: 1.05; }
    .tutorial-print-summary { width: 96%; margin: 8px auto 0; border: 1px solid #d5ddda; border-radius: 6px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
    .tutorial-print-summary td { width: 33.333%; padding: 5px 10px; border: 0; vertical-align: middle; }
    .tutorial-print-summary td + td { border-left: 1px solid #d5ddda; }
    .tutorial-print-summary-label { color: #000; font-size: 14px; letter-spacing: 1px; line-height: 1.05; text-transform: uppercase; }
    .tutorial-print-summary-value { margin-top: 2px; color: #000; font-size: 14px; font-weight: 700; line-height: 1.05; }
    .tutorial-print-marks-wrap { width: 96%; margin: 8px auto 0; border: 1px solid #d5ddda; border-radius: 6px; overflow: hidden; }
    .tutorial-print-student { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .tutorial-print-student th, .tutorial-print-student td { border: 1px solid #e3e8e6; padding: 4px 6px; line-height: 1.05; vertical-align: middle; }
    .tutorial-print-student thead th { height: 25px; background: transparent; color: #000; font-size: 14px; font-weight: 700; letter-spacing: .6px; text-align: left; text-transform: uppercase; white-space: nowrap; }
    .tutorial-print-student tbody tr:nth-child(even) td { background: transparent; }
    .tutorial-print-student tbody td { height: 24px; color: #000; font-size: 14px; font-weight: 700; }
    .tutorial-print-student tbody tr:last-child td { border-bottom: 0; }
    .tutorial-print-student .subject-column { width: 55%; font-weight: 600; }
    .tutorial-print-student .marks-column { width: 15%; text-align: center; }
    .tutorial-print-student .obtained { font-weight: 700; }
    .tutorial-print-total td { height: 27px; background: transparent !important; border: 1px solid #e3e8e6 !important; color: #000 !important; font-size: 14px; font-weight: 700; }
    .tutorial-print-total .total-percent { color: #000 !important; font-weight: 600; }
    .tutorial-print-footer { width: 96%; margin: 8px auto 10px; border-collapse: collapse; table-layout: fixed; }
    .tutorial-print-footer td { border: 0; padding: 0; vertical-align: bottom; }
    .tutorial-print-comment-cell { width: 58%; }
    .tutorial-print-signature-cell { width: 20%; }
    .tutorial-print-comment { display: inline-block; padding: 9px 18px; border: 0; border-radius: 999px; background: transparent; color: #000; font-size: 14px; font-weight: 700; line-height: 1.1; white-space: nowrap; }
    .tutorial-print-comment-label { display: inline-block; margin-right: 10px; padding-right: 2px; color: #000; font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .tutorial-print-signature-row td { padding-top: 20px; text-align: right; }
    .tutorial-print-signature-table { width: 145px; border-collapse: collapse; }
    .tutorial-print-signature-table td { padding: 0; border: 0; text-align: center; }
    .tutorial-print-signature-space { height: 1in; }
    .tutorial-print-signature { width: 145px; height: 100px; margin: 0; padding-top: 40px; border-top: 1.5px solid #1b2a25; text-align: center; font-size: 12px; font-weight: 500; line-height: 1.05; }
    .tutorial-print-signature img { display: block; width: 55px; max-height: 18px; object-fit: contain; margin: 0 auto 1px; }
    .tutorial-print-signature__name, .tutorial-print-signature__school, .tutorial-print-signature__details { font-size: 9px; }
    .tutorial-print-signature__name, .tutorial-print-signature__school { font-weight: 700; }

    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-card { margin-bottom: 12px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-header { height: 72px; padding: 9px 14px 0; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-header-table { height: 60px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-school { font-size: 19px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-exam { font-size: 11px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-title { font-size: 15px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-summary,
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-marks-wrap { margin-top: 10px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-summary td { padding: 7px 12px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student td { padding: 5px 7px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student thead th { height: 28px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student tbody td { height: 27px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-total td { height: 30px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-footer { margin-top: 10px; margin-bottom: 12px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-comment { padding: 10px 20px; }

    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-card { margin-bottom: 5px; border-radius: 6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header { height: 54px; padding: 5px 8px 0; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header-table { height: 45px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header-logo { width: 30px; height: 30px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-school { font-size: 15px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-exam { margin-top: 2px; font-size: 8px; letter-spacing: .6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-title { margin-top: 1px; font-size: 11px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-marks-wrap { margin-top: 5px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary td { padding: 3px 6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary-label { font-size: 10px; letter-spacing: .6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary-value { font-size: 11px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student td { padding: 2px 3px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student thead th { height: 19px; font-size: 9px; letter-spacing: .25px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student tbody td { height: 18px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-total td { height: 20px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-footer { margin-top: 5px; margin-bottom: 6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-comment { padding: 4px 8px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-comment-label { margin-right: 5px; font-size: 10px; letter-spacing: .5px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-row td { padding-top: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-table { width: 110px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-space { height: .4in; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature { width: 110px; height: 58px; padding-top: 18px; font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature img { width: 40px; max-height: 12px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__name,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__school,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__details { font-size: 7px; }
</style>

<div class="tutorial-print-layout tutorial-print-density-{{ $resultsPerPage }}">
    @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $academicInfo = $data['academicInfo'] ?? null;
            $rows = collect($data['rows'] ?? []);
            $totalFullMarks = $rows->sum(fn ($row) => (float) ($row['full_marks'] ?? 0));
            $totalObtained = $rows->filter(fn ($row) => ! $row['is_absent'])->sum(fn ($row) => (float) ($row['obtained'] ?? 0));
            $overallPercentage = $totalFullMarks > 0 ? min(100, ($totalObtained / $totalFullMarks) * 100) : 0;
        @endphp
        <div class="tutorial-print-card">
            <div class="tutorial-print-header">
                <table class="tutorial-print-header-table">
                    <tr>
                        <td class="tutorial-print-header-logo-cell">
                            @if($hasLogo)
                                <img class="tutorial-print-header-logo" src="{{ $logoPath }}" alt="School logo">
                            @endif
                        </td>
                        <td class="tutorial-print-header-text-cell">
                            <div class="tutorial-print-school">{{ $schoolName }}</div>
                            <div class="tutorial-print-exam">{{ $exam->name }} · {{ $exam->year ?? ($exam->academicSession->name_en ?? '') }}</div>
                            <div class="tutorial-print-title">Result Card</div>
                        </td>
                        <td class="tutorial-print-header-spacer-cell"></td>
                    </tr>
                </table>
            </div>

            <table class="tutorial-print-summary">
                <tr>
                    <td><div class="tutorial-print-summary-label">Student</div><div class="tutorial-print-summary-value">{{ $student->full_name_en }}</div></td>
                    <td><div class="tutorial-print-summary-label">Class</div><div class="tutorial-print-summary-value">{{ $reportContext['class'] ?? '—' }}</div></td>
                    <td><div class="tutorial-print-summary-label">ID</div><div class="tutorial-print-summary-value">{{ $student->student_cid ?? $student->id }}</div></td>
                </tr>
            </table>

            <div class="tutorial-print-marks-wrap">
                <table class="tutorial-print-student">
                    <thead><tr><th class="subject-column">Subject</th><th class="marks-column">Full Marks</th><th class="marks-column">Obtained Marks</th><th class="marks-column">Highest Marks</th></tr></thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $fullMarks = (float) ($row['full_marks'] ?? 0);
                                $obtainedMarks = $row['is_absent'] ? 0 : (float) ($row['obtained'] ?? 0);
                                $highestMarks = (float) ($row['highest'] ?? 0);
                                $obtainedPercent = $fullMarks > 0 ? min(100, max(0, ($obtainedMarks / $fullMarks) * 100)) : 0;
                            @endphp
                            <tr>
                                <td class="subject-column">{{ $row['subject_name'] }}</td>
                                <td class="marks-column">{{ $formatMark($fullMarks) }}</td>
                                <td class="marks-column obtained">{{ $row['is_absent'] ? 'AB' : $formatMark($obtainedMarks) }}</td>
                                <td class="marks-column">{{ $formatMark($highestMarks) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center;">No marks found.</td></tr>
                        @endforelse
                        <tr class="tutorial-print-total"><td style="background: transparent; color: #000;">Total</td><td class="marks-column" style="background: transparent; color: #000;">{{ $formatMark($totalFullMarks) }}</td><td class="marks-column" style="background: transparent; color: #000;">{{ $formatMark($totalObtained) }}</td><td class="marks-column total-percent" style="background: transparent; color: #000;">{{ $formatMark($overallPercentage) }}%</td></tr>
                    </tbody>
                </table>
            </div>

            <table class="tutorial-print-footer">
                <tr>
                    <td class="tutorial-print-comment-cell"><div class="tutorial-print-comment"><span class="tutorial-print-comment-label">Comments -</span><span>{{ $data['comment'] ?? '—' }}</span></div></td>
                </tr>
                <tr class="tutorial-print-signature-row">
                    <td colspan="3">
                        <table class="tutorial-print-signature-table" align="right">
                            <tr><td><div class="tutorial-print-signature-space"></div></td></tr>
                            <tr><td>
                                @if($hasPrincipalSignature)
                                    <div class="tutorial-print-signature"><img src="{{ $principalSignature }}" alt="Principal signature"><div class="tutorial-print-signature__name">{{ $school->principal_name ?? 'Principal' }}</div><div class="tutorial-print-signature__details">{{ $school->principal_designation ?? 'Principal' }}</div><div class="tutorial-print-signature__school">{{ $school->principal_school_name ?? $schoolName }}</div></div>
                                @else
                                    <div class="tutorial-print-signature" aria-label="Principal signature">Principal Signature</div>
                                @endif
                            </td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        @if($loop->iteration % $resultsPerPage === 0 && !$loop->last)
            <pagebreak />
        @endif
    @endforeach
</div>
