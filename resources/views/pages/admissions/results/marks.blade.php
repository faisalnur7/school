@extends('layouts.master')

@section('contents')
<style>
    .admission-marks-page { color: #172033; }
    .admission-marks-page .marks-hero { background: linear-gradient(120deg, #10233d, #155e75 65%, #0f766e); border-radius: 16px; color: #fff; overflow: hidden; position: relative; }
    .admission-marks-page .marks-hero::after { background: rgba(255,255,255,.08); border-radius: 50%; content: ''; height: 230px; position: absolute; right: -45px; top: -145px; width: 230px; }
    .admission-marks-page .marks-hero > * { position: relative; z-index: 1; }
    .admission-marks-page .summary-card { border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.07); }
    .admission-marks-page .summary-label { color: #718096; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .admission-marks-page .summary-value { color: #172033; font-size: 1.55rem; font-weight: 800; line-height: 1.1; }
    .admission-marks-page .marks-panel { border: 1px solid #e5eaf1; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.05); overflow: hidden; }
    .admission-marks-page .marks-panel .card-header { background: #fff; }
    .admission-marks-page .marks-panel .table thead th { background: #f7f9fc; border-bottom: 1px solid #e5eaf1; color: #667085; font-size: .7rem; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
    .admission-marks-page .marks-panel .table tbody td { border-top: 1px solid #edf1f5; padding: 13px 12px; vertical-align: middle; }
    .admission-marks-page .application-number { color: #0f6170; font-weight: 800; }
    .admission-marks-page .marks-input { border-color: #cbd8e6; border-radius: 8px; font-weight: 700; width: 110px; }
    .admission-marks-page .marks-input:focus { border-color: #0f766e; box-shadow: 0 0 0 .2rem rgba(15,118,110,.12); }
    .admission-marks-page .status-badge { border-radius: 999px; font-size: .7rem; font-weight: 700; padding: 5px 9px; }
    .admission-marks-page .status-passed { background: #dcfce7; color: #166534; }
    .admission-marks-page .status-failed { background: #fee2e2; color: #991b1b; }
    .admission-marks-page .status-pending { background: #f1f5f9; color: #64748b; }
</style>

@php
    $enteredCount = $applications->whereNotNull('total_marks')->count();
    $pendingCount = $applications->count() - $enteredCount;
@endphp

<div class="container-fluid py-3 admission-marks-page">
    <div class="marks-hero d-flex flex-wrap justify-content-between align-items-center mb-3 p-4">
        <div>
            <div class="small font-weight-bold text-uppercase" style="letter-spacing:.14em;opacity:.72;">Admission exam results</div>
            <h2 class="mb-1 mt-1 text-white">{{ $exam->name }}</h2>
            <p class="mb-0" style="opacity:.78;">Enter total marks by class. Results are calculated from the configured pass mark.</p>
        </div>
        @if($selectedClassId)
            <a href="{{ route('admissions.marks', $exam) }}" class="btn btn-light mt-3 mt-md-0">
                <i class="fas fa-th-large mr-1"></i> All classes
            </a>
        @endif
    </div>

    @if(! $selectedClassId)
        <div class="row">
            @forelse($classSettings as $setting)
                @php $stats = $classStats->get($setting->school_class_id); @endphp
                <div class="col-sm-6 col-xl-4 mb-4">
                    <a href="{{ route('admissions.marks', ['exam' => $exam, 'school_class_id' => $setting->school_class_id]) }}" class="card h-100 border-0 shadow-sm text-decoration-none admission-class-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-primary">Class</span>
                                <i class="fas fa-arrow-right text-primary"></i>
                            </div>
                            <h4 class="text-dark mb-1">{{ $setting->schoolClass?->name_en ?? 'Unnamed class' }}</h4>
                            <p class="text-muted mb-4">Total marks: <strong>{{ number_format((float) ($setting->total_mark ?? 100), 0) }}</strong> · Pass mark: <strong>{{ number_format((float) $setting->pass_mark, 0) }}</strong></p>
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span class="text-muted"><strong class="text-dark">{{ $stats?->applicants_count ?? 0 }}</strong> applicants</span>
                                <span class="text-muted"><strong class="text-dark">{{ $stats?->marked_count ?? 0 }}</strong> marked</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-warning">No classes are configured for this admission exam.</div></div>
            @endforelse
        </div>
    @else
        <div class="row mb-3">
            <div class="col-sm-4 mb-3 mb-sm-0"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Paid applicants</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value">{{ $applications->count() }}</span><i class="fas fa-users text-info"></i></div></div></div></div>
            <div class="col-sm-4 mb-3 mb-sm-0"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Marks entered</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value text-primary">{{ $enteredCount }}</span><i class="fas fa-pen text-primary"></i></div></div></div></div>
            <div class="col-sm-4"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Not entered</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value text-warning">{{ $pendingCount }}</span><i class="fas fa-clock text-warning"></i></div></div></div></div>
        </div>

        <div class="card marks-panel">
            <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <div class="d-flex align-items-center mb-1"><span class="badge badge-primary mr-2">Class</span><h4 class="mb-0">{{ $selectedClass?->name_en }}</h4></div>
                    <small class="text-muted">Total marks: <strong>{{ number_format((float) ($selectedSetting?->total_mark ?? 100), 0) }}</strong> · Pass mark: <strong>{{ number_format((float) $selectedSetting?->pass_mark, 0) }}</strong> · {{ $applications->count() }} paid applicants</small>
                </div>
                <div class="mt-2 mt-md-0 ml-auto">
                    <a href="{{ route('admissions.marks', $exam) }}" class="btn btn-sm btn-outline-primary mr-1">Change class</a>
                    <button form="batchMarksForm" type="submit" class="btn btn-sm btn-primary px-3" @disabled($applications->isEmpty())>
                        <i class="fas fa-save mr-1"></i> Save all marks
                    </button>
                </div>
            </div>
            <form id="batchMarksForm" method="POST" action="{{ route('admissions.marks.batch', $exam) }}">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Application</th><th>Applicant</th><th>Total marks</th><th>Result</th><th class="text-right">Pass mark</th></tr></thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td><span class="application-number">{{ $application->application_number }}</span></td>
                                <td><strong>{{ $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-' }}</strong></td>
                                <td>
                                    <input type="number" min="0" max="{{ (int) ($selectedSetting?->total_mark ?? 100) }}" step="1" name="marks[{{ $application->id }}]" value="{{ $application->total_marks !== null ? (int) $application->total_marks : '' }}" class="form-control form-control-sm marks-input" aria-label="Total marks for {{ $application->application_number }}">
                                </td>
                                <td>
                                    @if($application->result_status === 'passed') <span class="status-badge status-passed">Passed</span>
                                    @elseif($application->result_status === 'failed') <span class="status-badge status-failed">Failed</span>
                                    @else <span class="status-badge status-pending">Not entered</span> @endif
                                </td>
                                <td class="text-right text-muted">{{ $application->pass_mark_snapshot ? number_format((float) $application->pass_mark_snapshot, 0) : number_format((float) $selectedSetting?->pass_mark, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">No paid applications are available for this class.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </form>
        </div>
    @endif
</div>

<style>
    .admission-class-card { border-radius: 14px; transition: transform .18s ease, box-shadow .18s ease; }
    .admission-class-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(30, 41, 59, .12) !important; }
</style>
@endsection
