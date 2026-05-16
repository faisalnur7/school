@php
    $isUpdate = (bool) $attendance;
    $action = $isUpdate ? route('attendance.update', $attendance) : route('attendance.store');
@endphp

@if($students->isEmpty())
<div class="card card-body text-center text-muted py-5">
    <i class="fas fa-users fa-3x mb-3"></i>
    <p>No students found for the selected filters.</p>
</div>
@else
<div class="card card-outline card-info">
    <div class="card-header">
        <h6 class="mb-0 font-weight-bold text-white">
            <i class="fas fa-list mr-2"></i>Students ({{ $students->count() }})
        </h6>
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
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 90px;">Roll</th>
                            <th style="width: 120px;">Student ID</th>
                            <th>Name</th>
                            <th style="width: 120px;" class="text-center">Present</th>
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
                                <td>{{ $info->roll ?? '-' }}</td>
                                <td>{{ $sid }}</td>
                                <td>
                                    {{ $student->full_name_en }}
                                    @if($student->full_name_bn)
                                    <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="student_ids[]" value="{{ $sid }}">
                                    <input type="checkbox" name="present_ids[]" value="{{ $sid }}" {{ $checked ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-save mr-1"></i>{{ $isUpdate ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

