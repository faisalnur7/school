@extends('website.layouts.app')

@section('title', 'Final Report - {{ $student->full_name_en }}')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Final Report</h3>
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
                        @if($terminalExams->first())
                            <p><strong>Session:</strong> {{ $terminalExams->first()->academicSession->name }}</p>
                        @endif
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($terminalExams->isEmpty())
                        <div class="alert alert-info">No terminal exams found for the selected session.</div>
                    @elseif($terminalMarks->isEmpty())
                        <div class="alert alert-info">No marks found for terminal exams for this student.</div>
                    @else
                        @foreach($terminalExams as $exam)
                            <div class="mb-4">
                                <h6>{{ $exam->name }}</h6>
                                <p class="text-muted">Terminal Exam</p>
                                
                                @php
                                    $marksForExam = $terminalMarks->where('exam_id', $exam->id);
                                @endphp
                                
                                @if($marksForExam->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Marks Obtained</th>
                                                    <th>Total Marks</th>
                                                    <th>Percentage</th>
                                                    <th>Grade</th>
                                                    <th>Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($marksForExam as $mark)
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
                                                        <td>
                                                            @php
                                                                $passMark = $mark->subject->total_marks * 0.33; // 33% pass mark
                                                                echo ($mark->total >= $passMark) ? 'Pass' : 'Fail';
                                                            @endphp
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4"><strong>Total</strong></td>
                                                    <td>
                                                        @php
                                                            $obtainedTotal = $marksForExam->sum('total');
                                                            $totalTotal = $marksForExam->sum(function($mark) {
                                                                return $mark->subject->total_marks;
                                                            });
                                                            echo $obtainedTotal;
                                                        @endphp>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $obtainedTotal = $marksForExam->sum('total');
                                                            $totalTotal = $marksForExam->sum(function($mark) {
                                                                return $mark->subject->total_marks;
                                                            });
                                                            $percentage = ($totalTotal > 0) ? ($obtainedTotal / $totalTotal) * 100 : 0;
                                                            echo number_format($percentage, 2) . '%';
                                                        @endphp>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $obtainedTotal = $marksForExam->sum('total');
                                                            $totalTotal = $marksForExam->sum(function($mark) {
                                                                return $mark->subject->total_marks;
                                                            });
                                                            $percentage = ($totalTotal > 0) ? ($obtainedTotal / $totalTotal) * 100 : 0;
                                                            if ($percentage >= 80) echo 'A+';
                                                            elseif ($percentage >= 70) echo 'A';
                                                            elseif ($percentage >= 60) echo 'A-';
                                                            elseif ($percentage >= 50) echo 'B';
                                                            elseif ($percentage >= 40) echo 'C';
                                                            elseif ($percentage >= 33) echo 'D';
                                                            else echo 'F';
                                                        @endphp>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $allPassed = $marksForExam->every(function($mark) {
                                                                $passMark = $mark->subject->total_marks * 0.33;
                                                                return $mark->total >= $passMark;
                                                            });
                                                            echo $allPassed ? 'Pass' : 'Fail';
                                                        @endphp>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No marks found for this exam.</p>
                                @endif
                            </div>
                        @endforeach
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