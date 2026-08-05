@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
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
                    <div class="d-flex justify-content-between align-items-center my-3">
                        <h5 class="font-bold text-lg text-white mb-0">Assign Transport Fee</h5>
                        <button type="button" class="btn btn-success saveTransports">Save Transport Fees</button>
                    </div>
                    <div class="card mb-3 border">
                        <div class="card-body py-3">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="mb-1 font-weight-bold">Bulk Amount (৳)</label>
                                    <input type="number" class="form-control" id="bulkTransportAmount" placeholder="Enter amount" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="mb-1 font-weight-bold">Bulk Status</label>
                                    <select class="form-control" id="bulkTransportStatus">
                                        <option value="">Select status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    <button type="button" class="btn btn-primary" id="applyBulkTransport">
                                        Apply to All Rows
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                This will copy the selected amount and status into every loaded student row.
                            </small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background:#f8fafc">
                                <tr>
                                    <th width="10%">Student ID</th>
                                    <th width="10%">Roll</th>
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
                        <button type="button" class="btn btn-success saveTransports">Save Transport Fees</button>
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

                if (!sessionId) {
                    alert('Please select Academic Session');
                    return;
                }

                $.ajax({
                    url: '{{ route('transports.get-students') }}',
                    method: 'GET',
                    data: {
                        academic_session_id: sessionId,
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
                    const existingInfo = student.existing_transport ?
                        `৳${parseFloat(student.existing_transport.amount).toFixed(2)} (${student.existing_transport.status})` :
                        'None';

                    html += `
                        <tr data-student-id="${student.student_id}">
                            <td>${student.student_cid}</td>
                            <td>${student.roll}</td>
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

            function applyBulkTransportValues() {
                const bulkAmount = $('#bulkTransportAmount').val();
                const bulkStatus = $('#bulkTransportStatus').val();

                if (bulkAmount === '' && !bulkStatus) {
                    return false;
                }

                $('#studentTableBody tr').each(function() {
                    const $row = $(this);

                    if (bulkAmount !== '') {
                        $row.find('.transport-amount').val(bulkAmount);
                    }

                    if (bulkStatus) {
                        $row.find('.transport-status').val(bulkStatus);
                    }
                });

                return true;
            }

            $('#bulkTransportAmount, #bulkTransportStatus').on('change input', function() {
                if ($('#studentListContainer').is(':visible')) {
                    applyBulkTransportValues();
                }
            });

            $('#applyBulkTransport').click(function() {
                const bulkAmount = $('#bulkTransportAmount').val();
                const bulkStatus = $('#bulkTransportStatus').val();

                if (bulkAmount === '' && !bulkStatus) {
                    alert('Please select a bulk amount or status first');
                    return;
                }

                applyBulkTransportValues();
                alert('Bulk values applied to all rows');
            });

            $('.saveTransports').click(function() {
                const sessionId = $('#academic_session_id').val();
                if (!sessionId) {
                    alert('Please fill required fields');
                    return;
                }

                applyBulkTransportValues();

                const bulkAmount = $('#bulkTransportAmount').val();
                const bulkStatus = $('#bulkTransportStatus').val();
                const transports = [];
                $('#studentTableBody tr').each(function() {
                    const studentId = $(this).data('student-id');
                    const amount = $(this).find('.transport-amount').val() || bulkAmount;
                    const status = $(this).find('.transport-status').val() || bulkStatus || 'active';

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
                    url: '{{ route('transports.store-bulk') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        academic_session_id: sessionId,
                        bulk_amount: bulkAmount || null,
                        bulk_status: bulkStatus || null,
                        transports: transports
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.href = '{{ route('transports.index') }}';
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message ||
                        'Something went wrong'));
                    }
                });
            });
        });
    </script>
@endsection
