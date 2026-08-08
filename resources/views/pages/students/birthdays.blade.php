@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-pink-700 to-pink-900 rounded-2xl p-8 mb-8 flex items-center gap-5">
        <i class="fas fa-birthday-cake text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Student Birthdays</h3>
            <p class="text-pink-200 text-sm mt-1 mb-0">Find students celebrating birthdays within a date range</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('students.birthdays') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}" required>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}" required>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold">Session</label>
                        <select name="session_id" class="form-select">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ $sessionId == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold">Class</label>
                        <select name="class_id" class="form-select" id="classFilter">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold">Section</label>
                        <select name="section_id" class="form-select" id="sectionFilter">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold">Group</label>
                        <select name="group_id" class="form-select">
                            <option value="">All Groups</option>
                            @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        @if($date || $fromDate || $toDate)
                        <a href="{{ route('students.birthdays') }}" class="btn btn-outline-secondary ms-1" title="Clear" aria-label="Clear">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center text-white">
            <span class="fw-semibold">
                <i class="fas fa-birthday-cake text-white me-2"></i>
                @if($fromDate && $toDate)
                    Birthdays from {{ \Carbon\Carbon::parse($fromDate)->format('F j') }} to {{ \Carbon\Carbon::parse($toDate)->format('F j') }}
                @elseif($date)
                    Birthdays on {{ \Carbon\Carbon::parse($date)->format('F j') }}
                @else
                    Student Birthday Search Results
                @endif
            </span>
            <span class="badge bg-primary ml-auto">{{ $students->count() }} shown</span>
        </div>
        <div class="card-body p-0">
            @if($students->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-birthday-cake fa-3x mb-3 opacity-25"></i>
                @if($date || ($fromDate && $toDate))
                <p>No students have a birthday in this selected range.</p>
                @else
                <p>Use the filter above to search student birthdays.</p>
                @endif
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>CID</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Session</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Group</th>
                            <th>Phone</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php $info = $student->latestAcademicInformation; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <img src="{{ $student->photo_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                            </td>
                            <td>
                                <a href="{{ route('students.show', ['id' => $student->id]) }}"
                                   class="birthday-student-link fw-semibold text-decoration-none"
                                   title="View student details"
                                   aria-label="View details for {{ $student->full_name_en ?: $student->full_name_bn }}">
                                    {{ $student->full_name_en ?: $student->full_name_bn }}
                                    <i class="fas fa-external-link-alt birthday-student-link__icon" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>{{ $student->student_cid ?? '—' }}</td>
                            <td>{{ $student->date_of_birth?->format('d M Y') }}</td>
                            <td>{{ $student->gender_text }}</td>
                            <td>{{ $info?->academicSession?->name_en ?? '—' }}</td>
                            <td>{{ $info?->schoolClass?->name_en ?? '—' }}</td>
                            <td>{{ $info?->section?->name_en ?? '—' }}</td>
                            <td>{{ $info?->group?->name_en ?? '—' }}</td>
                            <td>{{ $student->guardian_phone ?? $student->father_phone ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('students.birthdays.card.preview', ['student' => $student->id] + request()->query()) }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-sm btn-gradient-warning"
                                   title="Open birthday card print preview"
                                   aria-label="Open birthday card print preview for {{ $student->full_name_en ?: $student->full_name_bn }}">
                                    <i class="fas fa-print me-1"></i> Print Preview
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 pb-3">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('#classFilter').on('change', function () {
        const classId = $(this).val();
        const $sectionSelect = $('#sectionFilter');
        $sectionSelect.html('<option value="">All Sections</option>');
        if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
        if (!classId) return;

        $.ajax({
            url: `{{ route('load_section_groups') }}`,
            type: 'GET',
            data: {
                school_class_id: classId
            },
            dataType: 'json',
            success: function (data) {
                const sections = (data && Array.isArray(data.sections)) ? data.sections : [];
                sections.forEach(function (s) {
                    $sectionSelect.append('<option value="' + s.id + '">' + s.name_en + '</option>');
                });
                if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
            },
            error: function () {
                $sectionSelect.html('<option value="">All Sections</option>');
                if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
            }
        });
    });
});
</script>
@endsection

@section('styles')
<style>
    .birthday-student-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #2563eb;
        transition: color 0.15s ease, text-decoration-color 0.15s ease;
    }

    .birthday-student-link:hover,
    .birthday-student-link:focus {
        color: #1d4ed8;
        text-decoration: underline;
        text-underline-offset: 0.15em;
    }

    .birthday-student-link__icon {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .btn-gradient-warning {
        color: #fff;
        border: 0;
        background: linear-gradient(135deg, #f59e0b 0%, #fb7185 52%, #ec4899 100%);
        box-shadow: 0 6px 14px rgba(236, 72, 153, 0.22);
    }

    .btn-gradient-warning:hover,
    .btn-gradient-warning:focus {
        color: #fff;
        filter: brightness(1.02);
        box-shadow: 0 8px 16px rgba(236, 72, 153, 0.28);
    }

    html[data-theme='dark'] .birthday-student-link {
        color: #93c5fd;
    }

    html[data-theme='dark'] .birthday-student-link:hover,
    html[data-theme='dark'] .birthday-student-link:focus {
        color: #bfdbfe;
    }
</style>
@endsection
