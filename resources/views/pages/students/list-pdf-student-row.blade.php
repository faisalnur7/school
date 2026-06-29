<tr>
    <td>{{ $rowNumber }}</td>
    @foreach($selectedColumns as $column)
        <td>
            @switch($column)
                @case('student_cid')
                    {{ $student->student_cid ?? '—' }}
                    @break

                @case('roll')
                    {{ $academicInformation?->roll ?? '—' }}
                    @break

                @case('full_name_en')
                    <strong>{{ $student->full_name_en ?? '—' }}</strong>
                    @break

                @case('full_name_bn')
                    {{ $student->full_name_bn ?? '—' }}
                    @break

                @case('class')
                    {{ $academicInformation?->schoolClass?->name_en ?? '—' }}
                    @break

                @case('section')
                    {{ $academicInformation?->section?->name_en ?? '—' }}
                    @break

                @case('group')
                    {{ $academicInformation?->group?->name_en ?? '—' }}
                    @break

                @case('gender')
                    {{ $student->gender_text ?? '—' }}
                    @break

                @case('religion')
                    {{ $student->religion_text ?? '—' }}
                    @break

                @case('date_of_birth')
                    {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '—' }}
                    @break

                @case('blood_group')
                    {{ $student->blood_group_text ?? '—' }}
                    @break

                @case('father_name')
                    {{ $student->father_name ?? '—' }}
                    @break

                @case('mother_name')
                    {{ $student->mother_name ?? '—' }}
                    @break

                @case('father_phone')
                    {{ $student->father_phone ?? '—' }}
                    @break

                @case('mother_phone')
                    {{ $student->mother_phone ?? '—' }}
                    @break

                @case('guardian_phone')
                    {{ $student->guardian_phone ?? '—' }}
                    @break

                @case('present_address')
                    {{ $student->present_address ?? '—' }}
                    @break

                @case('status')
                    {{ $student->status ? 'Active' : 'Inactive' }}
                    @break

                @default
                    —
            @endswitch
        </td>
    @endforeach
</tr>
