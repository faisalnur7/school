@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-user-graduate mr-2"></i>Assign Free Studentships
                </h4>
                <a href="{{ route('free-studentships.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('free-studentships.filter')

            <!-- Student List -->
            <div id="studentListContainer" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-lg font-bold">Students List</h5>
                    <button type="button" class="btn btn-success saveFreeStudentships">Save Free Studentships</button>
                </div>
                <div class="card mb-3 border">
                    <div class="card-body py-3">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="mb-1 font-weight-bold">Bulk Type</label>
                                <select class="form-control" id="bulkStudentshipType">
                                    <option value="">Select type</option>
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="mb-1 font-weight-bold">Bulk Value</label>
                                <input type="number" class="form-control" id="bulkStudentshipValue" placeholder="Enter value" step="0.01" min="0">
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="mb-1 font-weight-bold">Bulk Permitted By</label>
                                <input type="text" class="form-control" id="bulkPermittedBy" placeholder="Optional">
                            </div>
                            <div class="col-md-3 text-md-right">
                                <button type="button" class="btn btn-primary" id="applyBulkStudentship">
                                    Apply to All Rows
                                </button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            This will copy the selected type, value, and permitted-by text into every loaded student row.
                        </small>
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
                                <th width="14%">Permitted By</th>
                                <th width="10%">Current Free Studentship</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="text-right mt-3">
                    <button type="button" class="btn btn-success saveFreeStudentships">Save Free Studentships</button>
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

    function notify(message, type = 'info') {
        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](message);
        }
    }

    $('#loadStudents').click(function() {
        const sessionId = $('#academic_session_id').val();
        const feeCategoryId = $('#fee_category_id').val();
        
        if (!sessionId) {
            notify('Please select Academic Session', 'warning');
            return;
        }
        
        if (!feeCategoryId) {
            notify('Please select Fee Category', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("free-studentships.students") }}',
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
                notify('Error loading students', 'error');
            }
        });
    });

    function renderStudentTable(students) {
        let html = '';
        students.forEach(function(student) {
            const existingInfo = student.existing_type 
                ? `${student.existing_type === 'fixed' ? '৳' + student.existing_amount : student.existing_percentage + '%'}${student.existing_permitted_by ? ' · By ' + student.existing_permitted_by : ''}`
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
                        <select class="form-control free-studentship-type">
                            <option value="">No Free Studentship</option>
                            <option value="fixed" ${student.existing_type === 'fixed' ? 'selected' : (student.existing_type ? '' : 'selected')}>Fixed Amount</option>
                            <option value="percentage" ${student.existing_type === 'percentage' ? 'selected' : ''}>Percentage</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control free-studentship-value" 
                               placeholder="Enter value" 
                               value="${student.existing_type === 'fixed' ? student.existing_amount : (student.existing_type === 'percentage' ? student.existing_percentage : '')}"
                               step="0.01" min="0">
                    </td>
                    <td>
                        <input type="text"
                               class="form-control free-studentship-permitted-by"
                               placeholder="Enter permitted by"
                               value="${student.existing_permitted_by || ''}"
                               maxlength="255">
                    </td>
                    <td class="text-center">${existingInfo}</td>
                </tr>
            `;
        });
        $('#studentTableBody').html(html);
    }

    function applyBulkStudentshipValues() {
        const bulkType = $('#bulkStudentshipType').val();
        const bulkValue = $('#bulkStudentshipValue').val();
        const bulkPermittedBy = $('#bulkPermittedBy').val();

        if (!bulkType || bulkValue === '') {
            return false;
        }

        $('#studentTableBody tr').each(function() {
            const $row = $(this);
            $row.find('.free-studentship-type').val(bulkType);
            $row.find('.free-studentship-value').val(bulkValue);
            $row.find('.free-studentship-permitted-by').val(bulkPermittedBy);
        });

        return true;
    }

    $('#bulkStudentshipType, #bulkStudentshipValue, #bulkPermittedBy').on('change input', function() {
        if ($('#studentListContainer').is(':visible')) {
            applyBulkStudentshipValues();
        }
    });

    $('#applyBulkStudentship').click(function() {
        if (!$('#bulkStudentshipType').val()) {
            notify('Please select a bulk type first', 'warning');
            return;
        }

        if ($('#bulkStudentshipValue').val() === '') {
            notify('Please enter a bulk value', 'warning');
            return;
        }

        applyBulkStudentshipValues();

        notify('Bulk values applied to all rows', 'success');
    });

    $('.saveFreeStudentships').click(function() {
        const sessionId = $('#academic_session_id').val();
        const feeCategoryId = $('#fee_category_id').val();
        const bulkType = $('#bulkStudentshipType').val();
        const bulkValue = $('#bulkStudentshipValue').val();
        const bulkPermittedBy = $('#bulkPermittedBy').val();

        if (!sessionId || !feeCategoryId) {
            notify('Please fill required fields', 'warning');
            return;
        }

        applyBulkStudentshipValues();

        const freeStudentships = [];
        $('#studentTableBody tr').each(function() {
            const studentId = $(this).data('student-id');
            const academicInfoId = $(this).data('academic-info-id');
            const rowType = $(this).find('.free-studentship-type').val();
            const rowValue = $(this).find('.free-studentship-value').val();
            const rowPermittedBy = $(this).find('.free-studentship-permitted-by').val();
            const type = bulkType || rowType;
            const value = bulkValue || rowValue;
            const permittedBy = bulkPermittedBy || rowPermittedBy;

            if (type && value) {
                freeStudentships.push({
                    student_id: studentId,
                    academic_info_id: academicInfoId,
                    type: type,
                    amount: type === 'fixed' ? value : null,
                    percentage: type === 'percentage' ? value : null,
                    permitted_by: permittedBy || null
                });
            }
        });

        if (freeStudentships.length === 0) {
            notify('Please assign at least one free studentship', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("free-studentships.storeBulk") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                academic_session_id: sessionId,
                fee_category_id: feeCategoryId,
                bulk_type: bulkType || null,
                bulk_value: bulkValue || null,
                bulk_permitted_by: bulkPermittedBy || null,
                students: freeStudentships
            },
            success: function(response) {
                notify(response.message, 'success');
                window.location.href = '{{ route("free-studentships.index") }}';
            },
            error: function(xhr) {
                notify('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'), 'error');
            }
        });
    });
});
</script>
@endsection
