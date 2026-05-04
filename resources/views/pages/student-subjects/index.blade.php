@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 font-weight-bold">
            <i class="fas fa-user-graduate text-primary mr-2"></i>Student Subject Assignment
        </h4>
    </div>

    {{-- Filter --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <form method="GET" class="form-inline flex-wrap">
                <select name="class_id" id="filter_class_id" class="form-control form-control-sm mr-2 mb-1" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name_en }}
                    </option>
                    @endforeach
                </select>
                <select name="section_id" id="filter_section_id" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->name_en }}
                    </option>
                    @endforeach
                </select>
                <select name="session_id" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->name_en ?? $session->name_bn }}
                    </option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary mb-1"><i class="fas fa-search mr-1"></i>Search</button>
                <a href="{{ route('student-subjects.index') }}" class="btn btn-sm btn-light mb-1 ml-1">Reset</a>

                @if(request('class_id'))
                <form method="POST" action="{{ route('student-subjects.bulk-assign') }}" class="d-inline ml-3">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="session_id" value="{{ request('session_id') ?? \App\Models\AcademicSession::where('status',1)->value('id') }}">
                    <button class="btn btn-sm btn-warning mb-1" onclick="return confirm('Bulk assign all compulsory subjects to all students in this class?')">
                        <i class="fas fa-magic mr-1"></i>Bulk Assign Compulsory
                    </button>
                </form>
                @endif
            </form>
        </div>
    </div>

    @if($students->isNotEmpty())
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
                    @foreach($students as $i => $student)
                    @php
                        $info = $student->academicInformations->first();
                        $subjectCount = $student->studentSubjects()
                            ->when(request('session_id'), fn($q) => $q->where('academic_session_id', request('session_id')))
                            ->count();
                    @endphp
                    <tr>
                        <td>{{ $students->firstItem() + $i }}</td>
                        <td>
                            <strong>{{ $student->full_name_en }}</strong>
                            @if($student->full_name_bn)
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
                            <a href="{{ route('student-subjects.assign', ['student' => $student->id, 'session_id' => request('session_id')]) }}"
                                class="btn btn-xs btn-primary">
                                <i class="fas fa-book mr-1"></i>Assign Subjects
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
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
document.getElementById('filter_class_id').addEventListener('change', function () {
    const classId = this.value;
    const sectionSelect = document.getElementById('filter_section_id');
    sectionSelect.innerHTML = '<option value="">All Sections</option>';
    if (!classId) return;
    fetch(`/ajax/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(s => {
                sectionSelect.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.name_en}</option>`);
            });
        });
});
</script>
@endsection
