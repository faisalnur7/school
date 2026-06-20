@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
             style="width:52px;height:52px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);flex-shrink:0">
            <i class="fas fa-file-invoice text-white fa-lg"></i>
        </div>
        <div>
            <h4 class="mb-0 font-weight-bold text-white">Terminal Exam Report</h4>
            <small class="text-muted">Generate student progress reports by exam, class &amp; section</small>
        </div>
    </div>

    <div class="card card-outline no-print result-filter-panel">
        <div class="card-header">
            <h3 class="card-title text-white"><i class="fas fa-filter mr-2 text-success"></i>Filter Options</h3>
        </div>
        <div class="card-body">
            <form id="reportForm" method="POST" action="{{ route('result.progress-report.show') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control" required>
                            <option value="">— Select Session —</option>
                            @foreach($sessions as $s)
                            <option value="{{ $s->id }}">{{ $s->name_en ?? $s->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelect" class="form-control" required>
                            <option value="">— Select Class —</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name_en }}</option>
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
                        <label class="font-weight-bold">Exam <span class="text-danger">*</span></label>
                        <select name="exam_id" class="form-control" required>
                            <option value="">— Select Exam —</option>
                            @foreach($exams as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold">Student ID <small class="text-muted">(optional)</small></label>
                        <input type="text" name="student_id" class="form-control" placeholder="Leave blank for all students">
                    </div>
                </div>
                <div class="result-filter-actions mt-2">
                    <button type="submit" class="btn btn-success result-filter-icon-btn" title="View Report" aria-label="View Report">
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
$('#classSelect').on('change', function () {
    var classId = $(this).val();
    var $section = $('#sectionSelect');
    $section.html('<option value="">Loading...</option>');
    if (!classId) { $section.html('<option value="">— Select Section —</option>'); return; }
    $.get('/ajax/sections-by-class', { class_id: classId }, function (data) {
        var opts = '<option value="">— Select Section —</option>';
        $.each(data, function (i, s) { opts += '<option value="' + s.id + '">' + (s.name_en || s.name_bn) + '</option>'; });
        $section.html(opts);
        if (window.refreshSelect2) refreshSelect2($section);
    });
});

$('#pdfBtn').on('click', function () {
    var form = $('#reportForm');
    var params = form.serialize().replace('_token=' + $('input[name=_token]').val() + '&', '');
    window.open('{{ route('result.progress-report.pdf') }}?' + params, '_blank');
});
</script>
@endsection
