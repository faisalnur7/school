<!-- Students Table -->
<div class="card">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0 font-weight-bold">
                Students
                @if ($students->total() > 0)
                    <span class="badge badge-light ml-1">{{ $students->total() }}</span>
                @endif
            </h4>
            <div class="d-flex">
                <a href="{{ route('students.export', request()->all()) }}" class="btn btn-light btn-sm mr-1">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <a href="{{ route('students.list-pdf', request()->all()) }}" class="btn btn-light btn-sm mr-1" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add Student
                </a>
            </div>
        </div>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Student Info</th>
                        <th>Academic Info</th>
                        <th>Contact</th>
                        <th>Guardian</th>
                        <th class="text-center">Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($students as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>

                            <td>
                                @if ($student->image)
                                    <img src="{{ asset($student->image) }}" alt="{{ $student->full_name_en }}"
                                        class="elevation-1" width="64" height="64" style="object-fit: cover; border-radius: 12px;">
                                @else
                                    <div class="bg-secondary d-flex align-items-center justify-content-center text-white elevation-1"
                                        style="width: 64px; height: 64px; font-size: 20px; border-radius: 12px;">
                                        {{ strtoupper(substr($student->full_name_en, 0, 1)) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div>
                                    <strong>{{ $student->full_name_en }}</strong>
                                    @if ($student->full_name_bn)
                                        <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                    @endif
                                    @if ($student->date_of_birth)
                                        <br><small class="text-muted">
                                            <i class="fas fa-birthday-cake"></i>
                                            {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M, Y') }}
                                            ({{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs)
                                        </small>
                                    @endif
                                    @if ($student->gender)
                                        <br><small class="text-muted">
                                            <i class="fas fa-venus-mars"></i> {{ ucfirst($student->gender_text ?? $student->gender) }}
                                        </small>
                                    @endif
                                    @if ($student->blood_group_text)
                                        <br><small class="text-muted">
                                            <i class="fas fa-tint"></i> {{ $student->blood_group_text }}
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if ($student->academicInformations && $student->academicInformations->isNotEmpty())
                                    <small>
                                        @php
                                            $academicInformations = $student->academicInformations->last();
                                        @endphp
                                        <strong>Session:</strong>
                                        {{ $academicInformations->academicSession->name_en ?? 'N/A' }}<br>
                                        <strong>Class:</strong>
                                        {{ $academicInformations->schoolClass->name_en ?? 'N/A' }}<br>
                                        <strong>Section:</strong>
                                        {{ $academicInformations->section->name_en ?? 'N/A' }}<br>
                                        <strong>Roll:</strong> {{ $academicInformations->roll ?? 'N/A' }}
                                    </small>
                                @else
                                    <small class="text-muted">No academic info</small>
                                @endif
                            </td>

                            <td>
                                <small>
                                    @if ($student->father_phone)
                                        <i class="fas fa-phone"></i> Father: {{ $student->father_phone }}<br>
                                    @endif
                                    @if ($student->father_email)
                                        <i class="fas fa-envelope"></i> Father: {{ $student->father_email }}<br>
                                    @endif

                                    @if ($student->mother_phone)
                                        <i class="fas fa-phone"></i> Mother: {{ $student->mother_phone }}<br>
                                    @endif
                                    @if ($student->mother_email)
                                        <i class="fas fa-envelope"></i> Mother: {{ $student->mother_email }}<br>
                                    @endif

                                    @if ($student->guardian_phone)
                                        <i class="fas fa-phone"></i> Guardian: {{ $student->guardian_phone }}<br>
                                    @endif
                                    @if ($student->guardian_email)
                                        <i class="fas fa-envelope"></i> Guardian: {{ $student->guardian_email }}
                                    @endif

                                    @if (!$student->father_phone && !$student->father_email && !$student->mother_phone && !$student->mother_email && !$student->guardian_phone && !$student->guardian_email)
                                        <span class="text-muted">No contact</span>
                                    @endif
                                </small>
                            </td>

                            <td>
                                <small>
                                    @if ($student->guardian_type == 1)
                                        <span class="badge badge-info">Father</span>
                                    @elseif($student->guardian_type == 2)
                                        <span class="badge badge-success">Mother</span>
                                    @elseif($student->guardian_type == 3)
                                        <span class="badge badge-warning">Other</span>
                                        @if ($student->guardian_name)
                                            <br>{{ $student->guardian_name }}
                                        @endif
                                    @endif
                                </small>
                            </td>

                            <td class="text-center">
                                <form action="{{ route('students.toggle-status', $student->id) }}" method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $student->id }}" onchange="this.form.submit()"
                                            {{ $student->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $student->id }}">
                                        </label>
                                    </div>
                                </form>
                            </td>

                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info"
                                    title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-dark"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('students.delete', $student->id) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this student?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($students->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                <p>No Students found</p>
                                @if (request()->hasAny([
                                        'search',
                                        'academic_session_id',
                                        'school_class_id',
                                        'section_id',
                                        'group_id',
                                        'phone',
                                        'age_from',
                                        'age_to',
                                        'gender',
                                        'status',
                                        'present_division_id',
                                        'present_district_id',
                                        'present_police_station_id',
                                        'present_post_office_id',
                                    ]))
                                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">
                                        Clear Filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($students->hasPages())
            <div class="px-3 pt-3">
                {{ $students->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
