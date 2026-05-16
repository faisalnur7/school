@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">🚌 Assign Transport Fee</h4>
            <a href="{{ route('transports.index') }}" class="btn btn-secondary">
                ← Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-filter mr-2"></i>Filter Students
                    </h4>
                    <a href="{{ route('transports.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('transports.filter')

                <!-- Student List -->
                <div id="studentListContainer" style="display: none;">
                    <h5 class="mb-3">Students List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background:#f8fafc">
                                <tr>
                                    <th width="10%">Student ID</th>
                                    <th width="25%">Student Name</th>
                                    <th width="15%">Amount (৳)</th>
                                    <th width="15%">Status</th>
                                    <th width="20%">Current Transport</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right mt-3">
                        <button type="button" id="saveTransports" class="btn btn-success">Save Transport Fees</button>
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
                    url: '{{ route("transports.get-students") }}',
                    method: 'GET',
                    data: {
                        academic_session_id: sessionId,
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
                    const existingInfo = student.existing_transport 
                        ? `৳${parseFloat(student.existing_transport.amount).toFixed(2)} (${student.existing_transport.status})`
                        : 'None';

                    html += `
                        <tr data-student-id="${student.student_id}">
                            <td>${student.student_cid}</td>
                            <td>${student.name}</td>
                            <td>
                                <input type="number" class="form-control transport-amount" 
                                       placeholder="0.00" 
                                       value="${student.existing_transport ? student.existing_transport.amount : ''}"
                                       step="0.01" min="0">
                            </td>
                            <td>
                                <select class="form-control transport-status">
                                    <option value="active" ${student.existing_transport?.status === 'active' ? 'selected' : 'selected'}>Active</option>
                                    <option value="inactive" ${student.existing_transport?.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </td>
                            <td class="text-center">${existingInfo}</td>
                        </tr>
                    `;
                });
                $('#studentTableBody').html(html);
            }

            $('#saveTransports').click(function() {
                const sessionId = $('#academic_session_id').val();
                const feeCategoryId = $('#fee_category_id').val();

                if (!sessionId || !feeCategoryId) {
                    alert('Please fill required fields');
                    return;
                }

                const transports = [];
                $('#studentTableBody tr').each(function() {
                    const studentId = $(this).data('student-id');
                    const amount = $(this).find('.transport-amount').val();
                    const status = $(this).find('.transport-status').val();

                    if (amount && parseFloat(amount) > 0) {
                        transports.push({
                            student_id: studentId,
                            amount: amount,
                            status: status
                        });
                    }
                });

                if (transports.length === 0) {
                    alert('Please assign at least one transport fee with valid amount');
                    return;
                }

                $.ajax({
                    url: '{{ route("transports.store-bulk") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        academic_session_id: sessionId,
                        fee_category_id: feeCategoryId,
                        transports: transports
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.href = '{{ route("transports.index") }}';
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            });
        });
    </script>
@endsection
