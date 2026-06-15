@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-award mr-2"></i>Assign Scholarships
                </h4>
                <a href="{{ route('scholarships.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('scholarships.filter')

            <!-- Student List -->
            <div id="studentListContainer" style="display: none;">
                <div class="flex justify-between">
                <h5 class="mb-3 text-lg font-bold">Students List</h5>
                <div class="text-right mb-3">
                    <button type="button" class="btn btn-success saveScholarships">Save Scholarships</button>
                </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="8%">Roll</th>
                                <th width="10%">Student ID</th>
                                <th width="10%">Student Name</th>
                                <th width="10%">Academic Info</th>
                                <th width="10%">Fee Amount</th>
                                <th width="27%">Type</th>
                                <th width="10%">Value</th>
                                <th width="10%">Current Scholarship</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="text-right mt-3">
                    <button type="button" class="btn btn-success saveScholarships">Save Scholarships</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@include('scripts.common.load_academic_information')

<script>
$(document).ready(function() {
    let studentsData = [];

    $('#loadStudents').click(function() {
        const sessionId = $('#academic_session_id').val();
        const feeCategoryId = $('#fee_category_id').val();
        
        if (!sessionId) {
            alert('Please select Academic Session');
            return;
        }
        
        if (!feeCategoryId) {
            alert('Please select Fee Category');
            return;
        }

        $.ajax({
            url: '{{ route("scholarships.students") }}',
            method: 'GET',
            data: {
                academic_session_id: sessionId,
                student_cid: $('#studentCid').val(),
                fee_category_id: feeCategoryId,
                school_class_id: $('#classSelect').val(),
                section_id: $('#sectionSelect').val(),
                group_id: $('#groupSelect').val()
            },
            success: function(students) {
                studentsData = students;
                renderStudentTable(students);
                $('#studentListContainer').show();
            },
            error: function() {
                alert('Error loading students');
            }
        });
    });

    function renderStudentTable(students) {
        let html = '';
        students.forEach(function(student) {
            const existingInfo = student.existing_type 
                ? `${student.existing_type === 'fixed' ? '৳' + student.existing_amount : student.existing_percentage + '%'}`
                : 'None';
            
            const feeAmount = student.fee_category_amount ? '৳' + parseFloat(student.fee_category_amount).toFixed(2) : 'N/A';

            html += `
                <tr data-student-id="${student.id}" data-academic-info-id="${student.academic_info_id}">
                    <td>${student.roll || '—'}</td>
                    <td>${student.student_cid}</td>
                    <td>
                        <strong>${student.name}</strong>
                        <br><small class="text-muted">Father: ${student.father_name || '—'}</small>
                        <br><small class="text-muted">Mother: ${student.mother_name || '—'}</small>
                    </td>
                    <td>
                        <small>
                            <strong>Class:</strong> ${student.class || '—'}<br>
                            <strong>Section:</strong> ${student.section || '—'}<br>
                            <strong>Group:</strong> ${student.group || '—'}<br>
                            <strong>Session:</strong> ${student.academic_session || '—'}
                        </small>
                    </td>
                    <td><strong>${feeAmount}</strong></td>
                    <td>
                        <select class="form-control scholarship-type">
                            <option value="">No Scholarship</option>
                            <option value="fixed" ${student.existing_type === 'fixed' ? 'selected' : (student.existing_type ? '' : 'selected')}>Fixed Amount</option>
                            <option value="percentage" ${student.existing_type === 'percentage' ? 'selected' : ''}>Percentage</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control scholarship-value" 
                               placeholder="Enter value" 
                               value="${student.existing_type === 'fixed' ? student.existing_amount : (student.existing_type === 'percentage' ? student.existing_percentage : '')}"
                               step="0.01" min="0">
                    </td>
                    <td class="text-center">${existingInfo}</td>
                </tr>
            `;
        });
        $('#studentTableBody').html(html);
    }

    $('.saveScholarships').click(function() {
        const sessionId = $('#academic_session_id').val();
        const feeCategoryId = $('#fee_category_id').val();

        if (!sessionId || !feeCategoryId) {
            alert('Please fill required fields');
            return;
        }

        const scholarships = [];
        $('#studentTableBody tr').each(function() {
            const studentId = $(this).data('student-id');
            const academicInfoId = $(this).data('academic-info-id');
            const type = $(this).find('.scholarship-type').val();
            const value = $(this).find('.scholarship-value').val();

            if (type && value) {
                scholarships.push({
                    student_id: studentId,
                    academic_info_id: academicInfoId,
                    type: type,
                    amount: type === 'fixed' ? value : null,
                    percentage: type === 'percentage' ? value : null
                });
            }
        });

        if (scholarships.length === 0) {
            alert('Please assign at least one scholarship');
            return;
        }

        $.ajax({
            url: '{{ route("scholarships.storeBulk") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                academic_session_id: sessionId,
                fee_category_id: feeCategoryId,
                students: scholarships
            },
            success: function(response) {
                alert(response.message);
                window.location.href = '{{ route("scholarships.index") }}';
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    });
});
</script>
@endsection
