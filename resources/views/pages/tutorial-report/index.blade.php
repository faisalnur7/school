@extends('layouts.master')

@section('contents')
<div class="col-12 tutorial-report-page">
    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
             style="width:52px;height:52px;background:linear-gradient(135deg,#0891b2,#0e7490);flex-shrink:0">
            <i class="fas fa-clipboard-list text-white fa-lg"></i>
        </div>
        <div>
            <h4 class="mb-0 font-weight-bold text-black">Tutorial Exam Report</h4>
            <small class="text-muted">Show obtained marks for tutorial exams</small>
        </div>
    </div>

    @include('pages.tutorial-report._filter')

    <div class="card card-body text-center text-muted py-5">
        <i class="fas fa-filter fa-3x mb-3 text-success"></i>
        <p class="mb-0">Choose the filters above to view the report.</p>
    </div>
</div>
@endsection
