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
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Academics -->
                <li class="nav-item">
                    <a href="{{ route('academics.hub') }}"
                       class="nav-link {{ request()->routeIs('academics.hub', 'classes.*', 'sections.*', 'groups.*', 'sessions.*', 'subjects.*', 'classrooms.*', 'routines.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Academics</p>
                    </a>
                </li>

                <!-- Attendance -->
                <li class="nav-item">
                    <a href="{{ route('attendance.hub') }}"
                       class="nav-link {{ request()->routeIs('attendance.hub', 'attendance.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Attendance</p>
                    </a>
                </li>

                <!-- Students -->
                <li class="nav-item">
                    <a href="{{ route('students.hub') }}"
                       class="nav-link {{ request()->routeIs('students.hub', 'students.*', 'teacher-section-assignments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Students</p>
                    </a>
                </li>

                <!-- Fees & Accounts -->
                <li class="nav-item">
                    <a href="{{ route('fees.hub') }}"
                       class="nav-link {{ request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'transports.*', 'fees.*', 'payments.*', 'bank-accounts.*', 'mobile-banking-accounts.*', 'hand-cash.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Fees & Accounts</p>
                    </a>
                </li>

                <!-- Financials -->
                <li class="nav-item">
                    <a href="{{ route('financials.hub') }}"
                       class="nav-link {{ request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'transactions.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Financials</p>
                    </a>
                </li>

                <!-- Result Management -->
                <li class="nav-item">
                    <a href="{{ route('results.hub') }}"
                       class="nav-link {{ request()->routeIs('results.hub', 'exams.*', 'student-subjects.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-poll-h"></i>
                        <p>Result Management</p>
                    </a>
                </li>

                <!-- Shareholders -->
                <li class="nav-item">
                    <a href="{{ route('shareholders.hub') }}"
                       class="nav-link {{ request()->routeIs('shareholders.hub', 'shareholders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Shareholders</p>
                    </a>
                </li>

                <!-- HR & Payroll -->
                <li class="nav-item">
                    <a href="{{ route('hr.hub') }}"
                       class="nav-link {{ request()->routeIs('hr.hub', 'hr.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>HR & Payroll</p>
                    </a>
                </li>

                <!-- Accounts -->
                <li class="nav-item">
                    <a href="{{ route('accounts.hub') }}"
                       class="nav-link {{ request()->routeIs('accounts.hub', 'account-groups.*', 'accounts-list.*', 'ledger.*', 'accounting-periods.*', 'journal-entries.*', 'reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Accounts</p>
                    </a>
                </li>

                <!-- Assets -->
                <li class="nav-item">
                    <a href="{{ route('assets.hub') }}"
                       class="nav-link {{ request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Assets</p>
                    </a>
                </li>

                <!-- Inventory -->
                <li class="nav-item">
                    <a href="{{ route('inventory.hub') }}"
                       class="nav-link {{ request()->routeIs('inventory.hub', 'inventory.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Inventory</p>
                    </a>
                </li>

                <!-- Budget Control -->
                <li class="nav-item">
                    <a href="{{ route('budget.hub') }}"
                       class="nav-link {{ request()->routeIs('budget.hub', 'budget-allocations.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Budget Control</p>
                    </a>
                </li>

                <!-- Institute Settings -->
                <li class="nav-item">
                    <a href="{{ route('institute.hub') }}"
                       class="nav-link {{ request()->routeIs('institute.hub', 'school-settings.*', 'id-card-templates.*', 'buildings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Institute Settings</p>
                    </a>
                </li>

                <!-- Users & Roles -->
                <li class="nav-item">
                    <a href="{{ route('users.hub') }}"
                       class="nav-link {{ request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>User & Roles</p>
                    </a>
                </li>

                <!-- Location Settings -->
                <li class="nav-item">
                    <a href="{{ route('location.hub') }}"
                       class="nav-link {{ request()->routeIs('location.hub', 'division.*', 'district.*', 'police-station.*', 'post-office.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Location Settings</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
