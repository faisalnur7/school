<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <div>
            <h3 class="card-title mb-0 text-white text-lg">Student Directory</h3>
            <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:2px;">
                Browse current student records and manage each profile from one place.
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end ml-auto">
            <a href="{{ route('students.export', request()->all()) }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-file-excel mr-1"></i> Export
            </a>
            <a href="{{ route('students.list-pdf', request()->all()) }}" class="btn btn-sm btn-outline-light"
                target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> Add Student
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

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="students-serial">#</th>
                        <th>Name</th>
                        <th>Academic</th>
                        <th>Contact</th>
                        <th>Guardian</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($students as $index => $student)
                        @php
                            $academicInformation =
                                $student->academicInformations && $student->academicInformations->isNotEmpty()
                                    ? $student->academicInformations->last()
                                    : null;
                        @endphp
                        <tr>
                            <td class="students-serial">{{ $students->firstItem() + $index }}</td>

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
                                            CID: {{ $student->student_cid ?? 'N/A' }}
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
                                    <a href="{{ route('students.show', $student->id) }}" class="student-icon-btn"
                                        title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student->id) }}" class="student-icon-btn"
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
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
