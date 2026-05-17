<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tutorial Exam Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #444; padding: 6px; }
        th { background: #eee; }
        .muted { color: #666; }
        .card { border: 1px solid #ccc; padding: 10px; margin-bottom: 14px; }
    </style>
</head>
<body>
    <h2 style="margin:0;">Tutorial Exam Report</h2>
    <p class="muted" style="margin:4px 0 14px 0;">
        {{ $exam->name }} — {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}
    </p>

    @foreach($studentsData as $data)
        @php $student = $data['student']; @endphp
        <div class="card">
            <div>
                <strong>{{ $student->full_name_en }}</strong>
                <span class="muted"> (ID: {{ $student->student_cid ?? $student->id }})</span>
                <span style="float:right;"><strong>Total Obtained:</strong> {{ number_format($data['total_obtained'], 1) }}</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th style="width:160px;">Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['rows'] as $r)
                        <tr>
                            <td>{{ $r['subject_name'] }}</td>
                            <td style="text-align:center;">{{ $r['is_absent'] ? 'AB' : number_format($r['obtained'], 1) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="muted" style="text-align:center;">No marks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>

