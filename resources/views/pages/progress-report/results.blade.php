@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                 style="width:52px;height:52px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);flex-shrink:0">
                <i class="fas fa-file-invoice text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="mb-0 font-weight-bold">Terminal Exam Report</h4>
                <small class="text-muted">{{ $exam->name }} &mdash; {{ $exam->academicSession->name_en ?? $exam->academicSession->name_bn ?? '' }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('result.progress-report.pdf', $filters) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
            <button onclick="window.print()" class="btn btn-info btn-sm no-print">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('result.progress-report.index') }}" class="btn btn-secondary btn-sm no-print">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <div class="alert alert-success d-flex align-items-center">
        <i class="fas fa-users mr-2"></i>
        Showing <strong class="mx-1">{{ count($studentsData) }}</strong> student report(s)
    </div>

    @foreach($studentsData as $data)
    @php
        $student     = $data['student'];
        $info        = $data['academicInfo'];
        $subjectRows = $data['subjectRows'];
        $summary     = $data['summary'];
    @endphp
    <div class="card card-outline mb-4" style="border-top:3px solid #1a6b3c">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $student->full_name_en }}</strong>
                @if($student->full_name_bn)
                <small class="text-muted ml-2">{{ $student->full_name_bn }}</small>
                @endif
            </div>
            <div>
                <span class="badge badge-pill" style="background:#1a6b3c;color:#fff;font-size:13px">
                    GPA: {{ number_format($summary['gpa'], 2) }} &mdash; {{ $summary['grade'] }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            {{-- Student Info --}}
            <div class="px-3 pt-3 pb-2 border-bottom">
                <div class="row text-sm">
                    <div class="col-6 col-md-3"><span class="text-muted">Class:</span> <strong>{{ $info?->schoolClass?->name_en ?? '—' }}</strong></div>
                    <div class="col-6 col-md-3"><span class="text-muted">Section:</span> <strong>{{ $info?->section?->name_en ?? '—' }}</strong></div>
                    <div class="col-6 col-md-3"><span class="text-muted">Roll:</span> <strong>{{ $info?->roll ?? '—' }}</strong></div>
                    <div class="col-6 col-md-3"><span class="text-muted">Student ID:</span> <strong>{{ $student->student_cid ?? $student->id }}</strong></div>
                </div>
            </div>

            {{-- Marks Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background:#1a6b3c;color:#fff">
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th class="text-center">Full</th>
                            <th class="text-center">Obtained</th>
                            <th class="text-center">Highest</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectRows as $i => $row)
                        <tr class="{{ ($row['paper_fail'] ?? false) ? 'table-danger' : (($i % 2 == 1) ? 'table-light' : '') }}">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                {{ $row['subject_name'] }}
                                @if($row['paper_fail'] ?? false)
                                <span class="badge badge-danger ml-1">⚠ Paper Fail</span>
                                @endif
                                @if(!empty($row['papers']))
                                <div class="pl-3 mt-1">
                                    @foreach($row['papers'] as $paper)
                                    <small class="d-block text-muted">
                                        ├ {{ $paper['subject_name'] }}:
                                        @if($paper['is_absent']) <em>Absent</em>
                                        @else {{ number_format($paper['obtained'], 2) }}/{{ number_format($paper['full_marks'], 0) }} ({{ $paper['grade'] }})
                                        @endif
                                    </small>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($row['full_marks'], 0) }}</td>
                            <td class="text-center">
                                @if($row['is_absent']) <em class="text-muted">Absent</em>
                                @elseif(is_null($row['obtained'])) <em class="text-muted">—</em>
                                @else {{ number_format($row['obtained'], 2) }}
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($row['highest'], 2) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $row['grade'] === 'F' || $row['grade'] === 'AB' ? 'danger' : ($row['gpa'] >= 4 ? 'success' : ($row['gpa'] >= 3 ? 'primary' : 'warning')) }}">
                                    {{ $row['grade'] }}
                                </span>
                            </td>
                            <td class="text-center">{{ is_null($row['gpa']) ? '—' : number_format($row['gpa'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#f4f4f4;font-weight:bold">
                        <tr>
                            <td colspan="2" class="text-right">Total</td>
                            <td class="text-center">{{ number_format($summary['fullMarks'], 0) }}</td>
                            <td class="text-center">{{ number_format($summary['obtained'], 2) }}</td>
                            <td></td>
                            <td class="text-center">{{ $summary['grade'] }}</td>
                            <td class="text-center">{{ number_format($summary['gpa'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-right">
                                Percentage: <strong>{{ number_format($summary['percentage'], 2) }}%</strong>
                                &nbsp;|&nbsp;
                                Attendance: <strong>{{ $data['attendancePresent'] }}/{{ $data['attendanceTotal'] }} days</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
