<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Free Studentships Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #f5f5f5;
        }
        table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Free Studentships Report</h2>
        @if($session)
            <p><strong>Academic Session:</strong> {{ $session->name_en }}</p>
        @endif
        <p><strong>Generated on:</strong> {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

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
                <th>Session</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($freeStudentships as $i => $freeStudentship)
                @php $ai = $freeStudentship->studentAcademicInformation; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $freeStudentship->student->student_cid ?? '—' }}</td>
                    <td>{{ $ai?->roll ?? '—' }}</td>
                    <td>{{ $freeStudentship->student->full_name_en ?? '—' }}</td>
                    <td>{{ $ai?->schoolClass?->name_en ?? '—' }}</td>
                    <td>{{ $ai?->section?->name_en ?? '—' }}</td>
                    <td>{{ $ai?->group?->name_en ?? '—' }}</td>
                    <td>{{ $freeStudentship->feeCategory->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $freeStudentship->type === 'fixed' ? 'info' : 'warning' }}">
                            {{ ucfirst($freeStudentship->type) }}
                        </span>
                    </td>
                    <td>
                        @if ($freeStudentship->type === 'fixed')
                            ৳{{ number_format($freeStudentship->amount, 2) }}
                        @else
                            {{ $freeStudentship->percentage }}%
                        @endif
                    </td>
                    <td>{{ $freeStudentship->academicSession->name_en ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $freeStudentship->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($freeStudentship->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; color: #999;">No free studentships found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>