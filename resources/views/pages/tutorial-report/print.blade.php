<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 11px; color: #1e293b; }

    .school-header-wrap { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; margin-bottom: 10px; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; vertical-align: middle; line-height: 50px; overflow: hidden; background: #fff; }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #334155; }
    .school-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 1px; }
    .school-line { font-size: 10px; color: #334155; margin-top: 2px; }

    .pdf-header { background: #1e293b; color: #fff; padding: 10px 14px; margin-bottom: 12px; }
    .pdf-header h1 { font-size: 15px; font-weight: 700; margin: 0; }
    .pdf-header .meta { font-size: 10px; color: #94a3b8; margin-top: 2px; }

    .student-card { border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 14px; overflow: hidden; }
    .student-card__header { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 9px 12px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    .student-card__name { font-size: 12px; font-weight: 700; color: #0f172a; }
    .student-card__meta { color: #475569; font-size: 10px; margin-top: 2px; }
    .student-card__total { font-size: 11px; font-weight: 700; color: #0f172a; white-space: nowrap; }

    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    th { background: #f1f5f9; color: #334155; padding: 6px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
    td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
    .text-center { text-align: center; }
    .muted { color: #64748b; }
</style>
</head>
<body>
@include('partials.report-pdf-header')
<div class="pdf-header">
    <h1>Tutorial Exam Report</h1>
    <div class="meta">{{ $exam->name }} &nbsp;|&nbsp; {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

@foreach($studentsData as $data)
    @php $student = $data['student']; @endphp
    <div class="student-card">
        <div class="student-card__header">
            <div>
                <div class="student-card__name">{{ $student->full_name_en }}</div>
                <div class="student-card__meta">ID: {{ $student->student_cid ?? $student->id }}</div>
            </div>
            <div class="student-card__total">Total Obtained: {{ number_format($data['total_obtained'], 1) }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th class="text-center" style="width:160px;">Obtained</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['rows'] as $r)
                    <tr>
                        <td>{{ $r['subject_name'] }}</td>
                        <td class="text-center">{{ $r['is_absent'] ? 'AB' : number_format($r['obtained'], 1) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="muted" style="text-align:center;">No marks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endforeach
</body>
</html>
