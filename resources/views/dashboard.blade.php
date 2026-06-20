@extends('layouts.master')

@section('contents')
<div class="dashboard-modern">
    <!-- Quick Actions Bar -->
    <div class="quick-actions-section mb-5">
        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <a href="{{ route('attendance.index') }}" class="quick-action-card attendance">
                    <div class="action-icon"><i class="fas fa-clipboard-check"></i></div>
                    <span class="action-text">Mark Attendance</span>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('fees.collect') }}" class="quick-action-card fees">
                    <div class="action-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <span class="action-text">Collect Fees</span>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('students.create') }}" class="quick-action-card student">
                    <div class="action-icon"><i class="fas fa-user-plus"></i></div>
                    <span class="action-text">Add Student</span>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('accounts.hub') }}" class="quick-action-card reports">
                    <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                    <span class="action-text">View Reports</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="stats-grid mb-4">
        <!-- Students Card -->
        <div class="stat-card-modern students">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">Total Students</span>
                    <span class="stat-value">{{ $total_students }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Teachers Card -->
        <div class="stat-card-modern teachers">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">Total Teachers</span>
                    <span class="stat-value">{{ $total_teachers }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-chalkboard-user"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Staff Card -->
        <div class="stat-card-modern staff">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">Total Staff</span>
                    <span class="stat-value">{{ $total_staff }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-briefcase"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Classes Card -->
        <div class="stat-card-modern classes">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">Total Classes</span>
                    <span class="stat-value">{{ $total_classes }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-school"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>
    </div>

    <!-- Attendance Section -->
    @if(auth()->user()?->hasAnyPermission(['view_todays_attendence', 'view_fee_collection']))
    <div class="row mb-4">
        @if(auth()->user()?->hasPermission('view_todays_attendence'))
        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card-modern h-100">
                <div class="card-header-modern">
                    <span><i class="fas fa-clipboard-check me-2"></i>Today's Attendance</span>
                </div>
                <div class="card-body-modern">
                    <div class="attend-grid">
                        <div class="attend-item present">
                            <span class="attend-num">{{ $today_present }}</span>
                            <span class="attend-label">Present</span>
                        </div>
                        <div class="attend-item absent">
                            <span class="attend-num">{{ $today_absent }}</span>
                            <span class="attend-label">Absent</span>
                        </div>
                        <div class="attend-item leave">
                            <span class="attend-num">{{ $today_leave }}</span>
                            <span class="attend-label">Leave</span>
                        </div>
                    </div>
                    <div class="progress-section">
                        <div class="progress-header">
                            <span>Attendance Rate</span>
                            <strong>{{ $attendance_percentage }}%</strong>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern" style="width: {{ $attendance_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Fee Statistics -->
        @if(auth()->user()?->hasPermission('view_fee_collection'))
        <div class="col-12 col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header-modern fee-header">
                    <span><i class="fas fa-coins me-2"></i>Fee Collection Status</span>
                </div>
                <div class="card-body-modern">
                    <div class="attend-grid">
                        <div class="attend-item collected">
                            <span class="attend-num">৳{{ number_format($total_fees_paid, 0) }}</span>
                            <span class="attend-label">Collected</span>
                        </div>
                        <div class="attend-item pending">
                            <span class="attend-num">৳{{ number_format($total_fees_pending, 0) }}</span>
                            <span class="attend-label">Pending</span>
                        </div>
                        <div class="attend-item due">
                            <span class="attend-num">৳{{ number_format($total_fees_due, 0) }}</span>
                            <span class="attend-label">Total Due</span>
                        </div>
                    </div>
                    <div class="progress-section">
                        <div class="progress-header">
                            <span>Collection Rate</span>
                            <strong>{{ $fee_collection_rate }}%</strong>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-fee" style="width: {{ $fee_collection_rate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Financial Overview -->
    @if(auth()->user()?->hasPermission('view_income_expense'))
    <div class="finance-grid mb-4">
        <div class="finance-card income">
            <span class="finance-label">Total Income</span>
            <span class="finance-value">৳{{ number_format($total_income, 0) }}</span>
        </div>
        <div class="finance-card expense">
            <span class="finance-label">Total Expense</span>
            <span class="finance-value">৳{{ number_format($total_expense, 0) }}</span>
        </div>
        <div class="finance-card balance {{ $net_balance >= 0 ? 'positive' : 'negative' }}">
            <span class="finance-label">Net Balance</span>
            <span class="finance-value">৳{{ number_format($net_balance, 0) }}</span>
        </div>
    </div>
    @endif

    <!-- Assets Card -->
    @if(auth()->user()?->hasPermission('view_asset'))
    <div class="asset-card mb-4">
        <span class="asset-label">Total Assets</span>
        <span class="asset-value">{{ $total_assets }}</span>
    </div>
    @endif

    <!-- Classwise Attendance Table -->
    @if(auth()->user()?->hasPermission('view_classwise_attendance'))
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <span><i class="fas fa-list me-2"></i>Today's Classwise Attendance</span>
        </div>
        <div class="card-body-modern p-0">
            <div class="table-responsive-modern">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Attendance %</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classwise_attendance as $attendance)
                            <tr>
                                <td class="fw-bold">{{ $attendance['class'] }}</td>
                                <td>{{ $attendance['percentage'] }}%</td>
                                <td>
                                    <div class="status-bar-wrap">
                                        <div class="status-bar" style="width: {{ $attendance['percentage'] }}%; background: {{ $attendance['percentage'] >= 80 ? 'linear-gradient(90deg, #22c55e, #16a34a)' : ($attendance['percentage'] >= 60 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)') }}"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No attendance data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Data Section -->
    @if(auth()->user()?->hasAnyPermission(['view_recent_exam', 'view_recent_notice']))
    <div class="row g-3">
        @if(auth()->user()?->hasPermission('view_recent_exam'))
        <div class="col-12 col-lg-12">
            <div class="card-modern h-100">
                <div class="card-header-modern exam-header">
                    <span><i class="fas fa-file-alt me-2"></i>Recent Exams</span>
                </div>
                <div class="card-body-modern recent-list">
                    @forelse($recent_exams as $exam)
                        <div class="recent-item">
                            <div class="recent-content">
                                <strong>{{ $exam->name ?? 'Exam' }}</strong>
                                <small>{{ $exam->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No exams scheduled</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- @if(auth()->user()?->hasPermission('view_recent_notice'))
        <div class="col-12 col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header-modern notice-header">
                    <span><i class="fas fa-bullhorn me-2"></i>Recent Notices</span>
                </div>
                <div class="card-body-modern recent-list">
                    @forelse($recent_notices as $notice)
                        <div class="recent-item">
                            <div class="recent-content">
                                <strong>{{ $notice->title ?? 'Notice' }}</strong>
                                <small>{{ $notice->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No notices available</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif --}}
    </div>
    @endif
</div>

<style>
/* =====================================================
   MODERN DASHBOARD STYLES
   ===================================================== */
.dashboard-modern {
    padding: 20px;
    width: 100%;
}

/* Quick Actions Section */
.quick-actions-section {
    margin-bottom: 24px;
}

.quick-actions-section .row {
    row-gap: 18px;
}

.quick-actions-section [class*="col-"] {
    display: flex;
}

.quick-action-card {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(120deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0.1) 100%);
    transition: left 0.5s ease;
}

.quick-action-card:hover::before {
    left: 100%;
}

.quick-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    color: #fff;
}

.quick-action-card.attendance { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.quick-action-card.fees { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.quick-action-card.student { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
.quick-action-card.reports { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

.action-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    font-size: 1.25rem;
}

.action-text {
    font-size: 0.95rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

@media (max-width: 991px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-card-modern {
    position: relative;
    padding: 20px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.stat-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-info { display: flex; flex-direction: column; gap: 4px; }
.stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }
.stat-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }

.stat-icon-wrap {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.25rem;
}

.stat-card-modern.students .stat-icon-wrap { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.stat-card-modern.teachers .stat-icon-wrap { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5; }
.stat-card-modern.staff .stat-icon-wrap { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.stat-card-modern.classes .stat-icon-wrap { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; }

.stat-glow {
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    opacity: 0.1;
}

.stat-card-modern.students .stat-glow { background: #3b82f6; }
.stat-card-modern.teachers .stat-glow { background: #4f46e5; }
.stat-card-modern.staff .stat-glow { background: #f59e0b; }
.stat-card-modern.classes .stat-glow { background: #10b981; }

/* Card Modern */
.card-modern {
    border-radius: 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card-modern:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.card-header-modern {
    padding: 16px 20px;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.card-header-modern.fee-header { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
.card-header-modern.exam-header { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); }
.card-header-modern.notice-header { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); }

.card-body-modern {
    padding: 20px;
}

/* Attendance Grid */
.attend-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.attend-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 16px 12px;
    border-radius: 12px;
    background: #f8fafc;
}

.attend-num { font-size: 1.5rem; font-weight: 700; }
.attend-label { font-size: 0.75rem; color: #64748b; font-weight: 500; }

.attend-item.present { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
.attend-item.present .attend-num { color: #16a34a; }

.attend-item.absent { background: linear-gradient(135deg, #fee2e2, #fecaca); }
.attend-item.absent .attend-num { color: #dc2626; }

.attend-item.leave { background: linear-gradient(135deg, #fef3c7, #fde68a); }
.attend-item.leave .attend-num { color: #d97706; }

.attend-item.collected { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
.attend-item.collected .attend-num { color: #16a34a; }

.attend-item.pending { background: linear-gradient(135deg, #fee2e2, #fecaca); }
.attend-item.pending .attend-num { color: #dc2626; }

.attend-item.due { background: linear-gradient(135deg, #fef3c7, #fde68a); }
.attend-item.due .attend-num { color: #d97706; }

/* Progress Section */
.progress-section { margin-top: 8px; }
.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.85rem;
    color: #475569;
}

.progress-modern {
    height: 10px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-modern {
    height: 100%;
    background: linear-gradient(90deg, #22c55e, #16a34a);
    border-radius: 10px;
    transition: width 0.5s ease;
}

.progress-bar-fee {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Finance Grid */
.finance-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

@media (max-width: 767px) { .finance-grid { grid-template-columns: 1fr; } }

.finance-card {
    padding: 20px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.finance-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }
.finance-value { font-size: 1.5rem; font-weight: 700; }

.finance-card.income .finance-value { color: #16a34a; }
.finance-card.expense .finance-value { color: #dc2626; }
.finance-card.balance.positive .finance-value { color: #16a34a; }
.finance-card.balance.negative .finance-value { color: #dc2626; }

/* Asset Card */
.asset-card {
    padding: 20px;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.asset-label { font-size: 0.9rem; font-weight: 500; }
.asset-value { font-size: 1.75rem; font-weight: 700; }

/* Table Modern */
.table-responsive-modern { overflow-x: auto; }
.table-modern {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.table-modern th {
    padding: 14px 16px;
    background: #f8fafc;
    font-size: 0.8rem;
    font-weight: 600;
    color: #475569;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.table-modern td {
    padding: 14px 16px;
    font-size: 0.85rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

.table-modern tbody tr:hover { background: #f8fafc; }

.status-bar-wrap {
    width: 100%;
    max-width: 120px;
    height: 8px;
    background: #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.status-bar {
    height: 100%;
    border-radius: 8px;
    transition: width 0.5s ease;
}

/* Recent List */
.recent-list {
    max-height: 280px;
    overflow-y: auto;
}

.recent-item {
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.recent-item:last-child { border-bottom: none; }

.recent-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.recent-content strong { font-size: 0.9rem; color: #1e293b; }
.recent-content small { font-size: 0.75rem; color: #94a3b8; }

/* Responsive adjustments */
@media (max-width: 575px) {
    .dashboard-modern { padding: 12px; }
    .quick-action-card { padding: 14px 16px; gap: 10px; }
    .action-icon { width: 36px; height: 36px; font-size: 1rem; }
    .action-text { font-size: 0.8rem; }
    .stat-value { font-size: 1.5rem; }
    .attend-num { font-size: 1.25rem; }
}
</style>
@endsection
