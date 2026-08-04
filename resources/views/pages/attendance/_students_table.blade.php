@php
    $isUpdate = (bool) $attendance;
    $action = $isUpdate ? route('attendance.update', $attendance) : route('attendance.store');
@endphp

@if($students->isEmpty())
<div class="attendance-empty-state attendance-empty-state--results">
    <div class="attendance-empty-state__icon">
        <i class="fas fa-users"></i>
    </div>
    <h5>No students found</h5>
    <p>No students matched the selected session, class, and section.</p>
</div>
@else
<div class="card attendance-results-card border-0 shadow-sm">
    <div class="attendance-results-card__header">
        <div>
            <div class="attendance-results-card__eyebrow">Students Loaded</div>
            <h6 class="mb-0 attendance-results-card__title">
                <i class="fas fa-list mr-2"></i>Students
            </h6>
        </div>
        <div class="attendance-results-card__count">{{ $students->count() }} students</div>
    </div>
    <div class="card-body p-0">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($isUpdate)
            @method('PATCH')
            @endif

            <input type="hidden" name="session_id" value="{{ $sessionId }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 attendance-mark-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;" class="text-center">
                                <label class="attendance-select-all mb-0">
                                    <input type="checkbox" id="attendanceSelectAll">
                                    <span>Present</span>
                                </label>
                            </th>
                            <th style="width: 90px;">Roll</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $info)
                            @php
                                $student = $info->student;
                                $sid = $student->id;
                                $status = $statusByStudentId[$sid] ?? null;
                                $checked = $status === null ? true : ($status === 'present');
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="student_ids[]" value="{{ $sid }}">
                                    <input type="checkbox" name="present_ids[]" value="{{ $sid }}" {{ $checked ? 'checked' : '' }}>
                                </td>
                                <td>{{ $info->roll ?? '-' }}</td>
                                <td>
                                    {{ $student->full_name_en }}
                                    @if($student->full_name_bn)
                                    <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="attendance-results-card__footer">
                <button type="submit" class="btn btn-sm btn-success attendance-save-btn">
                    <i class="fas fa-save mr-1"></i>{{ $isUpdate ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif
