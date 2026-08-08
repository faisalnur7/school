@extends('layouts.master')

@section('contents')
@php
    $total_students = $total_students ?? 0;
    $total_teachers = $total_teachers ?? 0;
    $total_staff = $total_staff ?? 0;
    $total_classes = $total_classes ?? 0;
    $today_present = $today_present ?? 0;
    $today_absent = $today_absent ?? 0;
    $today_leave = $today_leave ?? 0;
    $attendance_percentage = $attendance_percentage ?? 0;
    $total_fees_due = $total_fees_due ?? 0;
    $total_fees_paid = $total_fees_paid ?? 0;
    $total_fees_pending = $total_fees_pending ?? 0;
    $fee_collection_rate = $fee_collection_rate ?? 0;
    $total_income = $total_income ?? 0;
    $total_expense = $total_expense ?? 0;
    $net_balance = $net_balance ?? 0;
    $classwise_attendance = $classwise_attendance ?? [];
    $recent_exams = $recent_exams ?? collect();
    $recent_notices = $recent_notices ?? collect();
    $monthly_attendance = $monthly_attendance ?? ['days' => [], 'percentages' => []];
    $fee_trend = $fee_trend ?? ['months' => [], 'amounts' => []];
    $income_expense = $income_expense ?? ['months' => [], 'incomes' => [], 'expenses' => []];
    $student_distribution = $student_distribution ?? [];
    $monthly_fee_collection = $monthly_fee_collection ?? [];
    $upcoming_birthdays = $upcoming_birthdays ?? [];
@endphp
<div class="dashboard-modern">
    <!-- Quick Actions Bar -->
    @php
        $quickActionCards = [
            'mark_attendance' => auth()->user()?->hasPermission('view_card_mark_attendance'),
            'collect_fees' => auth()->user()?->hasPermission('view_card_collect_fees'),
            'add_student' => auth()->user()?->hasPermission('view_card_add_student'),
            'view_reports' => auth()->user()?->hasPermission('view_card_view_reports'),
        ];
    @endphp
    @if(in_array(true, $quickActionCards, true))
    <div class="quick-actions-section mb-5">
        <div class="row g-4">
            @if($quickActionCards['mark_attendance'])
                <div class="col-6 col-lg-3">
                    <a href="{{ route('attendance.index') }}" class="quick-action-card attendance">
                        <div class="action-icon"><i class="fas fa-clipboard-check"></i></div>
                        <span class="action-text">{{ __('Mark Attendance') }}</span>
                    </a>
                </div>
            @endif
            @if($quickActionCards['collect_fees'])
                <div class="col-6 col-lg-3">
                    <a href="{{ route('fees.collect') }}" class="quick-action-card fees">
                        <div class="action-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <span class="action-text">{{ __('Collect Fees') }}</span>
                    </a>
                </div>
            @endif
            @if($quickActionCards['add_student'])
                <div class="col-6 col-lg-3">
                    <a href="{{ route('students.admission') }}" class="quick-action-card student">
                        <div class="action-icon"><i class="fas fa-user-plus"></i></div>
                        <span class="action-text">{{ __('Add Student') }}</span>
                    </a>
                </div>
            @endif
            @if($quickActionCards['view_reports'])
                <div class="col-6 col-lg-3">
                    <a href="{{ route('accounts.hub') }}" class="quick-action-card reports">
                        <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                        <span class="action-text">{{ __('View Reports') }}</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Key Statistics Cards -->
    @if(auth()->user()?->hasPermission('view_card_dashboard'))
    <div class="stats-grid mb-4">
        <!-- Students Card -->
        <div class="stat-card-modern students">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">{{ __('Total Students') }}</span>
                    <span class="stat-value">{{ localized_number($total_students) }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Teachers Card -->
        <div class="stat-card-modern teachers">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">{{ __('Total Teachers') }}</span>
                    <span class="stat-value">{{ localized_number($total_teachers) }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Staff Card -->
        <div class="stat-card-modern staff">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">{{ __('Total Staff') }}</span>
                    <span class="stat-value">{{ localized_number($total_staff) }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-briefcase"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>

        <!-- Classes Card -->
        <div class="stat-card-modern classes">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">{{ __('Total Classes') }}</span>
                    <span class="stat-value">{{ localized_number($total_classes) }}</span>
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-school"></i></div>
            </div>
            <div class="stat-glow"></div>
        </div>
    </div>
    @endif

    <!-- Financial Overview -->
    @if(auth()->user()?->hasPermission('view_income_expense'))
    <div class="finance-grid mb-4">
        <div class="finance-card income">
            <span class="finance-label">{{ __('Total Income') }}</span>
            <span class="finance-value">{{ localized_currency($total_income, '৳', 0) }}</span>
        </div>
        <div class="finance-card expense">
            <span class="finance-label">{{ __('Total Expense') }}</span>
            <span class="finance-value">{{ localized_currency($total_expense, '৳', 0) }}</span>
        </div>
        <div class="finance-card balance {{ $net_balance >= 0 ? 'positive' : 'negative' }}">
            <span class="finance-label">{{ __('Net Balance') }}</span>
            <span class="finance-value">{{ localized_currency($net_balance, '৳', 0) }}</span>
        </div>
    </div>
    @endif

    <!-- Attendance Section -->
    @if(auth()->user()?->hasAnyPermission(['view_todays_attendence', 'view_fee_collection']))
    <div class="row mb-4">
        @if(auth()->user()?->hasPermission('view_todays_attendence'))
        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card-modern h-100">
                <div class="card-header-modern">
                    <span><i class="fas fa-clipboard-check me-2"></i>{{ __("Today's Attendance") }}</span>
                </div>
                <div class="card-body-modern">
                    <div class="attend-grid">
                        <div class="attend-item present">
                            <span class="attend-num">{{ localized_number($today_present) }}</span>
                            <span class="attend-label">{{ __('Present') }}</span>
                        </div>
                        <div class="attend-item absent">
                            <span class="attend-num">{{ localized_number($today_absent) }}</span>
                            <span class="attend-label">{{ __('Absent') }}</span>
                        </div>
                        <div class="attend-item leave">
                            <span class="attend-num">{{ localized_number($today_leave) }}</span>
                            <span class="attend-label">{{ __('Leave') }}</span>
                        </div>
                    </div>
                    <div class="progress-section">
                        <div class="progress-header">
                            <span>{{ __('Attendance Rate') }}</span>
                            <strong>{{ localized_number($attendance_percentage) }}%</strong>
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
                    <span><i class="fas fa-coins me-2"></i>{{ __('Fee Collection Status') }}</span>
                </div>
                <div class="card-body-modern">
                    <div class="attend-grid">
                        <div class="attend-item collected">
                            <span class="attend-num">{{ localized_currency($total_fees_paid, '৳', 0) }}</span>
                            <span class="attend-label">{{ __('Collected') }}</span>
                        </div>
                        <div class="attend-item pending">
                            <span class="attend-num">{{ localized_currency($total_fees_pending, '৳', 0) }}</span>
                            <span class="attend-label">{{ __('Pending') }}</span>
                        </div>
                        <div class="attend-item due">
                            <span class="attend-num">{{ localized_currency($total_fees_due, '৳', 0) }}</span>
                            <span class="attend-label">{{ __('Total Due') }}</span>
                        </div>
                    </div>
                    <div class="progress-section">
                        <div class="progress-header">
                            <span>{{ __('Collection Rate') }}</span>
                            <strong>{{ localized_number($fee_collection_rate) }}%</strong>
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

    <!-- Attendance, Birthdays and Recent Exams -->
    @if(auth()->user()?->hasAnyPermission(['view_classwise_attendance', 'view_recent_exam', 'view_card_student_birthdays']))
    <div class="row g-3 dashboard-triple-row">
        @if(auth()->user()?->hasPermission('view_classwise_attendance'))
        <div class="col-12 col-lg-4">
            <div class="card-modern dashboard-triple-card">
                <div class="card-header-modern">
                    <span><i class="fas fa-list me-2"></i>{{ __("Today's Classwise Attendance") }}</span>
                </div>
                <div class="card-body-modern dashboard-triple-body p-0">
                    <div class="table-responsive-modern">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('Class') }}</th>
                                    <th>{{ __('Attendance %') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                        <tbody>
                            @forelse($classwise_attendance as $attendance)
                                    <tr @if(!empty($attendance['link'])) class="clickable-dashboard-row" data-href="{{ $attendance['link'] }}" role="link" tabindex="0" @endif>
                                        <td class="fw-bold">{{ $attendance['class'] }}</td>
                                        <td>{{ localized_number($attendance['percentage']) }}%</td>
                                        <td>
                                            <div class="status-bar-wrap">
                                                <div class="status-bar" style="width: {{ $attendance['percentage'] }}%; background: {{ $attendance['percentage'] >= 80 ? 'linear-gradient(90deg, #22c55e, #16a34a)' : ($attendance['percentage'] >= 60 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)') }}"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">{{ __('No attendance data available') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()?->hasPermission('view_card_student_birthdays'))
        <div class="col-12 col-lg-4">
            <div class="card-modern dashboard-triple-card">
                <div class="card-header-modern birthday-header">
                    <span class="birthday-header-title"><i class="fas fa-birthday-cake me-2"></i>{{ __('Birthdays Today & Next 5 Days') }}</span>
                    <span class="birthday-header-count badge bg-light text-dark">{{ count($upcoming_birthdays) }}</span>
                </div>
                <div class="card-body-modern dashboard-triple-body">
                    <div class="birthday-list">
                        @forelse($upcoming_birthdays as $birthday)
                            <div class="birthday-item">
                                <div class="birthday-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="birthday-content">
                                    <div class="birthday-row">
                                        <strong>{{ $birthday['name'] }}</strong>
                                        <span class="birthday-badge {{ $birthday['days_until'] === 0 ? 'today' : 'upcoming' }}">{{ $birthday['label'] }}</span>
                                    </div>
                                    <div class="birthday-meta">
                                        <span>{{ $birthday['date']->format('M d') }}</span>
                                        @if($birthday['class'] || $birthday['section'])
                                            <span>
                                                {{ collect([$birthday['class'], $birthday['section']])->filter()->join(' · ') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">{{ __('No birthdays in the next 5 days') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()?->hasPermission('view_recent_exam'))
        <div class="col-12 col-lg-4">
            <div class="card-modern dashboard-triple-card">
                <div class="card-header-modern exam-header">
                    <span><i class="fas fa-file-alt me-2"></i>{{ __('Recent Exams') }}</span>
                </div>
                <div class="card-body-modern dashboard-triple-body recent-list">
                    @forelse($recent_exams as $exam)
                        <a href="{{ route('exams.marks-entry', $exam) }}" class="recent-item recent-item-link">
                            <div class="recent-content">
                                <strong>{{ $exam->name ?? __('Exam') }}</strong>
                                <small>{{ $exam->created_at->format('M d, Y') }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted text-center py-4">{{ __('No exams scheduled') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
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
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: clamp(12px, 1.5vw, 16px);
}

@media (max-width: 1199px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
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
.card-header-modern.birthday-header { background: linear-gradient(135deg, #db2777 0%, #9d174d 100%); }

.birthday-header-title {
    flex: 1 1 auto;
    min-width: 0;
}

.birthday-header-count {
    margin-left: auto;
    flex-shrink: 0;
}

.card-body-modern {
    padding: 20px;
}

.dashboard-triple-row {
    align-items: stretch;
    margin-top: 0.25rem;
}

.dashboard-triple-card {
    height: clamp(360px, 34vw, 420px);
    display: flex;
    flex-direction: column;
}

.dashboard-triple-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
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
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(12px, 1.5vw, 16px);
}

@media (max-width: 991px) { .finance-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 575px) { .finance-grid { grid-template-columns: 1fr; } }

.finance-card {
    padding: 18px 20px;
    border-radius: 18px;
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

/* Table Modern */
.table-responsive-modern {
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
}
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
    position: sticky;
    top: 0;
    z-index: 2;
}

.table-modern td {
    padding: 14px 16px;
    font-size: 0.85rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

.table-modern tbody tr:hover { background: #f8fafc; }

.clickable-dashboard-row {
    cursor: pointer;
}

.clickable-dashboard-row:hover {
    background: #eff6ff;
}

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
    overflow-y: auto;
}

.card-modern .recent-list,
.card-modern .birthday-list {
    padding-top: 4px;
}

.recent-item {
    display: block;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.recent-item-link {
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.recent-item-link:hover {
    background: #f8fafc;
    transform: translateX(2px);
    color: inherit;
}

.recent-item:last-child { border-bottom: none; }

.recent-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.recent-content strong { font-size: 0.9rem; color: #1e293b; }
.recent-content small { font-size: 0.75rem; color: #94a3b8; }

/* Birthday List */
.birthday-list {
    display: flex;
    flex-direction: column;
}

.birthday-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.birthday-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.birthday-item:first-child {
    padding-top: 0;
}

.birthday-avatar {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #fbcfe8, #f9a8d4);
    color: #9d174d;
    flex-shrink: 0;
}

.birthday-content {
    flex: 1;
    min-width: 0;
}

.birthday-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.birthday-row strong {
    font-size: 0.95rem;
    color: #1e293b;
}

.birthday-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 4px;
    font-size: 0.8rem;
    color: #64748b;
}

.birthday-badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    white-space: nowrap;
}

.birthday-badge.today {
    background: #fee2e2;
    color: #b91c1c;
}

.birthday-badge.upcoming {
    background: #fce7f3;
    color: #9d174d;
}

/* Responsive adjustments */
@media (max-width: 575px) {
    .dashboard-modern { padding: 12px; }
    .dashboard-triple-card { height: auto; }
    .dashboard-triple-body { overflow-y: visible; }
    .quick-actions-section { margin-bottom: 18px; }
    .stats-grid { gap: 12px; }
    .finance-grid { gap: 14px; margin-bottom: 18px; }
    .dashboard-triple-row { row-gap: 14px; }
    .quick-action-card { padding: 14px 16px; gap: 10px; }
    .action-icon { width: 36px; height: 36px; font-size: 1rem; }
    .action-text { font-size: 0.8rem; }
    .stat-value { font-size: 1.5rem; }
    .attend-num { font-size: 1.25rem; }
    .birthday-row { flex-direction: column; align-items: flex-start; }
    .birthday-header-count { margin-left: 12px; }
}

@media (min-width: 576px) {
    .clickable-dashboard-row:focus {
        outline: 2px solid #3b82f6;
        outline-offset: -2px;
    }
}

@media (min-width: 992px) {
    .finance-grid {
        align-items: stretch;
    }
}

html[data-theme='dark'] .dashboard-modern {
    color: #e5eefb;
}

html[data-theme='dark'] .card-modern,
html[data-theme='dark'] .stat-card-modern,
html[data-theme='dark'] .finance-card {
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
    border-color: #24324a;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.24);
}

html[data-theme='dark'] .card-modern:hover,
html[data-theme='dark'] .stat-card-modern:hover {
    box-shadow: 0 14px 34px rgba(0, 0, 0, 0.3);
}

html[data-theme='dark'] .card-body-modern,
html[data-theme='dark'] .recent-content strong,
html[data-theme='dark'] .birthday-row strong,
html[data-theme='dark'] .finance-value,
html[data-theme='dark'] .asset-value {
    color: #e5eefb;
}

html[data-theme='dark'] .quick-action-card,
html[data-theme='dark'] .quick-action-card .action-text {
    color: #ffffff !important;
}

html[data-theme='dark'] .quick-action-card .action-icon {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

html[data-theme='dark'] .stat-label,
html[data-theme='dark'] .attend-label,
html[data-theme='dark'] .finance-label,
html[data-theme='dark'] .recent-content small,
html[data-theme='dark'] .birthday-meta,
html[data-theme='dark'] .progress-header,
html[data-theme='dark'] .table-modern td,
html[data-theme='dark'] .table-modern th,
html[data-theme='dark'] .text-muted {
    color: #94a3b8 !important;
}

html[data-theme='dark'] .table-modern th {
    background: #0f172a;
    border-bottom-color: #223047;
}

html[data-theme='dark'] .stat-label,
html[data-theme='dark'] .finance-label,
html[data-theme='dark'] .progress-header span,
html[data-theme='dark'] .progress-header strong,
html[data-theme='dark'] .card-header-modern {
    color: #f8fbff !important;
}

html[data-theme='dark'] .stat-value {
    color: #f8fbff;
    text-shadow: 0 0 1px rgba(255, 255, 255, 0.06);
}

html[data-theme='dark'] .finance-grid .finance-value {
    font-weight: 800;
}

html[data-theme='dark'] .finance-card {
    border-color: #24324a;
}

html[data-theme='dark'] .table-modern td,
html[data-theme='dark'] .recent-item,
html[data-theme='dark'] .birthday-item {
    border-bottom-color: #223047;
}

html[data-theme='dark'] .table-modern tbody tr:hover,
html[data-theme='dark'] .recent-item-link:hover {
    background: rgba(59, 130, 246, 0.12);
}

html[data-theme='dark'] .progress-modern,
html[data-theme='dark'] .status-bar-wrap,
html[data-theme='dark'] .attend-item {
    background: #1f2937;
}

html[data-theme='dark'] .attend-item.present {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.18), rgba(34, 197, 94, 0.1));
}

html[data-theme='dark'] .attend-item.absent {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.18), rgba(239, 68, 68, 0.1));
}

html[data-theme='dark'] .attend-item.leave {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(245, 158, 11, 0.1));
}

html[data-theme='dark'] .attend-item.collected {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.18), rgba(34, 197, 94, 0.1));
}

html[data-theme='dark'] .attend-item.pending {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.18), rgba(239, 68, 68, 0.1));
}

html[data-theme='dark'] .attend-item.due {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(245, 158, 11, 0.1));
}

html[data-theme='dark'] .stat-card-modern.students .stat-icon-wrap {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.16), rgba(191, 219, 254, 0.1));
    color: #93c5fd;
}

html[data-theme='dark'] .stat-card-modern.teachers .stat-icon-wrap {
    background: linear-gradient(135deg, rgba(224, 231, 255, 0.16), rgba(199, 210, 254, 0.1));
    color: #a5b4fc;
}

html[data-theme='dark'] .stat-card-modern.staff .stat-icon-wrap {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.16), rgba(253, 230, 138, 0.1));
    color: #fbbf24;
}

html[data-theme='dark'] .stat-card-modern.classes .stat-icon-wrap {
    background: linear-gradient(135deg, rgba(220, 252, 231, 0.16), rgba(187, 247, 208, 0.1));
    color: #4ade80;
}

html[data-theme='dark'] .quick-action-card {
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24);
}

html[data-theme='dark'] .birthday-avatar {
    background: linear-gradient(135deg, rgba(251, 207, 232, 0.18), rgba(249, 168, 212, 0.12));
    color: #f9a8d4;
}

html[data-theme='dark'] .birthday-badge.today {
    background: rgba(239, 68, 68, 0.18);
    color: #fca5a5;
}

html[data-theme='dark'] .birthday-badge.upcoming {
    background: rgba(236, 72, 153, 0.18);
    color: #f9a8d4;
}

html[data-theme='dark'] .finance-card.income .finance-value,
html[data-theme='dark'] .finance-card.balance.positive .finance-value {
    color: #4ade80;
}

html[data-theme='dark'] .finance-card.expense .finance-value,
html[data-theme='dark'] .finance-card.balance.negative .finance-value {
    color: #f87171;
}

html[data-theme='dark'] .dashboard-triple-row .card-header-modern {
    border-bottom: 1px solid rgba(148, 163, 184, 0.12);
}
</style>
<script>
document.addEventListener('click', function (event) {
    const row = event.target.closest('.clickable-dashboard-row');
    if (!row) {
        return;
    }

    const href = row.getAttribute('data-href');
    if (href) {
        window.location.href = href;
    }
});

document.addEventListener('keydown', function (event) {
    const row = event.target.closest('.clickable-dashboard-row');
    if (!row) {
        return;
    }

    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        const href = row.getAttribute('data-href');
        if (href) {
            window.location.href = href;
        }
    }
});
</script>
@endsection
