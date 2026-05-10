@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 font-weight-bold">
            <i class="fas fa-clipboard-check text-primary mr-2"></i>Student Attendance (Per Date)
        </h4>
    </div>

    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="form-inline flex-wrap">
                <select id="att_session_id" class="form-control form-control-sm mr-2 mb-1" required>
                    <option value="">Select Session</option>
                    @foreach($sessions as $session)
                    <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                    @endforeach
                </select>

                <select id="att_class_id" class="form-control form-control-sm mr-2 mb-1" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                    @endforeach
                </select>

                <select id="att_section_id" class="form-control form-control-sm mr-2 mb-1" required>
                    <option value="">Select Section</option>
                </select>

                <input type="date" id="att_date" class="form-control form-control-sm mr-2 mb-1" value="{{ $defaultDate }}" required />

                <button type="button" id="btnLoadStudents" class="btn btn-sm btn-primary mb-1">
                    <i class="fas fa-sync-alt mr-1"></i>Load Students
                </button>
            </div>
        </div>
    </div>

    <div id="attendanceStudentsWrap"></div>
</div>
@endsection

@section('scripts')
<script>
const sectionSelect = document.getElementById('att_section_id');
document.getElementById('att_class_id').addEventListener('change', function () {
    const classId = this.value;
    sectionSelect.innerHTML = '<option value="">Select Section</option>';
    if (!classId) return;
    fetch(`/ajax/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(s => {
                sectionSelect.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.name_en}</option>`);
            });
        });
});

document.getElementById('btnLoadStudents').addEventListener('click', function () {
    const sessionId = document.getElementById('att_session_id').value;
    const classId = document.getElementById('att_class_id').value;
    const sectionId = document.getElementById('att_section_id').value;
    const date = document.getElementById('att_date').value;

    const wrap = document.getElementById('attendanceStudentsWrap');
    wrap.innerHTML = `<div class="card card-body text-center text-muted py-4">Loading...</div>`;

    const qs = new URLSearchParams({session_id: sessionId, class_id: classId, section_id: sectionId, date: date});
    fetch(`/teacher/attendance/load?${qs.toString()}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
        .then(async r => {
            if (r.ok) return {ok: true, html: await r.text()};
            let msg = 'Failed to load students.';
            try {
                const j = await r.json();
                msg = j.message || msg;
            } catch (e) {
                // ignore
            }
            return {ok: false, msg, status: r.status};
        })
        .then(res => {
            if (res.ok) {
                wrap.innerHTML = res.html;
                return;
            }
            wrap.innerHTML = `<div class="alert alert-danger mb-0">${res.msg}</div>`;
        });
});
</script>
@endsection

