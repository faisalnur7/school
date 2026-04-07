<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 5px 7px; text-align: left; }
    td { padding: 4px 7px; border-bottom: 1px solid #ddd; }
    tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .badge-success { color: #155724; background: #d4edda; padding: 2px 6px; border-radius: 3px; }
    .badge-secondary { color: #383d41; background: #e2e3e5; padding: 2px 6px; border-radius: 3px; }
    .badge-info { color: #0c5460; background: #d1ecf1; padding: 2px 6px; border-radius: 3px; }
    .badge-warning { color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 3px; }
</style>
</head>
<body>
<h2>Scholarship List</h2>
<p class="sub">
    Session: {{ $session?->name_en ?? 'All Sessions' }}
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
    &nbsp;|&nbsp; Total: {{ $scholarships->count() }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Roll</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Group</th>
            <th>Fee Category</th>
            <th>Type</th>
            <th>Value</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scholarships as $i => $s)
            @php $ai = $s->studentAcademicInformation; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->student->student_cid ?? '—' }}</td>
                <td>{{ $ai?->roll ?? '—' }}</td>
                <td><strong>{{ $s->student->full_name_en ?? '—' }}</strong></td>
                <td>{{ $ai?->schoolClass?->name_en ?? '—' }}</td>
                <td>{{ $ai?->section?->name_en ?? '—' }}</td>
                <td>{{ $ai?->group?->name_en ?? '—' }}</td>
                <td>{{ $s->feeCategory->name ?? '—' }}</td>
                <td><span class="badge-{{ $s->type === 'fixed' ? 'info' : 'warning' }}">{{ ucfirst($s->type) }}</span></td>
                <td><strong>{{ $s->type === 'fixed' ? '৳'.number_format($s->amount,2) : $s->percentage.'%' }}</strong></td>
                <td><span class="badge-{{ $s->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($s->status) }}</span></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">Total: {{ $scholarships->count() }} records</td>
            <td>{{ $scholarships->where('status','active')->count() }} active</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
