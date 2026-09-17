@php
    $hubModules = [
        ['label' => __('Academics'), 'icon' => 'fa-school', 'route' => 'academics.hub', 'permission' => 'view_academics', 'match' => ['academics.hub', 'classes.*', 'sections.*', 'groups.*', 'sessions.*', 'subjects.*', 'classrooms.*', 'routines.*', 'class-schedules.*']],
        ['label' => __('Admissions'), 'icon' => 'fa-user-plus', 'route' => 'admissions.hub', 'permission' => 'view_admission_management', 'match' => ['admissions.*']],
        ['label' => __('Students'), 'icon' => 'fa-user-graduate', 'route' => 'students.hub', 'permission' => 'view_students', 'match' => ['students.*', 'teacher-section-assignments.*']],
        ['label' => __('Results'), 'icon' => 'fa-poll', 'route' => 'results.hub', 'permission' => 'view_results', 'match' => ['results.*', 'exams.*', 'student-subjects.*', 'result.*']],
        ['label' => __('Attendance'), 'icon' => 'fa-clipboard-check', 'route' => 'attendance.hub', 'permission' => 'view_attendance', 'match' => ['attendance.*']],
        ['label' => __('Fees'), 'icon' => 'fa-coins', 'route' => 'fees.hub', 'permission' => 'view_fees', 'match' => ['fees.*', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'payments.*']],
        ['label' => __('Accounts'), 'icon' => 'fa-book', 'route' => 'accounts.hub', 'permission' => 'view_accounts', 'match' => ['accounts.hub', 'account-groups.*', 'accounts-list.*', 'ledger.*', 'accounting-periods.*', 'journal-entries.*', 'reports.*', 'bank-accounts.*', 'mobile-banking-accounts.*', 'hand-cash.*', 'transactions.*']],
        ['label' => __('HR'), 'icon' => 'fa-users', 'route' => 'hr.hub', 'permission' => 'view_hr', 'match' => ['hr.*']],
        ['label' => __('Inventory'), 'icon' => 'fa-boxes', 'route' => 'inventory.hub', 'permission' => 'view_inventory', 'match' => ['inventory.*']],
        ['label' => __('Financials'), 'icon' => 'fa-chart-line', 'route' => 'financials.hub', 'permission' => 'view_financials', 'match' => ['financials.*', 'incomes.*', 'expenses.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*', 'shareholders.*']],
        ['label' => __('Assets'), 'icon' => 'fa-building', 'route' => 'assets.hub', 'permission' => 'view_assets', 'match' => ['assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*', 'facilities.*']],
        ['label' => __('Settings'), 'icon' => 'fa-cogs', 'route' => 'institute.hub', 'permission' => 'view_institute_settings', 'match' => ['institute.*', 'school-settings.*', 'certificates.*', 'id-card-templates.*', 'buildings.*', 'rooms.*', 'location.*', 'division.*', 'district.*', 'police-station.*', 'post-office.*']],
        ['label' => __('Users'), 'icon' => 'fa-user-cog', 'route' => 'users.hub', 'permission' => 'view_users', 'match' => ['users.*', 'roles.*', 'permissions.*', 'permission-categories.*']],
    ];

    $hubModules = collect($hubModules)->filter(fn ($module) => \Illuminate\Support\Facades\Route::has($module['route']) && auth()->user()?->hasPermission($module['permission']))->values();
    $currentModule = $hubModules->first(fn ($module) => request()->routeIs(...$module['match']));
    $visibleModules = $hubModules->take(9);
    $moreModules = $hubModules->slice(9);

    $contextLinks = [
        'academics.hub' => [
            ['label' => __('Sessions'), 'route' => 'sessions.index', 'permission' => 'view_card_sessions', 'match' => ['sessions.*']],
            ['label' => __('Classes'), 'route' => 'classes.index', 'permission' => 'view_card_classes', 'match' => ['classes.*']],
            ['label' => __('Sections'), 'route' => 'sections.index', 'permission' => 'view_card_sections', 'match' => ['sections.*']],
            ['label' => __('Groups'), 'route' => 'groups.index', 'permission' => 'view_card_groups', 'match' => ['groups.*']],
            ['label' => __('Subjects'), 'route' => 'subjects.index', 'permission' => 'view_card_subjects', 'match' => ['subjects.*']],
            ['label' => __('Class Rooms'), 'route' => 'classrooms.index', 'permission' => 'view_card_class_rooms', 'match' => ['classrooms.*']],
            ['label' => __('Class Routine'), 'route' => 'routines.index', 'permission' => 'view_card_class_routine', 'match' => ['routines.*']],
            ['label' => __('Time Schedule'), 'route' => 'class-schedules.index', 'permission' => 'view_card_class_routine', 'match' => ['class-schedules.*']],
        ],
        'admissions.hub' => [
            ['label' => __('Admission Exams'), 'route' => 'admissions.exams', 'permission' => 'view_admission_exams', 'match' => ['admissions.exams*']],
            ['label' => __('Applications'), 'route' => 'admissions.applications', 'permission' => 'view_admission_applications', 'match' => ['admissions.applications*']],
            ['label' => __('Admit Cards'), 'route' => 'admissions.admit-cards', 'permission' => 'manage_admission_admit_cards', 'match' => ['admissions.admit-cards*']],
            ['label' => __('Merit and Results'), 'route' => 'admissions.results', 'permission' => 'view_admission_results', 'match' => ['admissions.results*']],
            ['label' => __('Approved Students'), 'route' => 'admissions.approved', 'permission' => 'view_approved_admission_students', 'match' => ['admissions.approved']],
            ['label' => __('Converted Admissions'), 'route' => 'admissions.converted', 'permission' => 'view_converted_admissions', 'match' => ['admissions.converted']],
        ],
        'students.hub' => [
            ['label' => __('Student List'), 'route' => 'students.index', 'permission' => 'view_card_student_list', 'match' => ['students.index', 'students.show']],
            ['label' => __('Assign Teacher'), 'route' => 'teacher-section-assignments.index', 'permission' => 'view_card_assign_teacher', 'match' => ['teacher-section-assignments.*']],
            ['label' => __('Generate ID Cards'), 'route' => 'students.id-cards', 'permission' => 'view_card_generate_id_cards', 'match' => ['students.id-cards']],
            ['label' => __('Birthdays'), 'route' => 'students.birthdays', 'permission' => 'view_card_student_birthdays', 'match' => ['students.birthdays']],
            ['label' => __('New Admission'), 'route' => 'students.admission', 'permission' => 'view_card_new_admission', 'match' => ['students.admission']],
            ['label' => __('Promote Students'), 'route' => 'students.promote', 'permission' => 'view_card_promote_students', 'match' => ['students.promote']],
            ['label' => __('Mid-Year Correction'), 'route' => 'students.correction', 'permission' => 'view_card_mid_year_correction', 'match' => ['students.correction']],
            ['label' => __('Student Checkout'), 'route' => 'students.checkout', 'permission' => 'view_card_student_checkout', 'match' => ['students.checkout']],
            ['label' => __('Checked Out List'), 'route' => 'students.checked-out', 'permission' => 'view_card_student_checkout', 'match' => ['students.checked-out']],
            ['label' => __('Academic History'), 'route' => 'students.history', 'permission' => 'view_card_academic_history', 'match' => ['students.history']],
            ['label' => __('Certificate Hub'), 'route' => 'students.certificates', 'permission' => 'view_card_student_certificates', 'match' => ['students.certificates*']],
        ],
        'results.hub' => [
            ['label' => __('All Exams'), 'route' => 'exams.index', 'permission' => 'view_card_all_exams', 'match' => ['exams.*']],
            ['label' => __('Admit and Seat Cards'), 'route' => 'results.admit-seat-cards.index', 'permission' => 'view_results', 'match' => ['results.admit-seat-cards.*']],
            ['label' => __('Result Sheets'), 'route' => 'results.result-sheets', 'permission' => 'view_results', 'match' => ['results.result-sheets*']],
            ['label' => __('Terminal Report'), 'route' => 'result.progress-report.index', 'permission' => 'view_card_terminal_report', 'match' => ['result.progress-report.*']],
            ['label' => __('Yearly Report'), 'route' => 'result.yearly-final-report.index', 'permission' => 'view_card_yearly_final_report', 'match' => ['result.yearly-final-report.*']],
            ['label' => __('Tutorial Report'), 'route' => 'result.tutorial-report.index', 'permission' => 'view_card_tutorial_exam_report', 'match' => ['result.tutorial-report.*']],
        ],
        'attendance.hub' => [
            ['label' => __('Daily Attendance'), 'route' => 'attendance.index', 'permission' => 'view_card_daily_attendance', 'match' => ['attendance.index']],
            ['label' => __('Monthly Report'), 'route' => 'attendance.report.monthly', 'permission' => 'view_card_monthly_attendance_report', 'match' => ['attendance.report.monthly']],
            ['label' => __('Settings'), 'route' => 'attendance.settings.index', 'permission' => 'view_card_attendance_settings', 'match' => ['attendance.settings.*']],
        ],
        'fees.hub' => [
            ['label' => __('Collect Payments'), 'route' => 'fees.collect_payment', 'permission' => 'view_card_collect_payments', 'match' => ['fees.collect_payment', 'fees.collect']],
            ['label' => __('Fee Categories'), 'route' => 'fee-categories.index', 'permission' => 'view_card_fee_categories', 'match' => ['fee-categories.*']],
            ['label' => __('Fee Sets'), 'route' => 'fee-sets.index', 'permission' => 'view_card_fee_sets', 'match' => ['fee-sets.*']],
            ['label' => __('Scholarships'), 'route' => 'scholarships.index', 'permission' => 'view_card_scholarships', 'match' => ['scholarships.*']],
            ['label' => __('Free Studentship'), 'route' => 'free-studentships.index', 'permission' => 'view_card_free_studentships', 'match' => ['free-studentships.*']],
            ['label' => __('Transport Fees'), 'route' => 'transports.index', 'permission' => 'view_card_transport_fees', 'match' => ['transports.*']],
            ['label' => __('Payment Report'), 'route' => 'fees.payment-report', 'permission' => 'view_card_student_payment_report', 'match' => ['fees.payment-report*']],
            ['label' => __('Receive Report'), 'route' => 'fees.student-receive-report', 'permission' => 'view_card_student_receive_report', 'match' => ['fees.student-receive-report*']],
            ['label' => __('Receivable Report'), 'route' => 'fees.student-receivable-report', 'permission' => 'view_card_student_receivable_report', 'match' => ['fees.student-receivable-report*']],
            ['label' => __('All In One Report'), 'route' => 'fees.all-in-one-report', 'permission' => 'view_card_student_payment_report', 'match' => ['fees.all-in-one-report*']],
            ['label' => __('Classwise Due'), 'route' => 'fees.due-report', 'permission' => 'view_card_classwise_due_report', 'match' => ['fees.due-report*']],
            ['label' => __('Student Due'), 'route' => 'fees.student-due-report', 'permission' => 'view_card_student_due_report', 'match' => ['fees.student-due-report*']],
            ['label' => __('Student Ledger'), 'route' => 'fees.student-ledger.index', 'permission' => 'view_card_student_due_report', 'match' => ['fees.student-ledger.*']],
            ['label' => __('Discount List'), 'route' => 'fees.discount-list', 'permission' => 'view_card_discount_list', 'match' => ['fees.discount-list*']],
        ],
        'accounts.hub' => [
            ['label' => __('Transactions'), 'route' => 'transactions.index', 'permission' => 'view_card_transactions', 'match' => ['transactions.*']],
            ['label' => __('Ledger'), 'route' => 'ledger.index', 'permission' => 'view_card_ledger', 'match' => ['ledger.*']],
            ['label' => __('Trial Balance'), 'route' => 'reports.trial-balance', 'permission' => 'view_card_trial_balance', 'match' => ['reports.trial-balance*']],
            ['label' => __('Detailed Trial Balance'), 'route' => 'reports.details-trial-balance', 'permission' => 'view_card_detailed_trial_balance', 'match' => ['reports.details-trial-balance*']],
            ['label' => __('Balance Sheet'), 'route' => 'reports.balance-sheet', 'permission' => 'view_card_balance_sheet', 'match' => ['reports.balance-sheet*']],
            ['label' => __('Cash Book'), 'route' => 'reports.cash-book', 'permission' => 'view_card_cash_book', 'match' => ['reports.cash-book*']],
            ['label' => __('Day Book'), 'route' => 'reports.day-book', 'permission' => 'view_card_day_book', 'match' => ['reports.day-book*']],
            ['label' => __('Income & Expenditure'), 'route' => 'reports.income-expenditure', 'permission' => 'view_card_income_expenditure', 'match' => ['reports.income-expenditure*']],
            ['label' => __('Cash Summary'), 'route' => 'reports.cash-summary', 'permission' => 'view_card_cash_summary', 'match' => ['reports.cash-summary*']],
            ['label' => __('Receipt & Payment'), 'route' => 'reports.receipt-payment', 'permission' => 'view_card_receipt_payment', 'match' => ['reports.receipt-payment*']],
            ['label' => __('Supplier Due'), 'route' => 'reports.supplier-dues', 'permission' => 'view_supplier_due_report', 'match' => ['reports.supplier-dues*']],
            ['label' => __('Cash Flow'), 'route' => 'reports.cash-flow', 'permission' => 'view_card_cash_flow', 'match' => ['reports.cash-flow*']],
        ],
        'inventory.hub' => [
            ['label' => __('Categories'), 'route' => 'inventory.categories.index', 'permission' => 'view_card_inventory_categories', 'match' => ['inventory.categories.*']],
            ['label' => __('Products'), 'route' => 'inventory.products.index', 'permission' => 'view_card_inventory_products', 'match' => ['inventory.products.*']],
            ['label' => __('Suppliers'), 'route' => 'inventory.suppliers.index', 'permission' => 'view_card_inventory_suppliers', 'match' => ['inventory.suppliers.*']],
            ['label' => __('Purchases'), 'route' => 'inventory.purchases.index', 'permission' => 'view_card_inventory_purchases', 'match' => ['inventory.purchases.*']],
            ['label' => __('Opening Stock'), 'route' => 'inventory.opening-stock.create', 'permission' => 'manage_inventory_products', 'match' => ['inventory.opening-stock.*']],
            ['label' => __('Sales Hub'), 'route' => 'inventory.sales.hub', 'permission' => 'view_inventory', 'match' => ['inventory.sales.*']],
            ['label' => __('Stock Report'), 'route' => 'inventory.reports.stock', 'permission' => 'view_card_inventory_stock_report', 'match' => ['inventory.reports.stock']],
            ['label' => __('Low Stock Products'), 'route' => 'inventory.reports.lowStock', 'permission' => 'view_card_inventory_low_stock', 'match' => ['inventory.reports.lowStock']],
        ],
        'financials.hub' => [
            ['label' => __('Incomes'), 'route' => 'incomes.index', 'permission' => 'view_card_incomes', 'match' => ['incomes.*']],
            ['label' => __('Expenses'), 'route' => 'expenses.index', 'permission' => 'view_card_expenses', 'match' => ['expenses.*']],
            ['label' => __('Capital'), 'route' => 'shareholder-transactions.index', 'permission' => 'view_card_capital', 'params' => ['type' => 'capital'], 'match' => ['shareholder-transactions.*']],
            ['label' => __('Income Categories'), 'route' => 'income-categories.index', 'permission' => 'view_card_income_categories', 'match' => ['income-categories.*']],
            ['label' => __('Expense Categories'), 'route' => 'expense-categories.index', 'permission' => 'view_card_expense_categories', 'match' => ['expense-categories.*']],
            ['label' => __('All Shareholders'), 'route' => 'shareholders.index', 'permission' => 'view_card_all_shareholders', 'match' => ['shareholders.index', 'shareholders.show']],
            ['label' => __('Add Shareholder'), 'route' => 'shareholders.create', 'permission' => 'view_card_add_shareholder', 'match' => ['shareholders.create']],
            ['label' => __('Contribution'), 'route' => 'shareholders.contribution', 'permission' => 'view_card_all_shareholders', 'match' => ['shareholders.contribution']],
        ],
        'shareholders.hub' => [
            ['label' => __('All Shareholders'), 'route' => 'shareholders.index', 'permission' => 'view_card_all_shareholders', 'match' => ['shareholders.index', 'shareholders.show']],
            ['label' => __('Add Shareholder'), 'route' => 'shareholders.create', 'permission' => 'view_card_add_shareholder', 'match' => ['shareholders.create']],
            ['label' => __('Contribution'), 'route' => 'shareholders.contribution', 'permission' => 'view_card_all_shareholders', 'match' => ['shareholders.contribution']],
        ],
        'hr.hub' => [
            ['label' => __('All Employees'), 'route' => 'hr.employees.index', 'permission' => 'view_card_all_employees', 'match' => ['hr.employees.*']],
            ['label' => __('Add Employee'), 'route' => 'hr.employees.create', 'permission' => 'view_card_add_employee', 'match' => ['hr.employees.create']],
            ['label' => __('Departments'), 'route' => 'hr.departments.index', 'permission' => 'view_card_departments', 'match' => ['hr.departments.*']],
            ['label' => __('Designations'), 'route' => 'hr.designations.index', 'permission' => 'view_card_designations', 'match' => ['hr.designations.*']],
            ['label' => __('Leave Requests'), 'route' => 'hr.leave.index', 'permission' => 'view_card_leave_requests', 'match' => ['hr.leave.*']],
            ['label' => __('Leave Balances'), 'route' => 'hr.leave.balances', 'permission' => 'view_card_leave_balances', 'match' => ['hr.leave.balances*']],
        ],
        'assets.hub' => [
            ['label' => __('Asset Categories'), 'route' => 'asset-categories.index', 'permission' => 'view_card_asset_categories', 'match' => ['asset-categories.*']],
            ['label' => __('Assets List'), 'route' => 'assets.index', 'permission' => 'view_card_assets_list', 'match' => ['assets.*']],
            ['label' => __('Purchases'), 'route' => 'asset-purchases.index', 'permission' => 'view_card_asset_purchases', 'match' => ['asset-purchases.*']],
            ['label' => __('Issue Register'), 'route' => 'asset-issues.index', 'permission' => 'view_card_asset_issue_register', 'match' => ['asset-issues.*']],
            ['label' => __('Asset Stock'), 'route' => 'asset-issues.stock', 'permission' => 'view_card_asset_stock', 'match' => ['asset-issues.stock']],
            ['label' => __('Facility Bookings'), 'route' => 'facilities.bookings.index', 'permission' => 'view_card_facility_bookings', 'match' => ['facilities.bookings.*']],
        ],
        'institute.hub' => [
            ['label' => __('School Settings'), 'route' => 'school-settings.index', 'permission' => 'view_card_school_settings', 'match' => ['school-settings.*']],
            ['label' => __('Certificate Types'), 'route' => 'certificates.index', 'permission' => 'view_card_school_settings', 'match' => ['certificates.*']],
            ['label' => __('Buildings'), 'route' => 'buildings.index', 'permission' => 'view_card_buildings', 'match' => ['buildings.*']],
            ['label' => __('Rooms'), 'route' => 'rooms.index', 'permission' => 'view_card_rooms', 'match' => ['rooms.*']],
            ['label' => __('Division'), 'route' => 'division.index', 'permission' => 'view_card_divisions', 'match' => ['division.*']],
            ['label' => __('District'), 'route' => 'district.index', 'permission' => 'view_card_districts', 'match' => ['district.*']],
            ['label' => __('Police Station'), 'route' => 'police-station.index', 'permission' => 'view_card_police_stations', 'match' => ['police-station.*']],
            ['label' => __('Post Office'), 'route' => 'post-office.index', 'permission' => 'view_card_post_offices', 'match' => ['post-office.*']],
        ],
        'users.hub' => [
            ['label' => __('Users'), 'route' => 'users.index', 'permission' => 'view_card_users', 'match' => ['users.*']],
            ['label' => __('Roles'), 'route' => 'roles.index', 'permission' => 'view_card_roles', 'match' => ['roles.*']],
            ['label' => __('Permissions'), 'route' => 'permissions.index', 'permission' => 'view_card_permissions', 'match' => ['permissions.*']],
            ['label' => __('Audit Trail'), 'route' => 'audit-trails.index', 'permission' => 'view_audit_trail', 'match' => ['audit-trails.*']],
        ],
    ];

    $currentContextLinks = $currentModule ? ($contextLinks[$currentModule['route']] ?? []) : [];
    $currentContextLinks = collect($currentContextLinks)
        ->filter(fn ($link) => \Illuminate\Support\Facades\Route::has($link['route']) && auth()->user()?->hasPermission($link['permission']))
        ->values();
@endphp

<div class="hub-switcher no-print" aria-label="{{ __('Module navigation') }}">
    <div class="hub-switcher__primary">
        <span class="hub-switcher__current">
            <i class="fas {{ $currentModule['icon'] ?? 'fa-compass' }}" aria-hidden="true"></i>
            <span>{{ $currentModule['label'] ?? __('Modules') }}</span>
        </span>

        <div class="hub-switcher__links" role="navigation" aria-label="{{ __('Modules') }}">
            @foreach ($visibleModules as $module)
                <a href="{{ route($module['route']) }}" class="hub-switcher__link {{ $currentModule && $currentModule['route'] === $module['route'] ? 'is-active' : '' }}">
                    <i class="fas {{ $module['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $module['label'] }}</span>
                </a>
            @endforeach
        </div>

        @if ($moreModules->isNotEmpty())
            <div class="dropdown hub-switcher__more">
                <button type="button" class="hub-switcher__more-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('More') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right hub-switcher__menu">
                    @foreach ($moreModules as $module)
                        <a href="{{ route($module['route']) }}" class="dropdown-item {{ $currentModule && $currentModule['route'] === $module['route'] ? 'active' : '' }}">
                            <i class="fas {{ $module['icon'] }} mr-2" aria-hidden="true"></i>{{ $module['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if ($currentContextLinks->isNotEmpty())
        <div class="hub-switcher__context" role="navigation" aria-label="{{ __($currentModule['label'] . ' navigation') }}">
            @foreach ($currentContextLinks as $link)
                @php $contextHref = route($link['route'], $link['params'] ?? []); @endphp
                @if (!empty($link['anchor']))
                    @php $contextHref .= '#' . ltrim($link['anchor'], '#'); @endphp
                @endif
                <a href="{{ $contextHref }}" class="hub-switcher__context-link {{ request()->routeIs(...$link['match']) ? 'is-active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>

@once
    <style>
        .hub-switcher-hidden .hub-switcher {
            display: none !important;
        }

        .hub-switcher {
            position: relative;
            z-index: 1020;
            margin: .7rem 1rem 0;
            border: 1px solid rgba(148, 163, 184, .24);
            border-radius: 1rem;
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
            backdrop-filter: blur(14px);
        }
        .hub-switcher__primary, .hub-switcher__context { display: flex; align-items: center; gap: .35rem; min-width: 0; }
        .hub-switcher__primary { padding: .35rem .45rem; }
        .hub-switcher__current { display: inline-flex; align-items: center; gap: .45rem; flex: 0 0 auto; padding: .5rem .7rem; border-radius: .7rem; color: #0f172a; font-size: .8rem; font-weight: 800; }
        .hub-switcher__current i { color: #2563eb; }
        .hub-switcher__links { display: flex; align-items: center; gap: .15rem; min-width: 0; overflow-x: auto; scrollbar-width: none; }
        .hub-switcher__links::-webkit-scrollbar { display: none; }
        .hub-switcher__link, .hub-switcher__more-button { display: inline-flex; align-items: center; gap: .4rem; flex: 0 0 auto; padding: .52rem .65rem; border: 0; border-radius: .65rem; background: transparent; color: #64748b; font-size: .76rem; font-weight: 650; white-space: nowrap; text-decoration: none; }
        .hub-switcher__link:hover, .hub-switcher__more-button:hover { background: #eff6ff; color: #1d4ed8; text-decoration: none; }
        .hub-switcher__link.is-active { background: #2563eb; color: #fff; box-shadow: 0 5px 12px rgba(37, 99, 235, .2); }
        .hub-switcher__more { flex: 0 0 auto; }
        .hub-switcher__more-button::after { margin-left: .2rem; }
        .hub-switcher__menu { min-width: 12rem; border: 0; border-radius: .8rem; box-shadow: 0 16px 35px rgba(15, 23, 42, .16); }
        .hub-switcher__context { padding: .15rem .8rem .45rem; border-top: 1px solid rgba(148, 163, 184, .16); overflow-x: auto; scrollbar-width: none; }
        .hub-switcher__context::-webkit-scrollbar { display: none; }
        .hub-switcher__context-link { padding: .35rem .55rem; color: #64748b; font-size: .72rem; font-weight: 600; white-space: nowrap; text-decoration: none; border-bottom: 2px solid transparent; }
        .hub-switcher__context-link:hover, .hub-switcher__context-link.is-active { color: #1d4ed8; border-bottom-color: #2563eb; text-decoration: none; }
        html[data-theme='dark'] .hub-switcher { background: rgba(15, 23, 42, .88); border-color: rgba(148, 163, 184, .22); }
        html[data-theme='dark'] .hub-switcher__current { color: #e2e8f0; }
        html[data-theme='dark'] .hub-switcher__link, html[data-theme='dark'] .hub-switcher__more-button, html[data-theme='dark'] .hub-switcher__context-link { color: #94a3b8; }
        html[data-theme='dark'] .hub-switcher__link:hover, html[data-theme='dark'] .hub-switcher__more-button:hover { background: rgba(37, 99, 235, .2); color: #bfdbfe; }
        @media print {
            .hub-switcher { display: none !important; }
        }
        @media (max-width: 767.98px) {
            .hub-switcher { margin: .55rem .65rem 0; border-radius: .8rem; }
            .hub-switcher__current { padding: .45rem .55rem; }
            .hub-switcher__current span { display: none; }
            .hub-switcher__link { padding: .48rem .55rem; }
            .hub-switcher__link i { display: none; }
        }
    </style>
@endonce
