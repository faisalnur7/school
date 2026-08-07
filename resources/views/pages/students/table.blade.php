<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3 students-card-header">
        <div class="flex flex-col students-card-copy">
            <h3 class="card-title mb-0 text-white text-lg">{{ __('Student Directory')}}</h3>
            <div class="students-directory-subtitle">
                {{ __('Browse current student records and manage each profile from one place.') }}
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end ml-auto students-card-actions">
            <a href="{{ route('students.list-pdf', request()->all()) }}" class="btn btn-sm students-pdf-action"
                target="_blank">
                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                <span class="sr-only">PDF</span>
            </a>
            <a href="{{ route('students.admission') }}" class="btn btn-sm btn-primary students-add-student-btn">
                <i class="fas fa-plus mr-1"></i> {{ __('Add Student') }}
            </a>
        </div>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="px-3 pt-3 pb-2">
            <span class="badge"
                style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:12px;padding:6px 12px">
                Total: {{ number_format($students->total()) }} Students
            </span>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="students-serial">#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Academic') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Guardian') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $rowNumber = max(0, ($students->firstItem() ?? 1) - 1);
                    @endphp
                    @forelse ($groupedStudents as $classGroup)
                        <tr class="students-group-row students-group-row--class">
                            <td colspan="7">
                                <span class="students-group-badge">Class</span>
                                <strong>{{ $classGroup['class_name'] }}</strong>
                            </td>
                        </tr>

                        @foreach ($classGroup['sections'] as $sectionGroup)
                            <tr class="students-group-row students-group-row--section">
                                <td colspan="7">
                                    <span class="students-group-badge students-group-badge--section">Section</span>
                                    <strong>{{ $sectionGroup['section_name'] }}</strong>
                                    <span class="students-group-meta">{{ $sectionGroup['students']->count() }} students</span>
                                </td>
                            </tr>

                            @foreach ($sectionGroup['students'] as $student)
                                @php
                                    $rowNumber++;
                                    $academicInformation =
                                        $student->academicInformations && $student->academicInformations->isNotEmpty()
                                            ? $student->academicInformations->last()
                                            : null;
                                @endphp
                                <tr>
                                    <td class="students-serial">{{ $rowNumber }}</td>

                                    <td>
                                        <div class="student-row-main">
                                            <a href="{{ route('students.show', $student->id) }}">
                                                @if ($student->image)
                                                    <img src="{{ asset($student->image) }}" alt="{{ $student->full_name_en }}"
                                                        class="student-avatar">
                                                @else
                                                    <div class="student-avatar">
                                                        {{ strtoupper(substr($student->full_name_en, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </a>

                                            <div class="student-meta-stack">
                                                <a href="{{ route('students.show', $student->id) }}">
                                                    <p class="student-name">{{ $student->full_name_en }}</p>
                                                </a>

                                                @if ($student->full_name_bn)
                                                    <span class="student-subline">{{ $student->full_name_bn }}</span>
                                                @endif

                                                <span class="student-inline-meta">
                                                    <i class="far fa-id-badge"></i>
                                                    ID: {{ $student->student_cid ?? 'N/A' }}
                                                </span>

                                                @if ($student->date_of_birth)
                                                    <span class="student-inline-meta">
                                                        <i class="fas fa-birthday-cake"></i>
                                                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}
                                                        <span>({{ \Carbon\Carbon::parse($student->date_of_birth)->age }}
                                                            yrs)</span>
                                                    </span>
                                                @endif

                                                <div class="d-flex flex-wrap" style="gap: .45rem;">
                                                    @if ($student->gender)
                                                        <span
                                                            class="student-chip student-chip--light">{{ ucfirst($student->gender_text ?? $student->gender) }}</span>
                                                    @endif
                                                    @if ($student->blood_group_text)
                                                        <span
                                                            class="student-chip student-chip--light">{{ $student->blood_group_text }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($academicInformation)
                                            <div class="student-meta-stack">
                                                <span class="student-meta-item"><i class="fas fa-layer-group"></i> Session:
                                                    {{ $academicInformation->academicSession->name_en ?? 'N/A' }}</span>
                                                <span class="student-meta-item"><i class="fas fa-school"></i> Class:
                                                    {{ $academicInformation->schoolClass->name_en ?? 'N/A' }}</span>
                                                <span class="student-meta-item"><i class="fas fa-door-open"></i> Section:
                                                    {{ $academicInformation->section->name_en ?? 'N/A' }}</span>
                                                <span class="student-meta-item"><i class="fas fa-sort-numeric-down"></i> Roll:
                                                    {{ $academicInformation->roll ?? 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="student-subline">No academic info</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="student-contact-stack">
                                            @if ($student->father_phone)
                                                <span class="student-contact-item"><i class="fas fa-phone"></i> Father:
                                                    {{ $student->father_phone }}</span>
                                            @endif
                                            @if ($student->mother_phone)
                                                <span class="student-contact-item"><i class="fas fa-phone"></i> Mother:
                                                    {{ $student->mother_phone }}</span>
                                            @endif
                                            @if ($student->guardian_phone)
                                                <span class="student-contact-item"><i class="fas fa-phone"></i> Guardian:
                                                    {{ $student->guardian_phone }}</span>
                                            @endif
                                            @if ($student->father_email)
                                                <span class="student-contact-item"><i class="fas fa-envelope"></i>
                                                    {{ $student->father_email }}</span>
                                            @elseif ($student->mother_email)
                                                <span class="student-contact-item"><i class="fas fa-envelope"></i>
                                                    {{ $student->mother_email }}</span>
                                            @elseif ($student->guardian_email)
                                                <span class="student-contact-item"><i class="fas fa-envelope"></i>
                                                    {{ $student->guardian_email }}</span>
                                            @endif

                                            @if (
                                                !$student->father_phone &&
                                                    !$student->mother_phone &&
                                                    !$student->guardian_phone &&
                                                    !$student->father_email &&
                                                    !$student->mother_email &&
                                                    !$student->guardian_email)
                                                <span class="student-subline">No contact details</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="student-guardian-stack">
                                            @if ($student->guardian_type == 1)
                                                <span class="student-chip student-chip--info">Father</span>
                                            @elseif ($student->guardian_type == 2)
                                                <span class="student-chip student-chip--success">Mother</span>
                                            @elseif ($student->guardian_type == 3)
                                                <span class="student-chip student-chip--warning">Other Guardian</span>
                                            @else
                                                <span class="student-subline">Not set</span>
                                            @endif

                                            @if ($student->guardian_name)
                                                <span class="student-subline">{{ $student->guardian_name }}</span>
                                            @endif

                                            <span class="student-subline">Created
                                                {{ optional($student->created_at)->format('d M Y') ?? 'N/A' }}</span>
                                        </div>
                                    </td>

                                    <td class="student-status-cell">
                                        <form action="{{ route('students.toggle-status', $student->id) }}" method="POST"
                                            class="student-status-form">
                                            @csrf
                                            <label class="student-switch" for="statusSwitch{{ $student->id }}">
                                                <input type="checkbox" id="statusSwitch{{ $student->id }}"
                                                    onchange="this.form.submit()" {{ $student->status ? 'checked' : '' }}>
                                                <span class="student-switch-track"></span>
                                            </label>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="student-actions">
                                            <a href="{{ route('students.show', $student->id) }}" class="student-icon-btn student-icon-btn--view"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student->id) }}" class="student-icon-btn student-icon-btn--edit"
                                                title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('students.delete', $student->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this student?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="student-icon-btn student-icon-btn--danger"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="students-empty">
                                    <i class="fas fa-user-slash"></i>
                                    <p class="mb-2">No students found for the current filters.</p>
                                    @if (request()->hasAny([
                                            'search',
                                            'academic_session_id',
                                            'school_class_id',
                                            'section_id',
                                            'group_id',
                                            'phone',
                                            'gender',
                                            'status',
                                            'present_division_id',
                                            'present_district_id',
                                            'present_police_station_id',
                                            'present_post_office_id',
                                        ]))
                                        <a href="{{ route('students.index') }}"
                                            class="btn btn-dark students-action-btn">
                                            Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-block d-md-none px-3">
            @php
                $mobileRowNumber = max(0, ($students->firstItem() ?? 1) - 1);
            @endphp

            @forelse ($groupedStudents as $classGroup)
                <div class="mb-3 border rounded-4 overflow-hidden bg-white shadow-sm">
                    <div class="px-3 py-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="students-group-badge">Class</span>
                                <strong class="text-dark">{{ $classGroup['class_name'] }}</strong>
                            </div>
                            <div class="small text-muted mt-1">
                                {{ collect($classGroup['sections'])->sum(fn ($sectionGroup) => $sectionGroup['students']->count()) }}
                                students
                            </div>
                        </div>
                    </div>

                    @foreach ($classGroup['sections'] as $sectionGroup)
                        <div class="px-3 py-2 border-bottom bg-white">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="students-group-badge students-group-badge--section">Section</span>
                                <strong class="text-dark">{{ $sectionGroup['section_name'] }}</strong>
                                <span class="small text-muted">{{ $sectionGroup['students']->count() }} students</span>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="d-grid gap-3">
                                @foreach ($sectionGroup['students'] as $student)
                                    @php
                                        $mobileRowNumber++;
                                        $academicInformation =
                                            $student->academicInformations && $student->academicInformations->isNotEmpty()
                                                ? $student->academicInformations->last()
                                                : null;
                                    @endphp

                                    <article class="border rounded-4 overflow-hidden bg-white shadow-sm">
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <a href="{{ route('students.show', $student->id) }}" class="flex-shrink-0">
                                                    @if ($student->image)
                                                        <img src="{{ asset($student->image) }}" alt="{{ $student->full_name_en }}"
                                                            class="student-avatar" style="width:44px;height:56px;border-radius:12px;">
                                                    @else
                                                        <div class="student-avatar" style="width:44px;height:56px;border-radius:12px;font-size:0.9rem;">
                                                            {{ strtoupper(substr($student->full_name_en, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </a>

                                                <div style="min-width: 0; flex: 1 1 auto; text-align: left;">
                                                    <a href="{{ route('students.show', $student->id) }}" class="text-decoration-none">
                                                        <h4 class="mb-0 text-dark" style="font-size:0.92rem;font-weight:700;line-height:1.15;">
                                                            {{ $student->full_name_en }}
                                                        </h4>
                                                    </a>

                                                    @if ($student->full_name_bn)
                                                        <div class="small text-muted" style="font-size:0.74rem;">{{ $student->full_name_bn }}</div>
                                                    @endif

                                                    <div class="small text-muted mt-1" style="font-size:0.72rem;">
                                                        #{{ $mobileRowNumber }} · CID: {{ $student->student_cid ?? 'N/A' }}
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row g-2 mt-2">
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size:0.7rem;">Session</div>
                                                    <div class="fw-semibold text-dark small" style="font-size:0.78rem;">
                                                        {{ $academicInformation->academicSession->name_en ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size:0.7rem;">Class</div>
                                                    <div class="fw-semibold text-dark small" style="font-size:0.78rem;">
                                                        {{ $academicInformation->schoolClass->name_en ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size:0.7rem;">Section</div>
                                                    <div class="fw-semibold text-dark small" style="font-size:0.78rem;">
                                                        {{ $academicInformation->section->name_en ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size:0.7rem;">Roll</div>
                                                    <div class="fw-semibold text-dark small" style="font-size:0.78rem;">
                                                        {{ $academicInformation->roll ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                @if ($student->gender)
                                                    <span class="student-chip student-chip--light">{{ ucfirst($student->gender_text ?? $student->gender) }}</span>
                                                @endif
                                                @if ($student->blood_group_text)
                                                    <span class="student-chip student-chip--light">{{ $student->blood_group_text }}</span>
                                                @endif
                                                @if ($student->guardian_name)
                                                    <span class="student-chip student-chip--info">{{ $student->guardian_name }}</span>
                                                @endif
                                            </div>

                                            <details class="mt-2">
                                                <summary class="small fw-semibold text-secondary" style="font-size:0.76rem;">More details</summary>
                                                <div class="pt-2 small text-muted" style="font-size:0.74rem;">
                                                    @if ($student->date_of_birth)
                                                        <div class="d-flex gap-2 mb-1">
                                                            <i class="fas fa-birthday-cake mt-1"></i>
                                                            <span>
                                                                {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}
                                                                ({{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs)
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if ($student->father_phone || $student->mother_phone || $student->guardian_phone)
                                                        <div class="d-flex gap-2 mb-1">
                                                            <i class="fas fa-phone mt-1"></i>
                                                            <span>{{ $student->father_phone ?: ($student->mother_phone ?: $student->guardian_phone) }}</span>
                                                        </div>
                                                    @endif

                                                    @if ($student->father_email || $student->mother_email || $student->guardian_email)
                                                        <div class="d-flex gap-2 mb-1">
                                                            <i class="fas fa-envelope mt-1"></i>
                                                            <span>{{ $student->father_email ?: ($student->mother_email ?: $student->guardian_email) }}</span>
                                                        </div>
                                                    @endif

                                                    <div class="d-flex gap-2">
                                                        <i class="fas fa-user-shield mt-1"></i>
                                                        <span>
                                                            Guardian:
                                                            {{ $student->guardian_type == 1 ? 'Father' : ($student->guardian_type == 2 ? 'Mother' : ($student->guardian_type == 3 ? 'Other Guardian' : 'Not set')) }}
                                                            @if ($student->guardian_name)
                                                                · {{ $student->guardian_name }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </details>
                                        </div>

                                        <div class="px-2 py-2 border-top bg-light d-flex align-items-center justify-content-between gap-2 flex-nowrap"
                                            style="display:flex !important; flex-direction:row !important; align-items:center !important; justify-content:space-between !important; width:100%; flex-wrap:nowrap !important;">
                                            <form action="{{ route('students.toggle-status', $student->id) }}" method="POST" class="student-status-form mb-0">
                                                @csrf
                                                <label class="student-switch" for="mobileStatusSwitch{{ $student->id }}">
                                                    <input type="checkbox" id="mobileStatusSwitch{{ $student->id }}"
                                                        onchange="this.form.submit()" {{ $student->status ? 'checked' : '' }}>
                                                    <span class="student-switch-track"></span>
                                                </label>
                                            </form>

                                            <div class="d-flex align-items-center gap-1 flex-nowrap ms-auto"
                                                style="display:flex !important; flex-direction:row !important; align-items:center !important; justify-content:flex-end !important; gap:0.35rem !important; flex-wrap:nowrap !important; margin-left:auto !important; width:auto !important;">
                                                <a href="{{ route('students.show', $student->id) }}" class="student-icon-btn student-icon-btn--view" title="View" aria-label="View student">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}" class="student-icon-btn student-icon-btn--edit" title="Edit" aria-label="Edit student">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('students.delete', $student->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this student?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="student-icon-btn student-icon-btn--danger"
                                                        title="Delete" aria-label="Delete student">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="students-empty">
                    <i class="fas fa-user-slash"></i>
                    <p class="mb-2">No students found for the current filters.</p>
                    @if (request()->hasAny([
                            'search',
                            'academic_session_id',
                            'school_class_id',
                            'section_id',
                            'group_id',
                            'phone',
                            'gender',
                            'status',
                            'present_division_id',
                            'present_district_id',
                            'present_police_station_id',
                            'present_post_office_id',
                        ]))
                        <a href="{{ route('students.index') }}" class="btn btn-dark students-action-btn">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="students-footer px-3 pt-3">
            <div>
                @if ($students->total() > 0)
                    Showing {{ $students->firstItem() }}-{{ $students->lastItem() }} of {{ $students->total() }}
                    students
                @else
                    0 students
                @endif
            </div>

            @if ($students->hasPages())
                <div class="students-pagination">
                    {{ $students->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
