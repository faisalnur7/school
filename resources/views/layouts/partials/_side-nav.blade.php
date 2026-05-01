<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 customer_side_nav">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()?->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Academic Management -->
                <li class="nav-item has-treeview {{ menuOpen(['classes.*','sections.*','groups.*','sessions.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Academic Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('sessions.index') }}" class="nav-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Sessions</a></li>
                        <li><a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Classes</a></li>
                        <li><a href="{{ route('sections.index') }}" class="nav-link {{ request()->routeIs('sections.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Sections</a></li>
                        <li><a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Groups</a></li>
                    </ul>
                </li>

                <!-- Student Management -->
                <li class="nav-item has-treeview {{ menuOpen(['students.*','attendance.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Student Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Student List</a></li>
                        <li><a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Add Student</a></li>
                        <li><a href="{{ route('students.id-cards') }}" class="nav-link {{ request()->routeIs('students.id-cards') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Generate ID Cards</a></li>
                    </ul>
                </li>

                <!-- Finance & Accounts -->
                <li class="nav-item has-treeview {{ menuOpen(['fee-categories.*','fee-sets.*','scholarships.*','transports.*','fees.collect','payments.*','fees.due-report','fees.student-due-report','fees.discount-list','bank-accounts.*','mobile-banking-accounts.*','hand-cash.*','incomes.*','expenses.*','income-categories.*','expense-categories.*','transactions.*','shareholders.*','shareholder-transactions.*','account-groups.*','accounts-list.*','ledger.*','journal-entries.*','budget-allocations.*','reports.trial-balance','reports.balance-sheet','reports.cash-flow','reports.cash-book','reports.day-book','reports.income-expenditure','reports.receipt-payment','reports.cash-summary','reports.chart-of-accounts','reports.headwise-transactions']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>Finance & Accounts<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">

                        <!-- Fee Management -->
                        <li class="nav-item has-treeview {{ menuOpen(['fee-categories.*','fee-sets.*','scholarships.*','transports.*','fees.collect','payments.*','fees.due-report','fees.student-due-report','fees.discount-list']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['fee-categories.*','fee-sets.*','scholarships.*','transports.*','fees.collect','payments.*','fees.due-report','fees.student-due-report','fees.discount-list']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fee Management<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('fee-categories.index') }}" class="nav-link {{ request()->routeIs('fee-categories.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Fee Category</a></li>
                                <li><a href="{{ route('fee-sets.index') }}" class="nav-link {{ request()->routeIs('fee-sets.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Fee Set</a></li>
                                <li><a href="{{ route('scholarships.index') }}" class="nav-link {{ request()->routeIs('scholarships.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Scholarships</a></li>
                                <li><a href="{{ route('transports.index') }}" class="nav-link {{ request()->routeIs('transports.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Transport Fees</a></li>
                                <li><a href="{{ route('fees.collect') }}" class="nav-link {{ request()->routeIs('fees.collect') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Collect Payments</a></li>
                                <li><a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Student Payments</a></li>
                                <li class="nav-item has-treeview {{ menuOpen(['fees.due-report','fees.student-due-report','fees.discount-list']) ? 'menu-is-opening menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ menuActive(['fees.due-report','fees.student-due-report','fees.discount-list']) }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Fee Reports<i class="right fas fa-angle-left"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li><a href="{{ route('fees.due-report') }}" class="nav-link {{ request()->routeIs('fees.due-report') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Classwise Due Report</a></li>
                                        <li><a href="{{ route('fees.student-due-report') }}" class="nav-link {{ request()->routeIs('fees.student-due-report') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Students Due Report</a></li>
                                        <li><a href="{{ route('fees.discount-list') }}" class="nav-link {{ request()->routeIs('fees.discount-list') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Discount List</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <!-- Accounting -->
                        <li class="nav-item has-treeview {{ menuOpen(['account-groups.*','accounts-list.*','ledger.*','journal-entries.*','transactions.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['account-groups.*','accounts-list.*','ledger.*','journal-entries.*','transactions.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounting<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('account-groups.index') }}" class="nav-link {{ request()->routeIs('account-groups.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Account Groups</a></li>
                                <li><a href="{{ route('accounts-list.index') }}" class="nav-link {{ request()->routeIs('accounts-list.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Chart of Accounts</a></li>
                                <li><a href="{{ route('ledger.index') }}" class="nav-link {{ request()->routeIs('ledger.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Ledger</a></li>
                                <li><a href="{{ route('journal-entries.index') }}" class="nav-link {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Journal Entries</a></li>
                                <li><a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Transactions</a></li>
                            </ul>
                        </li>

                        <!-- Income & Expenses -->
                        <li class="nav-item has-treeview {{ menuOpen(['incomes.*','expenses.*','income-categories.*','expense-categories.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['incomes.*','expenses.*','income-categories.*','expense-categories.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Income & Expenses<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('incomes.index') }}" class="nav-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Incomes</a></li>
                                <li><a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Expenses</a></li>
                                <li class="nav-item has-treeview {{ menuOpen(['income-categories.*','expense-categories.*']) ? 'menu-is-opening menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ menuActive(['income-categories.*','expense-categories.*']) }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Categories<i class="right fas fa-angle-left"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li><a href="{{ route('income-categories.index') }}" class="nav-link {{ request()->routeIs('income-categories.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Income Categories</a></li>
                                        <li><a href="{{ route('expense-categories.index') }}" class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Expense Categories</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <!-- Banking & Cash -->
                        <li class="nav-item has-treeview {{ menuOpen(['bank-accounts.*','mobile-banking-accounts.*','hand-cash.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['bank-accounts.*','mobile-banking-accounts.*','hand-cash.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Banking & Cash<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('bank-accounts.index') }}" class="nav-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Bank Accounts</a></li>
                                <li><a href="{{ route('mobile-banking-accounts.index') }}" class="nav-link {{ request()->routeIs('mobile-banking-accounts.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Mobile Banking</a></li>
                                <li><a href="{{ route('hand-cash.index') }}" class="nav-link {{ request()->routeIs('hand-cash.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Hand Cash</a></li>
                            </ul>
                        </li>

                        <!-- Capital & Shareholders -->
                        <li class="nav-item has-treeview {{ menuOpen(['shareholders.*','shareholder-transactions.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['shareholders.*','shareholder-transactions.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Capital & Shareholders<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('shareholders.index') }}" class="nav-link {{ request()->routeIs('shareholders.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Shareholders</a></li>
                                <li><a href="{{ route('shareholder-transactions.create') }}" class="nav-link {{ request()->routeIs('shareholder-transactions.create') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Capital Transactions</a></li>
                            </ul>
                        </li>

                        <!-- Budget Control -->
                        <li class="nav-item has-treeview {{ menuOpen(['budget-allocations.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['budget-allocations.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Budget Control<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('budget-allocations.index') }}" class="nav-link {{ request()->routeIs('budget-allocations.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Budget Allocation</a></li>
                                <li><a href="{{ route('budget-allocations.report') }}" class="nav-link {{ request()->routeIs('budget-allocations.report') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Budget vs Actual</a></li>
                            </ul>
                        </li>

                        <!-- Financial Reports -->
                        <li class="nav-item has-treeview {{ menuOpen(['reports.trial-balance','reports.balance-sheet','reports.cash-flow','reports.income-expenditure','reports.receipt-payment','reports.cash-book','reports.day-book']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['reports.trial-balance','reports.balance-sheet','reports.cash-flow','reports.income-expenditure','reports.receipt-payment','reports.cash-book','reports.day-book']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Financial Reports<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('reports.trial-balance') }}" class="nav-link {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Trial Balance</a></li>
                                <li><a href="{{ route('reports.balance-sheet') }}" class="nav-link {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Balance Sheet</a></li>
                                <li><a href="{{ route('reports.cash-flow') }}" class="nav-link {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Cash Flow</a></li>
                                <li><a href="{{ route('reports.income-expenditure') }}" class="nav-link {{ request()->routeIs('reports.income-expenditure') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Income & Expenditure</a></li>
                                <li><a href="{{ route('reports.receipt-payment') }}" class="nav-link {{ request()->routeIs('reports.receipt-payment') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Receipt & Payment</a></li>
                                <li><a href="{{ route('reports.cash-book') }}" class="nav-link {{ request()->routeIs('reports.cash-book') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Cash Book</a></li>
                                <li><a href="{{ route('reports.day-book') }}" class="nav-link {{ request()->routeIs('reports.day-book') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Day Book</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- HR & Payroll -->
                <li class="nav-item has-treeview {{ menuOpen(['hr.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>HR & Payroll<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Dashboard</a></li>

                        <!-- Employees -->
                        <li class="nav-item has-treeview {{ menuOpen(['hr.employees.*','hr.departments.*','hr.designations.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Employees<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>All Employees</a></li>
                                <li><a href="{{ route('hr.employees.create') }}" class="nav-link {{ request()->routeIs('hr.employees.create') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Add Employee</a></li>
                                <li><a href="{{ route('hr.departments.index') }}" class="nav-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Departments</a></li>
                                <li><a href="{{ route('hr.designations.index') }}" class="nav-link {{ request()->routeIs('hr.designations.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Designations</a></li>
                            </ul>
                        </li>

                        <!-- Salary -->
                        <li class="nav-item has-treeview {{ menuOpen(['hr.salary-structures.*','hr.salary.defaults.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Salary<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.salary-structures.index') }}" class="nav-link {{ request()->routeIs('hr.salary-structures.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Salary Structures</a></li>
                                <li><a href="{{ route('hr.salary.defaults.index') }}" class="nav-link {{ request()->routeIs('hr.salary.defaults.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Designation Defaults</a></li>
                            </ul>
                        </li>

                        <!-- Payroll -->
                        <li class="nav-item has-treeview {{ menuOpen(['hr.payroll.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Payroll<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.payroll.index') }}" class="nav-link {{ request()->routeIs('hr.payroll.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Generate Payroll</a></li>
                            </ul>
                        </li>

                        <!-- Leave -->
                        <li class="nav-item has-treeview {{ menuOpen(['hr.leave.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Leave<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.leave.index') }}" class="nav-link {{ request()->routeIs('hr.leave.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Leave Requests</a></li>
                                <li><a href="{{ route('hr.leave.create') }}" class="nav-link {{ request()->routeIs('hr.leave.create') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>New Request</a></li>
                                <li><a href="{{ route('hr.leave.balances') }}" class="nav-link {{ request()->routeIs('hr.leave.balances') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Leave Balances</a></li>
                            </ul>
                        </li>

                        <!-- Reports -->
                        <li class="nav-item has-treeview {{ menuOpen(['hr.reports.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Reports<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.reports.salary-sheet') }}" class="nav-link {{ request()->routeIs('hr.reports.salary-sheet') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Salary Sheet</a></li>
                                <li><a href="{{ route('hr.reports.payroll-summary') }}" class="nav-link {{ request()->routeIs('hr.reports.payroll-summary') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Payroll Summary</a></li>
                                <li><a href="{{ route('hr.reports.leave') }}" class="nav-link {{ request()->routeIs('hr.reports.leave') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Leave Report</a></li>
                                <li><a href="{{ route('hr.reports.hierarchy') }}" class="nav-link {{ request()->routeIs('hr.reports.hierarchy') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Hierarchy Report</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Asset Management -->
                <li class="nav-item has-treeview {{ menuOpen(['assets.*','asset-categories.*','asset-purchases.*','asset-issues.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Asset Management<i class="right fas fa-angle-left"></i>
                            @php $assetCount = \App\Models\Asset::where('status', 'active')->sum('quantity'); @endphp
                            @if($assetCount > 0)
                                <span class="badge badge-info right">{{ $assetCount }}</span>
                            @endif
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li>
                            <a href="{{ route('asset-categories.index') }}" class="nav-link {{ request()->routeIs('asset-categories.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Assets List
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('asset-purchases.index') }}" class="nav-link {{ request()->routeIs('asset-purchases.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Purchases
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('asset-issues.index') }}" class="nav-link {{ request()->routeIs('asset-issues.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Issue Register
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('asset-issues.stock') }}" class="nav-link {{ request()->routeIs('asset-issues.stock') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Asset Stock
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- System & Settings -->
                <li class="nav-item has-treeview {{ menuOpen(['school-settings.*','id-card-templates.*','users.*','roles.*','permissions.*','division.*','district.*','police-station.*','post-office.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>System & Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Institute Settings -->
                        <li class="nav-item has-treeview {{ menuOpen(['school-settings.*','id-card-templates.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['school-settings.*','id-card-templates.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Institute Settings<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('school-settings.index') }}" class="nav-link {{ request()->routeIs('school-settings.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>School Settings</a></li>
                                <li><a href="{{ route('id-card-templates.index') }}" class="nav-link {{ request()->routeIs('id-card-templates.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>ID Card Templates</a></li>
                            </ul>
                        </li>

                        <!-- User & Roles -->
                        <li class="nav-item has-treeview {{ menuOpen(['users.*','roles.*','permissions.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['users.*','roles.*','permissions.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User & Roles<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Users</a></li>
                                <li><a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Roles</a></li>
                                <li><a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Permissions</a></li>
                            </ul>
                        </li>

                        <!-- Location Settings -->
                        <li class="nav-item has-treeview {{ menuOpen(['division.*','district.*','police-station.*','post-office.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ menuActive(['division.*','district.*','police-station.*','post-office.*']) }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Location Settings<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('division.index') }}" class="nav-link {{ request()->routeIs('division.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Division</a></li>
                                <li><a href="{{ route('district.index') }}" class="nav-link {{ request()->routeIs('district.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>District</a></li>
                                <li><a href="{{ route('police-station.index') }}" class="nav-link {{ request()->routeIs('police-station.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Police Station</a></li>
                                <li><a href="{{ route('post-office.index') }}" class="nav-link {{ request()->routeIs('post-office.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Post Office</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
