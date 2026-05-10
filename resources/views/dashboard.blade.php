@extends('layouts.master')

@section('contents')
<div class="dashboard-container">
    <!-- Quick Actions Bar -->
    <div class="quick-actions-bar mb-4">
        <div class="row g-2">
            <div class="col-12 col-sm-6 col-md-3">
                <a href="{{ route('attendance.index') }}" class="quick-action-btn btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="d-none d-sm-inline">Mark Attendance</span>
                    <span class="d-sm-none">Attendance</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <a href="{{ route('fees.collect') }}" class="quick-action-btn btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="d-none d-sm-inline">Collect Fees</span>
                    <span class="d-sm-none">Fees</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <a href="{{ route('students.create') }}" class="quick-action-btn btn btn-info btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <span class="d-none d-sm-inline">Add Student</span>
                    <span class="d-sm-none">Student</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <a href="{{ route('reports.index') }}" class="quick-action-btn btn btn-warning btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-chart-bar"></i>
                    <span class="d-none d-sm-inline">View Reports</span>
                    <span class="d-sm-none">Reports</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="row mb-4">
        <!-- Students Card -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 stat-label">Total Students</p>
                            <h3 class="mb-0 stat-number">{{ $total_students }}</h3>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Card -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 stat-label">Total Teachers</p>
                            <h3 class="mb-0 stat-number">{{ $total_teachers }}</h3>
                        </div>
                        <div class="stat-icon bg-info">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 stat-label">Total Staff</p>
                            <h3 class="mb-0 stat-number">{{ $total_staff }}</h3>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Card -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 stat-label">Total Classes</p>
                            <h3 class="mb-0 stat-number">{{ $total_classes }}</h3>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Section -->
    <div class="row mb-4">
        <div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Today's Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="attendance-number present">{{ $today_present }}</div>
                                <p class="text-muted mb-0 attendance-label">Present</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="attendance-number absent">{{ $today_absent }}</div>
                                <p class="text-muted mb-0 attendance-label">Absent</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="attendance-stat">
                                <div class="attendance-number leave">{{ $today_leave }}</div>
                                <p class="text-muted mb-0 attendance-label">Leave</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Attendance Rate</span>
                            <strong>{{ $attendance_percentage }}%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendance_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Statistics -->
        <div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Fee Collection Status</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fee-stat">
                                <div class="fee-amount">৳{{ number_format($total_fees_paid, 0) }}</div>
                                <p class="text-muted mb-0 fee-label">Collected</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fee-stat">
                                <div class="fee-amount pending">৳{{ number_format($total_fees_pending, 0) }}</div>
                                <p class="text-muted mb-0 fee-label">Pending</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fee-stat">
                                <div class="fee-amount">৳{{ number_format($total_fees_due, 0) }}</div>
                                <p class="text-muted mb-0 fee-label">Total Due</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Collection Rate</span>
                            <strong>{{ $fee_collection_rate }}%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $fee_collection_rate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 financial-label">Total Income</p>
                    <h4 class="text-success mb-0 financial-amount">৳{{ number_format($total_income, 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 financial-label">Total Expense</p>
                    <h4 class="text-danger mb-0 financial-amount">৳{{ number_format($total_expense, 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 financial-label">Net Balance</p>
                    <h4 class="mb-0 financial-amount" style="color: {{ $net_balance >= 0 ? '#28a745' : '#dc3545' }}">
                        ৳{{ number_format($net_balance, 0) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Assets</p>
                    <h4 class="mb-0">{{ $total_assets }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Classwise Attendance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Today's Classwise Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Class</th>
                                    <th>Attendance %</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classwise_attendance as $attendance)
                                    <tr>
                                        <td><strong>{{ $attendance['class'] }}</strong></td>
                                        <td>{{ $attendance['percentage'] }}%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" 
                                                    style="width: {{ $attendance['percentage'] }}%; background-color: {{ $attendance['percentage'] >= 80 ? '#28a745' : ($attendance['percentage'] >= 60 ? '#ffc107' : '#dc3545') }}">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No attendance data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Section -->
    <div class="row">
        <!-- Recent Exams -->
        <div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Exams</h5>
                </div>
                <div class="card-body recent-items">
                    @forelse($recent_exams as $exam)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>{{ $exam->name ?? 'Exam' }}</strong></p>
                                <small class="text-muted">{{ $exam->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No exams scheduled</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Notices -->
        <div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Notices</h5>
                </div>
                <div class="card-body recent-items">
                    @forelse($recent_notices as $notice)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>{{ $notice->title ?? 'Notice' }}</strong></p>
                                <small class="text-muted">{{ $notice->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No notices available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    .dashboard-container {
        padding: 15px;
        width: 100%;
        max-width: 100%;
    }

    /* Mobile First Approach */
    @media (max-width: 576px) {
        .dashboard-container {
            padding: 10px;
        }

        .quick-actions-bar {
            margin-bottom: 20px;
        }

        .quick-action-btn {
            padding: 12px 8px !important;
            font-size: 0.85rem;
            min-height: 50px;
        }

        .stat-card {
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 0.75rem;
        }

        .stat-number {
            font-size: 1.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .attendance-number {
            font-size: 24px;
        }

        .attendance-label {
            font-size: 0.75rem;
        }

        .fee-amount {
            font-size: 18px;
        }

        .fee-label {
            font-size: 0.75rem;
        }

        .financial-label {
            font-size: 0.75rem;
        }

        .financial-amount {
            font-size: 1.25rem;
        }

        .card {
            margin-bottom: 12px;
        }

        .table {
            font-size: 0.85rem;
        }

        .table th, .table td {
            padding: 0.5rem;
        }

        .recent-items {
            max-height: 300px;
            overflow-y: auto;
        }
    }

    /* Tablet */
    @media (min-width: 577px) and (max-width: 991px) {
        .dashboard-container {
            padding: 15px;
        }

        .quick-action-btn {
            padding: 15px 10px !important;
            font-size: 0.9rem;
        }

        .stat-label {
            font-size: 0.8rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .attendance-number {
            font-size: 28px;
        }

        .fee-amount {
            font-size: 20px;
        }

        .financial-amount {
            font-size: 1.5rem;
        }
    }

    /* Desktop */
    @media (min-width: 992px) {
        .dashboard-container {
            padding: 20px;
        }

        .quick-action-btn {
            padding: 15px 20px !important;
            font-size: 1rem;
        }

        .stat-label {
            font-size: 0.85rem;
        }

        .stat-number {
            font-size: 2.5rem;
        }

        .attendance-number {
            font-size: 32px;
        }

        .fee-amount {
            font-size: 24px;
        }

        .financial-amount {
            font-size: 1.75rem;
        }
    }

    /* Common Styles */
    .quick-actions-bar {
        margin-bottom: 30px;
    }

    .quick-action-btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }

    .stat-icon {
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary {
        background-color: #007bff;
    }

    .stat-icon.bg-info {
        background-color: #17a2b8;
    }

    .stat-icon.bg-warning {
        background-color: #ffc107;
        color: #333;
    }

    .stat-icon.bg-success {
        background-color: #28a745;
    }

    .attendance-stat {
        padding: 10px 5px;
    }

    .attendance-number {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .attendance-number.present {
        color: #28a745;
    }

    .attendance-number.absent {
        color: #dc3545;
    }

    .attendance-number.leave {
        color: #ffc107;
    }

    .fee-stat {
        padding: 10px 5px;
    }

    .fee-amount {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .fee-amount {
        color: #28a745;
    }

    .fee-amount.pending {
        color: #dc3545;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0;
        background-color: #343a40 !important;
    }

    .card-header h5 {
        color: white !important;
    }

    .card-header .mb-0 {
        color: white !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table-responsive {
        border-radius: 8px;
    }

    .progress {
        border-radius: 4px;
    }

    /* Ensure proper spacing on all screen sizes */
    .row {
        margin-right: -7.5px;
        margin-left: -7.5px;
    }

    .row > * {
        padding-right: 7.5px;
        padding-left: 7.5px;
    }
</style>
@endsection
