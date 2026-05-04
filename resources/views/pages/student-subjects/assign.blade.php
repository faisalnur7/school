@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="fas fa-book mr-2"></i>Assign Subjects
                        </h4>
                        <small class="text-muted">
                            {{ $student->full_name_en }}
                            @if($student->full_name_bn) / {{ $student->full_name_bn }} @endif
                            &mdash; Class: {{ $academicInfo->schoolClass->name_en ?? '' }}
                            @if($academicInfo->section) / {{ $academicInfo->section->name_en }} @endif
                        </small>
                    </div>
                    <div>
                        <select id="session_switcher" class="form-control form-control-sm" style="width:auto">
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $sessionId ? 'selected' : '' }}>
                                {{ $session->name_en ?? $session->name_bn }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <form method="POST" action="{{ route('student-subjects.save', $student) }}">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $sessionId }}">
                    <input type="hidden" name="class_id" value="{{ $classId }}">

                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Compulsory Subjects --}}
                        @if($compulsory->isNotEmpty())
                        <div class="card card-success card-outline mb-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0"><i class="fas fa-lock mr-2 text-success"></i>Compulsory Subjects</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($compulsory as $assignment)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" disabled checked
                                                id="comp_{{ $assignment->subject_id }}">
                                            <label class="custom-control-label" for="comp_{{ $assignment->subject_id }}">
                                                <strong>{{ $assignment->subject->name }}</strong>
                                                <span class="badge badge-success badge-sm ml-1">Compulsory</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Compulsory subjects are auto-assigned and cannot be removed.</small>
                            </div>
                        </div>
                        @endif

                        {{-- Exclusive Groups (e.g., Biology OR Higher Math) --}}
                        @foreach($exclusiveGroups as $groupKey => $groupAssignments)
                        <div class="card card-warning card-outline mb-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-exchange-alt mr-2 text-warning"></i>
                                    Exclusive Group: <code>{{ $groupKey }}</code>
                                    <small class="text-muted ml-2">(Select one)</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($groupAssignments as $assignment)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input"
                                                id="excl_{{ $groupKey }}_{{ $assignment->subject_id }}"
                                                name="subject_ids[]"
                                                value="{{ $assignment->subject_id }}"
                                                {{ in_array($assignment->subject_id, $currentSubjectIds) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="excl_{{ $groupKey }}_{{ $assignment->subject_id }}">
                                                {{ $assignment->subject->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input"
                                                id="excl_{{ $groupKey }}_none"
                                                name="subject_ids[]"
                                                value="">
                                            <label class="custom-control-label" for="excl_{{ $groupKey }}_none">
                                                None
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Free Optional Subjects --}}
                        @if($freeOptional->isNotEmpty())
                        <div class="card card-info card-outline mb-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0"><i class="fas fa-plus-circle mr-2 text-info"></i>Optional Subjects</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($freeOptional as $assignment)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="opt_{{ $assignment->subject_id }}"
                                                name="subject_ids[]"
                                                value="{{ $assignment->subject_id }}"
                                                {{ in_array($assignment->subject_id, $currentSubjectIds) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="opt_{{ $assignment->subject_id }}">
                                                {{ $assignment->subject->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($compulsory->isEmpty() && $exclusiveGroups->isEmpty() && $freeOptional->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No subjects are assigned to this class yet. Please assign subjects to the class first.
                        </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Save Subject Assignment
                        </button>
                        <a href="{{ route('student-subjects.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('session_switcher').addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('session_id', this.value);
    window.location.href = url.toString();
});
</script>
@endsection
