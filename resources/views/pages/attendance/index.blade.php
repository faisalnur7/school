@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i>Student Attendance (Per Date)
                    </h4>
                </div>
            </div>
            <div class="card-body py-2">

                <div class="d-flex flex-wrap align-items-end">

                    {{-- Session --}}
                    <div class="p-1" style="flex: 1; min-width: 180px;">
                        <select id="att_session_id" class="form-control form-control-sm" required>
                            <option value="">Select Session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Class --}}
                    <div class="p-1" style="flex: 1; min-width: 140px;">
                        <select id="classSelect" class="form-control form-control-sm" required>
                            <option value="">Select Class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section --}}
                    <div class="p-1" style="flex: 1; min-width: 140px;">
                        <select id="sectionSelect" class="form-control form-control-sm" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="p-1" style="flex: 1; min-width: 160px;">
                        <input type="date" id="att_date" class="form-control form-control-sm"
                            value="{{ $defaultDate }}" required />
                    </div>

                    {{-- Button --}}
                    <div class="p-1" style="min-width: 140px;">
                        <button type="button" id="btnLoadStudents" class="btn btn-primary btn-sm btn-block w-100">
                            Load Students
                        </button>
                    </div>

                </div>

            </div>
        </div>

        <div id="attendanceStudentsWrap"></div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        const sectionSelect = document.getElementById('sectionSelect');
        document.getElementById('classSelect').addEventListener('change', function() {
            const classId = this.value;
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (!classId) return;
            fetch(`{{ route('load_section_groups') }}?school_class_id=${classId}`)
                .then(async r => {
                    if (!r.ok) throw new Error('Failed to load sections');
                    return r.json();
                })
                .then(data => {
                    const sections = Array.isArray(data?.sections) ? data.sections : [];
                    sections.forEach(s => {
                        sectionSelect.insertAdjacentHTML('beforeend',
                            `<option value="${s.id}">${s.name_en}</option>`);
                    });
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                });
        });

        document.getElementById('btnLoadStudents').addEventListener('click', function() {
            const sessionId = document.getElementById('att_session_id').value;
            const classId = document.getElementById('classSelect').value;
            const sectionId = document.getElementById('sectionSelect').value;
            const date = document.getElementById('att_date').value;

            const wrap = document.getElementById('attendanceStudentsWrap');
            wrap.innerHTML = `<div class="card card-body text-center text-muted py-4">Loading...</div>`;

            const qs = new URLSearchParams({
                session_id: sessionId,
                class_id: classId,
                section_id: sectionId,
                date: date
            });
            fetch(`/teacher/attendance/load?${qs.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async r => {
                    if (r.ok) return {
                        ok: true,
                        html: await r.text()
                    };
                    let msg = 'Failed to load students.';
                    try {
                        const j = await r.json();
                        msg = j.message || msg;
                    } catch (e) {
                        // ignore
                    }
                    return {
                        ok: false,
                        msg,
                        status: r.status
                    };
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
