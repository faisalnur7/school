@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif



        {{-- Filter --}}
        <div class="card card-outline card-primary mb-3 student-subjects-filter-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-user-graduate mr-2"></i>Student Subject Assignment
                    </h4>
                    <small class="text-white-50">Filter by academic session, class, section, exam, and student ID.</small>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('student-subjects.index') }}" id="studentSubjectsFilterForm" class="student-subjects-filter-form">
                    <div class="student-subjects-filter-grid">
                        <div class="student-subjects-filter-group">
                            <label class="font-weight-bold">Academic Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="filter_session_id" class="form-control form-control-sm student-subjects-filter-control" required>
                                <option value="">-- Select Session --</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" {{ (string) $selectedSessionId === (string) $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en ?? $session->name_bn }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="student-subjects-filter-group">
                            <label class="font-weight-bold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="filter_class_id" class="form-control form-control-sm student-subjects-filter-control" required>
                                <option value="">-- Select Class --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClass?->id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="student-subjects-filter-group">
                            <label class="font-weight-bold">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="filter_section_id" class="form-control form-control-sm student-subjects-filter-control" @disabled(!$selectedClass)>
                                <option value="">-- All Sections --</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" {{ (string) $selectedSectionId === (string) $section->id ? 'selected' : '' }}>
                                        {{ $section->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="student-subjects-filter-group">
                            <label class="font-weight-bold">Exam <span class="text-danger">*</span></label>
                            <select
                                name="exam_id"
                                id="filter_exam_id"
                                class="form-control form-control-sm student-subjects-filter-control"
                                data-exams-url="{{ route('student-subjects.ajax.exams-by-session') }}"
                                @disabled(!$selectedSessionId)
                            >
                                <option value="">-- Select Exam --</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ $selectedExam?->id == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ $exam->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="student-subjects-filter-group student-subjects-filter-group-wide">
                            <label class="font-weight-bold">Student ID <span class="text-muted">(optional)</span></label>
                            <div class="student-subjects-filter-inline">
                                <input
                                    type="text"
                                    name="student_cid"
                                    id="filter_student_cid"
                                    class="form-control form-control-sm student-subjects-filter-control student-subjects-filter-input"
                                    placeholder="Leave blank for all students"
                                    value="{{ $studentCid }}">

                                <div class="student-subjects-filter-actions">
                                    <button type="submit" class="btn btn-dark btn-sm student-subjects-action-btn" title="View" aria-label="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('student-subjects.index') }}" class="btn btn-outline-secondary btn-sm student-subjects-action-btn" title="Reset" aria-label="Reset">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                @if ($selectedClass)
                    <div class="mt-3">
                        <form method="POST" action="{{ route('student-subjects.bulk-assign') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                            <input type="hidden" name="session_id" value="{{ $selectedSessionId }}">
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Bulk assign all compulsory subjects to all students in this class?')">
                                <i class="fas fa-magic mr-1"></i>Bulk Assign Compulsory
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        @if ($students->isNotEmpty())
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Roll</th>
                                <th>Section</th>
                                <th>Group</th>
                                <th>Subjects Assigned</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                @php
                                    $info = $student->academicInformations->first();
                                    $subjectCount = $student
                                        ->studentSubjects()
                                        ->when(
                                            request('session_id'),
                                            fn($q) => $q->where('academic_session_id', request('session_id')),
                                        )
                                        ->count();
                                @endphp
                                <tr>
                                    <td>{{ $students->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $student->full_name_en }}</strong>
                                        @if ($student->full_name_bn)
                                            <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $info?->roll ?? '-' }}</td>
                                    <td>{{ $info?->section?->name_en ?? '-' }}</td>
                                    <td>{{ $info?->group?->name_en ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $subjectCount > 0 ? 'success' : 'secondary' }}">
                                            {{ $subjectCount }} subjects
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('student-subjects.assign', ['student' => $student->id, 'session_id' => $selectedSessionId, 'exam_id' => $selectedExam?->id]) }}"
                                            class="btn btn-xs btn-primary">
                                            <i class="fas fa-book mr-1"></i>Assign Subjects
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($students->hasPages())
                    <div class="card-footer">{{ $students->links() }}</div>
                @endif
            </div>
        @elseif(request('class_id'))
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>No students found for the selected filters.</p>
            </div>
        @else
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-filter fa-3x mb-3"></i>
                <p>Select a class to view students.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('studentSubjectsFilterForm');
            const classSelect = document.getElementById('filter_class_id');
            const sectionSelect = document.getElementById('filter_section_id');
            const sessionSelect = document.getElementById('filter_session_id');
            const examSelect = document.getElementById('filter_exam_id');
            const examsUrl = examSelect?.dataset.examsUrl;
            const selectedSectionId = @json($selectedSectionId);
            const selectedExamId = @json($selectedExam?->id);

            function loadSections(classId, selectedId = null) {
                if (!sectionSelect) return;

                if (!classId) {
                    sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
                    sectionSelect.disabled = true;
                    return;
                }

                sectionSelect.disabled = true;
                sectionSelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`{{ route('ajax.sections-by-class') }}?class_id=${encodeURIComponent(classId)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to load sections');
                        return response.json();
                    })
                    .then(data => {
                        let html = '<option value="">-- All Sections --</option>';
                        (Array.isArray(data) ? data : []).forEach(section => {
                            const selected = String(selectedId) === String(section.id) ? 'selected' : '';
                            html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                        });
                        sectionSelect.innerHTML = html;
                        sectionSelect.disabled = false;
                    })
                    .catch(() => {
                        sectionSelect.innerHTML = '<option value="">-- All Sections --</option>';
                        sectionSelect.disabled = false;
                    });
            }

            function loadExams(sessionId, selectedId = null) {
                if (!examSelect) return;

                if (!sessionId) {
                    examSelect.innerHTML = '<option value="">-- Select Exam --</option>';
                    examSelect.disabled = true;
                    return;
                }

                examSelect.disabled = true;
                examSelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`${examsUrl}?session_id=${encodeURIComponent(sessionId)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to load exams');
                        return response.json();
                    })
                    .then(data => {
                        let html = '<option value="">-- Select Exam --</option>';
                        (Array.isArray(data?.exams) ? data.exams : []).forEach(exam => {
                            const selected = String(selectedId) === String(exam.id) ? 'selected' : '';
                            html += `<option value="${exam.id}" ${selected}>${exam.name} (${exam.type_label})</option>`;
                        });
                        examSelect.innerHTML = html;
                        examSelect.disabled = false;
                    })
                    .catch(() => {
                        examSelect.innerHTML = '<option value="">-- Select Exam --</option>';
                        examSelect.disabled = false;
                    });
            }

            classSelect?.addEventListener('change', function () {
                loadSections(this.value);
            });

            sessionSelect?.addEventListener('change', function () {
                if (examSelect) {
                    examSelect.value = '';
                }
                loadExams(this.value);
            });

            if (classSelect?.value) {
                loadSections(classSelect.value, selectedSectionId);
            } else if (sectionSelect) {
                sectionSelect.disabled = true;
            }

            if (sessionSelect?.value) {
                loadExams(sessionSelect.value, selectedExamId);
            } else if (examSelect) {
                examSelect.disabled = true;
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .student-subjects-filter-card .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 0;
        }

        .student-subjects-filter-form {
            width: 100%;
        }

        .student-subjects-filter-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
        }

        .student-subjects-filter-group {
            min-width: 0;
        }

        .student-subjects-filter-group-wide {
            grid-column: span 1;
        }

        .student-subjects-filter-control {
            min-height: 42px;
            border-radius: 10px;
        }

        .student-subjects-filter-inline {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .student-subjects-filter-input {
            flex: 1 1 auto;
            min-width: 0;
        }

        .student-subjects-filter-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex: 0 0 auto;
        }

        .student-subjects-action-btn {
            min-width: 42px;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        @media (max-width: 1200px) {
            .student-subjects-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .student-subjects-filter-grid {
                grid-template-columns: 1fr;
            }

            .student-subjects-filter-inline {
                flex-direction: column;
                align-items: stretch;
            }

            .student-subjects-filter-actions {
                justify-content: flex-start;
            }
        }
    </style>
@endsection
