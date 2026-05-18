@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-file-alt mr-2"></i>Yearly Final Report
                </h4>
                <a href="{{ route('results.hub') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Hub
                </a>
            </div>
        </div>

        <div class="card-body p-3">
            <form method="POST" action="{{ route('result.yearly-final-report.show') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Academic Session</label>
                            <select name="session_id" class="form-control" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ optional($filters)['session_id'] == $session->id ? 'selected' : '' }}>
                                    {{ $session->name_en ?? $session->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="class_id" id="classSelect" class="form-control" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ optional($filters)['class_id'] == $class->id ? 'selected' : '' }}>
                                    {{ $class->name_en ?? $class->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control" id="sectionSelect">
                                <option value="">All Sections</option>
                                @foreach(App\Models\Section::where('school_class_id', optional($filters)['class_id'])->get() as $section)
                                <option value="{{ $section->id }}" {{ optional($filters)['section_id'] == $section->id ? 'selected' : '' }}>
                                    {{ $section->name_en ?? $section->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control"
                                value="{{ optional($filters)['student_id'] ?? '' }}" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i>Generate Report
                    </button>
                    @if(!empty($rows))
                    <a href="{{ route('result.yearly-final-report.pdf', request()->all()) }}" class="btn btn-secondary btn-sm" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i>Download PDF
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(!empty($rows))
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Pair 1 Total</th>
                            <th>Pair 1 Weighted ({{ $pairWeights[1] ?? 0 }}%)</th>
                            <th>Pair 2 Total</th>
                            <th>Pair 2 Weighted ({{ $pairWeights[2] ?? 0 }}%)</th>
                            <th>Pair 3 Total</th>
                            <th>Pair 3 Weighted ({{ $pairWeights[3] ?? 0 }}%)</th>
                            <th>Grand Total</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['student']->full_name_en ?? $row['student']->full_name_bn }}</td>
                            <td>{{ $row['totals'][1]['total'] ?? 0 }}</td>
                            <td>{{ $row['totals'][1]['weighted'] ?? 0 }}</td>
                            <td>{{ $row['totals'][2]['total'] ?? 0 }}</td>
                            <td>{{ $row['totals'][2]['weighted'] ?? 0 }}</td>
                            <td>{{ $row['totals'][3]['total'] ?? 0 }}</td>
                            <td>{{ $row['totals'][3]['weighted'] ?? 0 }}</td>
                            <td>{{ $row['grand_total'] }}</td>
                            <td>{{ $row['position'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <strong>Highest Grand Total:</strong> {{ $highest }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection