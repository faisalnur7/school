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
        <div class="attendance-summary" aria-live="polite">
            <span class="attendance-summary__chip attendance-summary__chip--present"><i class="fas fa-check-circle"></i> <b id="attPresentCount">{{ $students->count() }}</b> Present</span>
            <span class="attendance-summary__chip attendance-summary__chip--absent"><i class="fas fa-times-circle"></i> <b id="attAbsentCount">0</b> Absent</span>
            <span class="attendance-summary__chip attendance-summary__chip--total">{{ $students->count() }} students</span>
        </div>
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

            <div class="attendance-table-wrap">
                <table class="table table-sm table-hover mb-0 attendance-mark-table">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <label class="attendance-select-all mb-0">
                                    <input type="checkbox" id="attendanceSelectAll">
                                    <span>Present</span>
                                </label>
                            </th>
                            <th>Roll</th>
                            <th>Student CID</th>
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
                            <tr class="attendance-row {{ $checked ? '' : 'is-absent' }}">
                                <td class="text-center attendance-row__check" data-label="Present">
                                    <input type="hidden" name="student_ids[]" value="{{ $sid }}">
                                    <input type="checkbox" name="present_ids[]" value="{{ $sid }}" {{ $checked ? 'checked' : '' }}>
                                </td>
                                <td class="attendance-row__roll" data-label="Roll">{{ $info->roll ?? '-' }}</td>
                                <td class="attendance-row__sid" data-label="CID">{{ $student->student_cid ?? '-' }}</td>
                                <td class="attendance-row__name">
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
                <span class="attendance-results-card__hint d-none d-md-inline">Unchecked students will be saved as absent.</span>
                <button type="submit" class="btn btn-success attendance-save-btn">
                    <i class="fas fa-save mr-1"></i>{{ $isUpdate ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif
