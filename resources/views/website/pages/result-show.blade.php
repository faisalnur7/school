@extends('website.layouts.app')

@section('title', 'Result - {{ $student->full_name_en }}')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Student Result</h3>
                    <div>
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm me-2">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('website.result.check') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Search
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Student Information</h5>
                        <p><strong>Name:</strong> {{ $student->full_name_en }} ({{ $student->full_name_bn }})</p>
                        <p><strong>Student CID:</strong> {{ $student->student_cid }}</p>
                        <p><strong>Session:</strong> {{ $exams->first()->academicSession->name ?? 'N/A' }}</p>
                        <p><strong>Exam Type:</strong> {{ ucfirst($data['exam_type']) }} Exam</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($exams->isEmpty())
                        <div class="alert alert-info">No exams found for the selected session and exam type.</div>
                    @else
                        <h5>Exams</h5>
                        <div class="list-group">
                            @foreach($exams as $exam)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $exam->name }}</h6>
                                        <small>{{ ucfirst($data['exam_type']) }} Exam</small>
                                    </div>
                                    <p class="mb-1">Session: {{ $exam->academicSession->name }}</p>

                                    @if(isset($marksByExam[$exam->id]) && $marksByExam[$exam->id]->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Marks Obtained</th>
                                                        <th>Total Marks</th>
                                                        <th>Percentage</th>
                                                        <th>Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($marksByExam[$exam->id] as $mark)
                                                        <tr>
                                                            <td>{{ $mark->subject->name }}</td>
                                                            <td>{{ $mark->total }}</td>
                                                            <td>{{ $mark->subject->total_marks }}</td>
                                                            <td>
                                                                @php
                                                                    $percentage = ($mark->total / $mark->subject->total_marks) * 100;
                                                                    echo number_format($percentage, 2) . '%';
                                                                @endphp
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $percentage = ($mark->total / $mark->subject->total_marks) * 100;
                                                                    if ($percentage >= 80) echo 'A+';
                                                                    elseif ($percentage >= 70) echo 'A';
                                                                    elseif ($percentage >= 60) echo 'A-';
                                                                    elseif ($percentage >= 50) echo 'B';
                                                                    elseif ($percentage >= 40) echo 'C';
                                                                    elseif ($percentage >= 33) echo 'D';
                                                                    else echo 'F';
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No marks found for this exam.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($terminalExam && !$terminalMarks->isEmpty())
                        <div class="mt-4">
                            <h5>Final Report</h5>
                            <p>Final report for the session {{ $terminalExam->academicSession->name }} is available.</p>
                            <a href="{{ route('website.result.final-report', ['student_cid' => $student->student_cid, 'session_id' => $terminalExam->academic_session_id]) }}" class="btn btn-success btn-sm">View Final Report</a>
                        </div>
                    @elseif($terminalExam && $terminalMarks->isEmpty())
                        <div class="mt-4 alert alert-info">
                            No marks found for terminal exams for this student.
                        </div>
                    @else
                        <div class="mt-4 alert alert-info">
                            No final report available for this session.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print, .btn, .card-header .btn-group {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            border-bottom: 1px solid #dee2e6 !important;
            margin-bottom: 1rem !important;
        }
        .table {
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Add Font Awesome for print icon if not already loaded
    if (!document.querySelector('link[href*="font-awesome"]') && !document.querySelector('link[href*="fontawesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(link);
    }
</script>
@endpush