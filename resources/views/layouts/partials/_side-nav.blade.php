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
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Admissions -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Admissions<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('admissions.applications') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Applications</a></li>
                        <li><a href="{{ route('admissions.process') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Process Tracking</a></li>
                        <li><a href="{{ route('admissions.documents') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Document Verification</a></li>
                        <li><a href="{{ route('admissions.interviews') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Interview Scheduling</a></li>
                        <li><a href="{{ route('admissions.portal') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Online Portal</a></li>
                    </ul>
                </li> --}}

                <!-- Academics -->
                <li class="nav-item has-treeview  {{ menuOpen(['classes.*','sections.*','groups.*','sessions.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Academics<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('sessions.index') }}" class="nav-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Sessions</a></li>
                        <li><a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Classes</a></li>
                        <li><a href="{{ route('sections.index') }}" class="nav-link {{ request()->routeIs('sections.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Sections</a></li>
                        <li><a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Groups</a></li>
                        {{-- <li><a href="{{ route('subjects.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Subjects</a></li>
                        <li><a href="{{ route('routines.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Class Routine</a></li>
                        <li><a href="{{ route('classrooms.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Class Room</a></li> --}}
                    </ul>
                </li>

                <!-- Online Learning & LMS -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-laptop"></i>
                        <p>Online Learning<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('lms.courses') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Course Materials</a></li>
                        <li><a href="{{ route('lms.assignments') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Assignments</a></li>
                        <li><a href="{{ route('lms.digital_classroom') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Digital Classroom</a></li>
                        <li><a href="{{ route('lms.video_conference') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Video Conferencing</a></li>
                        <li><a href="{{ route('lms.content_management') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Content Management</a></li>
                    </ul>
                </li> --}}

                <!-- Lesson Plan -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Lesson Plan<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('lessons.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Lessons</a></li>
                        <li><a href="{{ route('topics.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Topics</a></li>
                        <li><a href="{{ route('lessonplans.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Lesson Plans</a></li>
                        <li><a href="{{ route('lessonplan.overview') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Plan Overview</a></li>
                    </ul>
                </li> --}}

                <!-- Students -->
                <li class="nav-item has-treeview {{ menuOpen(['students.*','attendance.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Students<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Student List</a></li>
                        <li><a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Add Student</a></li>
                        {{-- <li><a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Attendance</a></li> --}}
                        <li><a href="{{ route('students.id-cards') }}" class="nav-link {{ request()->routeIs('students.id-cards') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Generate ID Cards</a></li>
                        {{-- <li><a href="{{ route('students.progress') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Progress Tracking</a></li> --}}
                        {{-- <li><a href="{{ route('reports.student') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Reports</a></li> --}}
                    </ul>
                </li>

                <!-- Student Services -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-hands-helping"></i>
                        <p>Student Services<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('health.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Health Records</a></li>
                        <li><a href="{{ route('discipline.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Discipline Management</a></li>
                        <li><a href="{{ route('activities.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Student Activities</a></li>
                        <li><a href="{{ route('sports.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Sports Management</a></li>
                        <li><a href="{{ route('clubs.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Club Management</a></li>
                        <li><a href="{{ route('counseling.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Counseling Records</a></li>
                    </ul>
                </li> --}}

                <!-- Fees & Accounts -->
                @php
    $feeSettings = ['fee-categories.*','fee-sets.*','scholarships.*','transports.*'];
    $feeOperations = ['fees.collect','payments.*'];
    $reports = ['fees.due-report','fees.student-due-report','fees.discount-list'];
    $accounts = ['bank-accounts.*','mobile-banking-accounts.*','hand-cash.*'];
@endphp

<li class="nav-item has-treeview {{ menuOpen(array_merge($feeSettings, $feeOperations, $reports, $accounts)) }}">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-money-bill"></i>
        <p>Fees & Accounts <i class="right fas fa-angle-left"></i></p>
    </a>

    <ul class="nav nav-treeview">

        {{-- Fee Settings --}}
        <li class="nav-item has-treeview {{ menuOpen($feeSettings) }}">
            <a href="#" class="nav-link {{ menuActive($feeSettings) }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Fee Settings <i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">
                <li><a href="{{ route('fee-categories.index') }}" class="nav-link {{ request()->routeIs('fee-categories.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Fee Category</a></li>
                <li><a href="{{ route('fee-sets.index') }}" class="nav-link {{ request()->routeIs('fee-sets.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Fee Set</a></li>
                <li><a href="{{ route('scholarships.index') }}" class="nav-link {{ request()->routeIs('scholarships.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Scholarships</a></li>
                <li><a href="{{ route('transports.index') }}" class="nav-link {{ request()->routeIs('transports.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Transport Fees</a></li>
            </ul>
        </li>

        {{-- Fee Operations --}}
        <li>
            <a href="{{ route('fees.collect') }}" class="nav-link {{ request()->routeIs('fees.collect') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>Collect Payments
            </a>
        </li>

        <li>
            <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>Student Payments
            </a>
        </li>

        {{-- Reports --}}
        <li class="nav-item has-treeview {{ menuOpen($reports) }}">
            <a href="#" class="nav-link {{ menuActive($reports) }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Reports <i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">
                <li><a href="{{ route('fees.due-report') }}" class="nav-link {{ request()->routeIs('fees.due-report') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Classwise Due Report</a></li>
                <li><a href="{{ route('fees.student-due-report') }}" class="nav-link {{ request()->routeIs('fees.student-due-report') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Students Due Report</a></li>
                <li><a href="{{ route('fees.discount-list') }}" class="nav-link {{ request()->routeIs('fees.discount-list') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Discount List</a></li>
            </ul>
        </li>

        {{-- Account Management --}}
        <li class="nav-item has-treeview {{ menuOpen($accounts) }}">
            <a href="#" class="nav-link {{ menuActive($accounts) }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Account Management <i class="right fas fa-angle-left"></i></p>
            </a>

            <ul class="nav nav-treeview">
                <li><a href="{{ route('bank-accounts.index') }}" class="nav-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Bank Accounts</a></li>
                <li><a href="{{ route('mobile-banking-accounts.index') }}" class="nav-link {{ request()->routeIs('mobile-banking-accounts.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Mobile Banking</a></li>
                <li><a href="{{ route('hand-cash.index') }}" class="nav-link {{ request()->routeIs('hand-cash.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Hand Cash</a></li>
            </ul>
        </li>

    </ul>
</li>
                {{-- Incomes & Expenses --}}
                <li class="nav-item has-treeview {{ menuOpen(['incomes.*', 'expenses.*', 'transactions.*', 'income-categories.*', 'expense-categories.*','shareholder-transactions.create']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Financials<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li>
                            <a href="{{ route('incomes.index') }}" class="nav-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Incomes
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Expenses
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('shareholder-transactions.create') }}" class="nav-link {{ request()->routeIs('shareholder-transactions.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Capital
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Transactions
                            </a>
                        </li>

                        <!-- Categories -->
                        <li class="nav-item has-treeview {{ menuOpen(['income-categories.*', 'expense-categories.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li>
                                    <a href="{{ route('income-categories.index') }}" class="nav-link {{ request()->routeIs('income-categories.*') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>Income Categories
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('expense-categories.index') }}" class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                                        <i class="far fa-dot-circle nav-icon"></i>Expense Categories
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- Procurement & Budget -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Procurement<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('procurement.orders') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Purchase Orders</a></li>
                        <li><a href="{{ route('procurement.vendors') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Vendor Management</a></li>
                        <li><a href="{{ route('procurement.budget') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Budget Tracking</a></li>
                        <li><a href="{{ route('procurement.allocation') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Budget Allocation</a></li>
                    </ul>
                </li> --}}

                <!-- Exams -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Exams<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('exams.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Exam List</a></li>
                        <li><a href="{{ route('marks.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Marks</a></li>
                        <li><a href="{{ route('onlineexams.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Online Exam</a></li>
                    </ul>
                </li> --}}

                <!-- Result Management -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>Result Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('results.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>All Results</a></li>
                        <li><a href="{{ route('results.create') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Add Result</a></li>
                        <li><a href="{{ route('results.reports') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Result Reports</a></li>
                    </ul>
                </li> --}}

                <!-- Assessment & Analytics -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Analytics<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('analytics.dashboard') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Analytics Dashboard</a></li>
                        <li><a href="{{ route('analytics.performance') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Performance Tracking</a></li>
                        <li><a href="{{ route('analytics.trends') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Trend Analysis</a></li>
                        <li><a href="{{ route('analytics.predictive') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Predictive Analytics</a></li>
                        <li><a href="{{ route('analytics.reports') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Advanced Reports</a></li>
                    </ul>
                </li> --}}

                <!-- Library -->
                {{-- <li class="nav-item">
                    <a href="{{ route('library.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-book-reader"></i>
                        <p>Library</p>
                    </a>
                </li> --}}

                <!-- Transport -->
                {{-- <li class="nav-item">
                    <a href="{{ route('transport.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-bus"></i>
                        <p>Transport</p>
                    </a>
                </li> --}}

                <!-- Dormitory -->
                {{-- <li class="nav-item">
                    <a href="{{ route('dormitory.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-bed"></i>
                        <p>Dormitory</p>
                    </a>
                </li> --}}

                <!-- Asset Management -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Asset Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('assets.property') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Property Tracking</a></li>
                        <li><a href="{{ route('assets.equipment') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Equipment</a></li>
                        <li><a href="{{ route('assets.maintenance') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Maintenance</a></li>
                        <li><a href="{{ route('assets.depreciation') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Depreciation</a></li>
                        <li><a href="{{ route('facilities.booking') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Facility Booking</a></li>
                    </ul>
                </li> --}}

                <!-- Visitor Management -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-check"></i>
                        <p>Visitor Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('visitors.registration') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Registration</a></li>
                        <li><a href="{{ route('visitors.tracking') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Visitor Tracking</a></li>
                        <li><a href="{{ route('visitors.appointments') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Appointments</a></li>
                        <li><a href="{{ route('visitors.security') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Security Clearance</a></li>
                    </ul>
                </li> --}}

                <!-- Reports -->
                {{-- <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Reports</p>
                    </a>
                </li> --}}

                <!-- HR & Payroll -->
                <li class="nav-item has-treeview {{ menuOpen(['hr.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>HR & Payroll<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Dashboard</a></li>

                        {{-- Employees --}}
                        <li class="nav-item has-treeview {{ menuOpen(['hr.employees.*','hr.designations.*','hr.departments.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Employees<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>All Employees</a></li>
                                <li><a href="{{ route('hr.employees.create') }}" class="nav-link {{ request()->routeIs('hr.employees.create') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Add Employee</a></li>
                                <li><a href="{{ route('hr.departments.index') }}" class="nav-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Departments</a></li>
                                <li><a href="{{ route('hr.designations.index') }}" class="nav-link {{ request()->routeIs('hr.designations.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Designations</a></li>
                            </ul>
                        </li>

                        {{-- Salary --}}
                        <li class="nav-item has-treeview {{ menuOpen(['hr.salary-structures.*','hr.salary.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Salary<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.salary-structures.index') }}" class="nav-link {{ request()->routeIs('hr.salary-structures.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Salary Structures</a></li>
                                <li><a href="{{ route('hr.salary.defaults.index') }}" class="nav-link {{ request()->routeIs('hr.salary.defaults.*') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Designation Defaults</a></li>
                            </ul>
                        </li>

                        {{-- Payroll --}}
                        <li class="nav-item has-treeview {{ menuOpen(['hr.payroll.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Payroll<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.payroll.index') }}" class="nav-link {{ request()->routeIs('hr.payroll.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Generate Payroll</a></li>
                            </ul>
                        </li>

                        {{-- Leave --}}
                        <li class="nav-item has-treeview {{ menuOpen(['hr.leave.*']) ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Leave<i class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview">
                                <li><a href="{{ route('hr.leave.index') }}" class="nav-link {{ request()->routeIs('hr.leave.index') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Leave Requests</a></li>
                                <li><a href="{{ route('hr.leave.create') }}" class="nav-link {{ request()->routeIs('hr.leave.create') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>New Request</a></li>
                                <li><a href="{{ route('hr.leave.balances') }}" class="nav-link {{ request()->routeIs('hr.leave.balances') ? 'active' : '' }}"><i class="far fa-dot-circle nav-icon"></i>Leave Balances</a></li>
                            </ul>
                        </li>

                        {{-- Reports --}}
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

                <!-- Communication -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>Communication<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('chat.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Chat</a></li>
                        <li><a href="{{ route('notice.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Notice</a></li>
                        <li><a href="{{ route('emailsms.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Email/SMS</a></li>
                        <li><a href="{{ route('communication.notifications') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Push Notifications</a></li>
                        <li><a href="{{ route('social.integration') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Social Media</a></li>
                    </ul>
                </li> --}}

                <!-- Parent Portal -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Parent Portal<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('parents.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Parents List</a></li>
                        <li><a href="{{ route('communication.parent') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Communication</a></li>
                    </ul>
                </li> --}}

                {{-- <!-- Alumni -->
                <li class="nav-item">
                    <a href="{{ route('alumni.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-clock"></i>
                        <p>Alumni</p>
                    </a>
                </li>

                <!-- Inventory -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Inventory<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('inventory.items') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Items</a></li>
                        <li><a href="{{ route('inventory.suppliers') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Suppliers</a></li>
                        <li><a href="{{ route('inventory.requests') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Requests</a></li>
                    </ul>
                </li>

                <!-- Event Management -->
                <li class="nav-item">
                    <a href="{{ route('events.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Event Management</p>
                    </a>
                </li> --}}

                <!-- Shareholders -->
                <li class="nav-item has-treeview {{ menuOpen(['shareholders.index','shareholders.create']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Shareholders<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li>
                            <a href="{{ route('shareholders.index') }}" class="nav-link {{ request()->routeIs('shareholders.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>All Shareholders
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shareholders.create') }}" class="nav-link {{ request()->routeIs('shareholders.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>Add Shareholder
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Accounts Module -->
                <li class="nav-item has-treeview {{ menuOpen(['account-groups.*','accounts-list.*','ledger.*','reports.*','journal-entries.*','accounting-periods.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Accounts<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('account-groups.index') }}" class="nav-link {{ request()->routeIs('account-groups.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Account Groups</a></li>
                        <li><a href="{{ route('accounts-list.index') }}" class="nav-link {{ request()->routeIs('accounts-list.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Chart of Accounts</a></li>
                        <li><a href="{{ route('accounting-periods.index') }}" class="nav-link {{ request()->routeIs('accounting-periods.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Accounting Periods</a></li>
                        {{-- <li><a href="{{ route('journal-entries.index') }}" class="nav-link {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Journal Entries</a></li> --}}
                        <li><a href="{{ route('ledger.index') }}" class="nav-link {{ request()->routeIs('ledger.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Ledger</a></li>
                        <li><a href="{{ route('reports.trial-balance') }}" class="nav-link {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Trial Balance</a></li>
                        <li><a href="{{ route('reports.balance-sheet') }}" class="nav-link {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Balance Sheet</a></li>
                        <li><a href="{{ route('reports.cash-book') }}" class="nav-link {{ request()->routeIs('reports.cash-book') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Cash Book</a></li>
                        <li><a href="{{ route('reports.day-book') }}" class="nav-link {{ request()->routeIs('reports.day-book') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Day Book</a></li>
                        <li><a href="{{ route('reports.income-expenditure') }}" class="nav-link {{ request()->routeIs('reports.income-expenditure') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Income & Expenditure</a></li>
                        <li><a href="{{ route('reports.cash-summary') }}" class="nav-link {{ request()->routeIs('reports.cash-summary') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Cash Summary</a></li>
                        <li><a href="{{ route('reports.receipt-payment') }}" class="nav-link {{ request()->routeIs('reports.receipt-payment') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Receipt & Payment</a></li>
                        <li><a href="{{ route('reports.cash-flow') }}" class="nav-link {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Cash Flow</a></li>
                        <li><a href="{{ route('reports.chart-of-accounts') }}" class="nav-link {{ request()->routeIs('reports.chart-of-accounts') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Chart of Accounts</a></li>
                        <li><a href="{{ route('reports.headwise-transactions') }}" class="nav-link {{ request()->routeIs('reports.headwise-transactions') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Headwise Transactions</a></li>
                    </ul>
                </li>


                <!-- Asset Management -->
                <li class="nav-item has-treeview {{ menuOpen(['assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Assets<i class="right fas fa-angle-left"></i>
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
                            <a href="{{ route('asset-issues.index') }}" class="nav-link {{ request()->routeIs('asset-issues.index') ? 'active' : '' }}">
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

                <!-- Budget Control -->
                <li class="nav-item has-treeview {{ menuOpen(['budget-allocations.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Budget Control<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('budget-allocations.index') }}" class="nav-link {{ request()->routeIs('budget-allocations.index') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Budget Allocation</a></li>
                        <li><a href="{{ route('budget-allocations.report') }}" class="nav-link {{ request()->routeIs('budget-allocations.report') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Budget vs Actual</a></li>
                    </ul>
                </li>

                <!-- ID Cards & Certificates -->
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-id-card"></i>
                        <p>ID Cards & Certificates<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('idcards.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>ID Cards</a></li>
                        <li><a href="{{ route('certificates.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Certificates</a></li>
                    </ul>
                </li> --}}

                {{-- <!-- Timetable Generator -->
                <li class="nav-item">
                    <a href="{{ route('timetable.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>Timetable Generator</p>
                    </a>
                </li>

                <!-- Mobile App Management -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-mobile-alt"></i>
                        <p>Mobile App<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('mobile.notifications') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Push Notifications</a></li>
                        <li><a href="{{ route('mobile.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>App Settings</a></li>
                        <li><a href="{{ route('mobile.offline') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Offline Management</a></li>
                    </ul>
                </li>

                <!-- Security & Compliance -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Security & Compliance<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('security.access_control') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Access Control</a></li>
                        <li><a href="{{ route('security.cctv') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>CCTV Integration</a></li>
                        <li><a href="{{ route('security.emergency') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Emergency Response</a></li>
                        <li><a href="{{ route('privacy.gdpr') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Data Privacy</a></li>
                        <li><a href="{{ route('security.audit') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Audit Trails</a></li>
                    </ul>
                </li> --}}

                <!-- Institute Settings -->
                <li class="nav-item has-treeview {{ menuOpen(['school-settings.*','id-card-templates.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Institute Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li>
                            <a href="{{ route('school-settings.index') }}" class="nav-link {{ request()->routeIs('school-settings.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>School Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('id-card-templates.index') }}" class="nav-link {{ request()->routeIs('id-card-templates.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>ID Card Templates
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Users & Roles -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>User & Roles<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('users.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Users</a></li>
                        <li><a href="{{ route('roles.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Roles</a></li>
                        <li><a href="{{ route('permissions.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Permissions</a></li>
                    </ul>
                </li>

                <!-- Settings -->
                <li class="nav-item has-treeview {{ menuOpen(['division.*','district.*','police-station.*','post-office.*']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Location Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('division.index') }}" class="nav-link  {{ request()->routeIs('division.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Division</a></li>
                        <li><a href="{{ route('district.index') }}" class="nav-link  {{ request()->routeIs('district.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>District</a></li>
                        <li><a href="{{ route('police-station.index') }}" class="nav-link  {{ request()->routeIs('police-station.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Police Station</a></li>
                        <li><a href="{{ route('post-office.index') }}" class="nav-link  {{ request()->routeIs('post-office.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>Post Office</a></li>
                    </ul>
                </li>

                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>System Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('general.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>General</a></li>
                        <li><a href="{{ route('email.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Email</a></li>
                        <li><a href="{{ route('payment.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Payments</a></li>
                        <li><a href="{{ route('backup.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Backup</a></li>
                    </ul>
                </li> --}}

            </ul>
        </nav>
    </div>
</aside>
