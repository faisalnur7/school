@extends('layouts.master')

@section('contents')
<div class="col-12 progress-report-page">
    <div class="card card-outline card-primary mb-3 progress-report-filter-card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <a href="{{ route('results.hub') }}" class="btn btn-sm btn-secondary mr-3">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-file-invoice mr-2"></i>Terminal Report
                    </h4>
                </div>
                <div class="d-flex gap-2 flex-wrap" role="group" aria-label="Terminal report actions">
                    <a href="{{ route('result.progress-report.template-settings.edit') }}" class="btn btn-outline-light">
                        <i class="fas fa-sliders-h mr-1"></i>Template Settings
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body progress-report-filter-card-body">
            @include('pages.progress-report._filter')
        </div>
    </div>

    <div class="card card-body text-center text-muted py-5">
        <i class="fas fa-filter fa-3x mb-3 text-success"></i>
        <p class="mb-0">Choose the filters above to view the report.</p>
    </div>
</div>
@endsection
