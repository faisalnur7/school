@extends('layouts.master')

@section('contents')
<div class="col-12">
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

    <div class="card card-outline no-print result-filter-panel">
        <div class="card-header">
            <h3 class="card-title text-white"><i class="fas fa-filter mr-2 text-info"></i>Filter Options</h3>
        </div>
        <div class="card-body">
            <form id="reportForm" method="GET" action="{{ route('result.tutorial-report.show') }}">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control" required>
                            <option value="">— Select Session —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en ?? $s->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelect" class="form-control" required>
                            <option value="">— Select Class —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="sectionSelect" class="form-control" required>
                            <option value="">— Select Section —</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Tutorial Exam <span class="text-danger">*</span></label>
                        <select name="exam_id" class="form-control" required>
                            <option value="">— Select Exam —</option>
                            @foreach($exams as $e)
                                <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Student ID <small class="text-muted">(optional)</small></label>
                        <input type="text" name="student_id" class="form-control" value="{{ request('student_id') }}" placeholder="Enter Student ID or CID">
                    </div>
                </div>
                <div class="result-filter-actions mt-2">
                    <button type="submit" class="btn btn-info result-filter-icon-btn" title="View Report" aria-label="View Report">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" id="pdfBtn" class="btn btn-danger result-filter-icon-btn" title="Download PDF" aria-label="Download PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <a href="{{ route('results.hub') }}" class="btn btn-secondary result-filter-icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    var selectedSection = @json(request('section_id'));

    function loadSections(classId, selectedSectionId = null) {
        var $section = $('#sectionSelect');
        if (!classId) {
            $section.html('<option value="">— Select Section —</option>');
            if (window.refreshSelect2) refreshSelect2($section);
            return;
        }

        $section.html('<option value="">Loading...</option>');
        if (window.refreshSelect2) refreshSelect2($section);

        $.get('/ajax/sections-by-class', { class_id: classId }, function (data) {
            var opts = '<option value="">— Select Section —</option>';
            $.each(data, function (i, s) {
                var selected = String(selectedSectionId) === String(s.id) ? 'selected' : '';
                opts += '<option value="' + s.id + '" ' + selected + '>' + (s.name_en || s.name_bn) + '</option>';
            });
            $section.html(opts);
            if (window.refreshSelect2) refreshSelect2($section);
        });
    }

    $('#classSelect').on('change', function () {
        loadSections($(this).val());
    });

    if ($('#classSelect').val()) {
        loadSections($('#classSelect').val(), selectedSection);
    }
});

$('#pdfBtn').on('click', function () {
    var form = $('#reportForm');
    var params = form.serialize();
    window.open('{{ route('result.tutorial-report.pdf') }}?' + params, '_blank');
});
</script>
@endsection
