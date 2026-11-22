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
    $attendance_taken_today = $attendance_taken_today ?? false;
    $total_fees_due = $total_fees_due ?? 0;
    $total_fees_paid = $total_fees_paid ?? 0;
    $total_fees_pending = $total_fees_pending ?? 0;
    $fee_collection_rate = $fee_collection_rate ?? 0;
    $total_income = $total_income ?? 0;
    $inactive_students = $inactive_students ?? 0;
    $period_label = $period_label ?? now()->translatedFormat('M Y');
    $prev_period_label = $prev_period_label ?? now()->subMonthNoOverflow()->translatedFormat('M');
    $prev_income = $prev_income ?? null;
    $prev_expense = $prev_expense ?? null;
    $prev_net_balance = $prev_net_balance ?? null;
    $alltime_income = $alltime_income ?? null;
    $alltime_expense = $alltime_expense ?? null;
    $alltime_net_balance = $alltime_net_balance ?? null;
    // "▲ 12% vs Oct" — null when there is nothing to compare against
    $kpiDelta = function ($current, $previous) use ($prev_period_label) {
        if ($previous === null || (float) $previous == 0.0) { return null; }
        $pct = (($current - $previous) / abs($previous)) * 100;
        return ['up' => $pct >= 0, 'text' => ($pct >= 0 ? '▲ ' : '▼ ') . localized_number(abs(round($pct))) . '% ' . __('vs :month', ['month' => $prev_period_label])];
    };
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
    <div class="dashboard-quick-actions mb-4" role="group" aria-label="{{ __('Quick actions') }}">
        @if($quickActionCards['collect_fees'])
            <x-btn variant="primary" icon="fas fa-money-bill-wave" :href="route('fees.collect')">{{ __('Collect Fees') }}</x-btn>
        @endif
        @if($quickActionCards['mark_attendance'])
            <x-btn variant="secondary" icon="fas fa-clipboard-check" :href="route('attendance.index')">{{ __('Mark Attendance') }}</x-btn>
        @endif
        @if($quickActionCards['add_student'])
            <x-btn variant="secondary" icon="fas fa-user-plus" :href="route('students.admission')">{{ __('Add Student') }}</x-btn>
        @endif
        @if($quickActionCards['view_reports'])
            <x-btn variant="secondary" icon="fas fa-chart-bar" :href="route('accounts.hub')">{{ __('View Reports') }}</x-btn>
        @endif
    </div>
    @endif

    <!-- Key Statistics Cards -->
    @if(auth()->user()?->hasPermission('view_card_dashboard'))
    <div class="stats-grid mb-4">
        <!-- Students Card -->
        <div class="stat-card-modern students">
            <div class="stat-content">
                <div class="stat-info">
                    <span class="stat-label">{{ __('Active Students') }}</span>
                    <span class="stat-value">{{ localized_number($total_students) }}</span>
                    @if($inactive_students > 0)
                        <span class="stat-hint">{{ __(':count inactive', ['count' => localized_number($inactive_students)]) }}</span>
                    @endif
                </div>
                <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            </div>
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
        </div>
    </div>
    @endif

    <!-- Financial Overview -->
    @if(auth()->user()?->hasPermission('view_income_expense'))
    <div class="finance-grid mb-4">
        @foreach ([
            ['key' => 'income', 'label' => __('Income'), 'value' => $total_income, 'prev' => $prev_income, 'all' => $alltime_income, 'goodWhenUp' => true],
            ['key' => 'expense', 'label' => __('Expense'), 'value' => $total_expense, 'prev' => $prev_expense, 'all' => $alltime_expense, 'goodWhenUp' => false],
            ['key' => 'balance', 'label' => __('Net Balance'), 'value' => $net_balance, 'prev' => $prev_net_balance, 'all' => $alltime_net_balance, 'goodWhenUp' => true],
        ] as $fin)
            @php $delta = $kpiDelta($fin['value'], $fin['prev']); @endphp
            <div class="finance-card {{ $fin['key'] }} {{ $fin['key'] === 'balance' ? ($fin['value'] >= 0 ? 'positive' : 'negative') : '' }}">
                <span class="finance-label">{{ $fin['label'] }} <span class="finance-period">· {{ $period_label }}</span></span>
                <span class="finance-value">{{ localized_currency($fin['value'], '৳', 0) }}</span>
                <span class="finance-meta">
                    @if($delta)
                        <span class="finance-delta {{ $delta['up'] === $fin['goodWhenUp'] ? 'is-good' : 'is-bad' }}">{{ $delta['text'] }}</span>
                    @else
                        <span class="finance-delta">{{ __('Nothing recorded in :month', ['month' => $prev_period_label]) }}</span>
                    @endif
                    @if($fin['all'] !== null)
                        <span class="finance-alltime">{{ __('All time') }}: {{ localized_currency($fin['all'], '৳', 0) }}</span>
                    @endif
                </span>
            </div>
        @endforeach
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
                    @if(! $attendance_taken_today)
                    <div class="dashboard-empty-state">
                        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                        <p class="mb-2 fw-bold">{{ __("Attendance hasn't been taken today") }}</p>
                        @if(auth()->user()?->hasAnyPermission(['view_attendance', 'view_card_mark_attendance']) && Route::has('attendance.index'))
                            <x-btn variant="primary" size="sm" icon="fas fa-clipboard-check" :href="route('attendance.index')">{{ __('Mark attendance') }}</x-btn>
                        @endif
                    </div>
                    @else
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
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Fee Statistics -->
        @if(auth()->user()?->hasPermission('view_fee_collection'))
        <div class="col-12 col-lg-6">
            <div class="card-modern h-100 fee-status-card">
                <div class="card-header-modern fee-header">
                    <span><i class="fas fa-coins me-2"></i>{{ __('Fee Collection Status') }}</span>
                </div>
                <div class="card-body-modern">
                    <div class="attend-grid fee-status-grid">
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
                    <span><i class="fas fa-list me-2"></i>{{ __('Classwise Attendance') }}</span>
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
                                        @if(! ($attendance['taken'] ?? true))
                                        <td class="text-muted">{{ __('Not taken') }}</td>
                                        <td><div class="status-bar-wrap" aria-hidden="true"></div></td>
                                        @else
                                        <td>{{ localized_number($attendance['percentage']) }}%</td>
                                        <td>
                                            <div class="status-bar-wrap">
                                                <div class="status-bar" style="width: {{ $attendance['percentage'] }}%; background: {{ $attendance['percentage'] >= 80 ? '#16a34a' : ($attendance['percentage'] >= 60 ? '#d97706' : '#dc2626') }}"></div>
                                            </div>
                                        </td>
                                        @endif
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
                    <span class="birthday-header-title"><i class="fas fa-birthday-cake me-2"></i>{{ __('Recent Birthdays') }}</span>
                    <span class="birthday-header-count badge bg-light text-dark">{{ count($upcoming_birthdays) }}</span>
                </div>
                <div class="card-body-modern dashboard-triple-body">
                    <div class="birthday-list birthday-mobile-list">
                        @forelse($upcoming_birthdays as $birthday)
                            <a class="birthday-item birthday-item-link" href="{{ $birthday['link'] ?? '#' }}">
                                <div class="birthday-avatar">
                                    @if(!empty($birthday['image_url']))
                                        <img src="{{ $birthday['image_url'] }}" alt="{{ $birthday['name'] }}">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <div class="birthday-content">
                                    <div class="birthday-row">
                                        <strong>{{ $birthday['name'] }}</strong>
                                        <span class="birthday-badge {{ (int) $birthday['days_until'] === 0 ? 'today' : 'upcoming' }}">{{ (int) $birthday['days_until'] === 0 ? __('Today is birthday') : $birthday['label'] }}</span>
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
                            </a>
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
    padding: 16px;
    width: 100%;
}

.dashboard-modern > .dashboard-quick-actions,
.dashboard-modern > .stats-grid,
.dashboard-modern > .finance-grid,
.dashboard-modern > .row.mb-4 {
    margin-bottom: 16px !important;
}

.dashboard-modern > .row.mb-4,
.dashboard-modern > .dashboard-triple-row {
    --bs-gutter-x: 12px;
    --bs-gutter-y: 12px;
}

/* Quick Actions Section */










/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
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

.stat-card-modern.students .stat-icon-wrap { color: #2563eb; }
.stat-card-modern.teachers .stat-icon-wrap { color: #4f46e5; }
.stat-card-modern.staff .stat-icon-wrap { color: #d97706; }
.stat-card-modern.classes .stat-icon-wrap { color: #16a34a; }



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
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}


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
    margin-top: 0;
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
.dashboard-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 160px;
    gap: .25rem;
    color: var(--c-text-muted, #64748b);
}
.dashboard-empty-state > i { font-size: 1.75rem; opacity: .6; margin-bottom: .25rem; }
.dashboard-empty-state p { color: var(--c-text, #0f172a); }
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

.attend-item.present .attend-num { color: #16a34a; }

.attend-item.absent .attend-num { color: #dc2626; }

.attend-item.leave .attend-num { color: #d97706; }

.attend-item.collected .attend-num { color: #16a34a; }

.attend-item.pending .attend-num { color: #dc2626; }

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
    border-radius: 10px;
    transition: width 0.5s ease;
}

.progress-bar-fee {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Finance Grid */
.finance-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
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
.recent-content small { font-size: 0.75rem; color: var(--c-text-muted, #475569); }

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

.birthday-item-link {
    color: inherit;
    text-decoration: none;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.birthday-item-link:hover,
.birthday-item-link:focus-visible {
    color: inherit;
    background: #fff1f8;
    transform: translateX(2px);
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
    color: #9d174d;
    flex-shrink: 0;
}

.birthday-avatar img {
    width: 100%;
    height: 100%;
    border-radius: inherit;
    object-fit: cover;
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
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .finance-grid { gap: 12px; margin-bottom: 16px; }
    .dashboard-triple-row { row-gap: 12px; }
    .stat-value { font-size: 1.5rem; }
    .attend-num { font-size: 1.25rem; }
    .fee-status-card,
    .fee-status-card .card-body-modern,
    .fee-status-grid {
        min-width: 0;
    }
    .fee-status-card { overflow: hidden; }
    .fee-status-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }
    .fee-status-grid .attend-item {
        min-width: 0;
        padding: 12px 5px;
        overflow: hidden;
    }
    .fee-status-grid .attend-num {
        max-width: 100%;
        font-size: clamp(.78rem, 3.8vw, 1.05rem);
        line-height: 1.15;
        overflow-wrap: anywhere;
        word-break: break-word;
        text-align: center;
    }
    .birthday-mobile-list .birthday-item {
        gap: 10px;
        padding: 9px 0;
    }
    .birthday-mobile-list .birthday-item:first-child {
        padding-top: 0;
    }
    .birthday-mobile-list .birthday-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        font-size: .9rem;
    }
    .birthday-mobile-list .birthday-row {
        gap: 6px;
    }
    .birthday-mobile-list .birthday-row strong {
        font-size: .84rem;
        line-height: 1.2;
    }
    .birthday-mobile-list .birthday-badge {
        padding: 3px 8px;
        font-size: .68rem;
    }
    .birthday-mobile-list .birthday-meta {
        gap: 6px;
        margin-top: 3px;
        font-size: .7rem;
        line-height: 1.2;
    }
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







html[data-theme='dark'] .stat-card-modern.students .stat-icon-wrap {
    color: #93c5fd;
}

html[data-theme='dark'] .stat-card-modern.teachers .stat-icon-wrap {
    color: #a5b4fc;
}

html[data-theme='dark'] .stat-card-modern.staff .stat-icon-wrap {
    color: #fbbf24;
}

html[data-theme='dark'] .stat-card-modern.classes .stat-icon-wrap {
    color: #4ade80;
}

html[data-theme='dark'] .birthday-avatar {
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

/* F-22: one calm palette. Neutral surfaces and headers; colour only on semantic values
   (green = collected/present/income, red = pending/absent/expense, amber = leave/due). */
.dashboard-quick-actions { display: flex; flex-wrap: wrap; gap: var(--sp-2, 8px); }
.dashboard-quick-actions .ds-btn { min-height: 40px; }
.card-header-modern,
.card-header-modern.fee-header,
.card-header-modern.exam-header,
.card-header-modern.notice-header,
.card-header-modern.birthday-header {
    background: var(--c-surface, #fff);
    color: var(--c-text, #0f172a);
    font-size: var(--fs-lg, 16px);
    border-bottom: 1px solid var(--c-border, #e2e8f0);
}
.card-header-modern i { color: var(--c-text-muted, #475569); }
.stat-card-modern .stat-icon-wrap { background: none !important; width: auto; height: auto; color: var(--c-text-muted, #475569) !important; font-size: 1.35rem; }
.stat-hint { font-size: var(--fs-sm, 13px); color: var(--c-text-muted, #475569); }
.stat-label, .attend-label, .finance-label { color: var(--c-text-muted, #475569); }
.attend-item,
.attend-item.present, .attend-item.absent, .attend-item.leave,
.attend-item.collected, .attend-item.pending, .attend-item.due { background: var(--c-surface-muted, #f8fafc); border: 1px solid var(--c-border, #e2e8f0); }
.attend-item.leave .attend-num, .attend-item.due .attend-num { color: #b45309; }
.progress-bar-modern { background: #16a34a; }
.progress-bar-fee { background: var(--c-primary, #0b6b3a); }
.birthday-avatar { background: var(--c-surface-muted, #f1f5f9); color: var(--c-text-muted, #475569); }
.finance-period { font-weight: 400; }
.finance-meta { display: flex; flex-direction: column; gap: 2px; margin-top: 2px; font-size: var(--fs-sm, 13px); color: var(--c-text-muted, #475569); }
.finance-delta.is-good { color: #15803d; font-weight: 600; }
.finance-delta.is-bad { color: #b91c1c; font-weight: 600; }
html[data-theme='dark'] .card-modern,
html[data-theme='dark'] .stat-card-modern,
html[data-theme='dark'] .finance-card { background: var(--c-surface, #111827); }
html[data-theme='dark'] .card-header-modern { background: var(--c-surface, #111827); color: var(--c-text, #e2e8f0); border-bottom-color: var(--c-border, #1f2937); }
html[data-theme='dark'] .attend-item,
html[data-theme='dark'] .attend-item.present, html[data-theme='dark'] .attend-item.absent, html[data-theme='dark'] .attend-item.leave,
html[data-theme='dark'] .attend-item.collected, html[data-theme='dark'] .attend-item.pending, html[data-theme='dark'] .attend-item.due { background: var(--c-surface-muted, #0f172a); border-color: var(--c-border, #1f2937); }
html[data-theme='dark'] .stat-card-modern .stat-icon-wrap, html[data-theme='dark'] .stat-hint, html[data-theme='dark'] .finance-meta { color: #94a3b8 !important; }
html[data-theme='dark'] .finance-delta.is-good { color: #4ade80; }
html[data-theme='dark'] .finance-delta.is-bad { color: #f87171; }
html[data-theme='dark'] .birthday-avatar { background: #1f2937; color: #cbd5e1; }
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
