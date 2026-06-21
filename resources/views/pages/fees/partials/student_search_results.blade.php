@if($students->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="fas fa-search fa-2x mb-2"></i>
        <p class="mb-0">No students found for the selected filters.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0 student-search-table">
            <thead class="thead-light">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Group</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    @php
                        $info = $student->academicInformations->firstWhere('is_current', true) ?? $student->academicInformations->first();
                    @endphp
                    <tr
                        class="student-search-row"
                        role="button"
                        tabindex="0"
                        data-url="{{ route('fees.collect_payment', ['student_id' => $student->id]) }}"
                    >
                        <td>{{ $student->student_cid }}</td>
                        <td>{{ $student->full_name_en }}</td>
                        <td>{{ $info?->schoolClass?->name_en ?? '—' }}</td>
                        <td>{{ $info?->section?->name_en ?? '—' }}</td>
                        <td>{{ $info?->group?->name_en ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
