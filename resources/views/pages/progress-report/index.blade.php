@extends('layouts.master')

@section('contents')
<div class="col-12 progress-report-page">
    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
             style="width:52px;height:52px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);flex-shrink:0">
            <i class="fas fa-file-invoice text-white fa-lg"></i>
        </div>
        <div>
            <h4 class="mb-0 font-weight-bold">Terminal Report</h4>
            <small class="text-muted">Generate student progress reports by terminal exam, class, and section</small>
        </div>
    </div>

    @include('pages.progress-report._filter')

    <div class="card card-body text-center text-muted py-5">
        <i class="fas fa-filter fa-3x mb-3 text-success"></i>
        <p class="mb-0">Choose the filters above to view the report.</p>
    </div>
</div>
@endsection
