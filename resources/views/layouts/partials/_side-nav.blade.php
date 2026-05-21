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
                <img src="{{ auth()->user()->image_url }}" class="img-circle elevation-2" style="width:34px;height:34px;object-fit:cover;"
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
                @if(auth()->user()?->hasPermission('view_dashboard'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @endif

                <!-- Academics -->
                @if(auth()->user()?->hasPermission('view_academics'))
                <li class="nav-item">
                    <a href="{{ route('academics.hub') }}"
                       class="nav-link {{ request()->routeIs('academics.hub', 'classes.*', 'sections.*', 'groups.*', 'sessions.*', 'subjects.*', 'classrooms.*', 'routines.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Academics</p>
                    </a>
                </li>
                @endif

                <!-- Attendance -->
                @if(auth()->user()?->hasPermission('view_attendance'))
                <li class="nav-item">
                    <a href="{{ route('attendance.hub') }}"
                       class="nav-link {{ request()->routeIs('attendance.hub', 'attendance.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                @endif

                <!-- Students -->
                @if(auth()->user()?->hasPermission('view_students'))
                <li class="nav-item">
                    <a href="{{ route('students.hub') }}"
                       class="nav-link {{ request()->routeIs('students.hub', 'students.*', 'teacher-section-assignments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Students</p>
                    </a>
                </li>
                @endif

                <!-- Fees & Accounts -->
                @if(auth()->user()?->hasPermission('view_fees'))
                <li class="nav-item">
                    <a href="{{ route('fees.hub') }}"
                        class="nav-link {{ request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'fees.*', 'payments.*', 'fees.payment-report', 'fees.payment-report.pdf') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Fee Collection</p>
                    </a>
                </li>
                @endif

                <!-- Financials -->
                @if(auth()->user()?->hasPermission('view_financials'))
                <li class="nav-item">
                    <a href="{{ route('financials.hub') }}"
                       class="nav-link {{ request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'transactions.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Financials</p>
                    </a>
                </li>
                @endif

                <!-- Result Management -->
                @if(auth()->user()?->hasPermission('view_results'))
                <li class="nav-item">
                    <a href="{{ route('results.hub') }}"
                       class="nav-link {{ request()->routeIs('results.hub', 'exams.*', 'student-subjects.*','result.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-poll-h"></i>
                        <p>Result Management</p>
                    </a>
                </li>
                @endif

                <!-- Shareholders -->
                @if(auth()->user()?->hasPermission('view_shareholders'))
                <li class="nav-item">
                    <a href="{{ route('shareholders.hub') }}"
                       class="nav-link {{ request()->routeIs('shareholders.hub', 'shareholders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Shareholders</p>
                    </a>
                </li>
                @endif

                <!-- HR & Payroll -->
                @if(auth()->user()?->hasPermission('view_hr'))
                <li class="nav-item">
                    <a href="{{ route('hr.hub') }}"
                       class="nav-link {{ request()->routeIs('hr.hub', 'hr.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>HR & Payroll</p>
                    </a>
                </li>
                @endif

                <!-- Accounts -->
                @if(auth()->user()?->hasPermission('view_accounts'))
                <li class="nav-item">
                    <a href="{{ route('accounts.hub') }}"
                       class="nav-link {{ request()->routeIs('accounts.hub', 'account-groups.*', 'accounts-list.*', 'ledger.*', 'accounting-periods.*', 'journal-entries.*', 'reports.*', 'bank-accounts.*', 'mobile-banking-accounts.*', 'hand-cash.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Accounts</p>
                    </a>
                </li>
                @endif

                <!-- Assets -->
                @if(auth()->user()?->hasPermission('view_assets'))
                <li class="nav-item">
                    <a href="{{ route('assets.hub') }}"
                       class="nav-link {{ request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Assets</p>
                    </a>
                </li>
                @endif

                <!-- Inventory -->
                @if(auth()->user()?->hasPermission('view_inventory'))
                <li class="nav-item">
                    <a href="{{ route('inventory.hub') }}"
                       class="nav-link {{ request()->routeIs('inventory.hub', 'inventory.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Inventory</p>
                    </a>
                </li>
                @endif

                <!-- Budget Control -->
                @if(auth()->user()?->hasPermission('view_budget'))
                {{-- <li class="nav-item">
                    <a href="{{ route('budget.hub') }}"
                       class="nav-link {{ request()->routeIs('budget.hub', 'budget-allocations.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Budget Control</p>
                    </a>
                </li> --}}
                @endif

                <!-- Institute Settings -->
                @if(auth()->user()?->hasPermission('view_institute_settings'))
                <li class="nav-item">
                    <a href="{{ route('institute.hub') }}"
                       class="nav-link {{ request()->routeIs('institute.hub', 'school-settings.*', 'id-card-templates.*', 'buildings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Institute Settings</p>
                    </a>
                </li>
                @endif

                <!-- Users & Roles -->
                @if(auth()->user()?->hasPermission('view_users'))
                <li class="nav-item">
                    <a href="{{ route('users.hub') }}"
                       class="nav-link {{ request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>User & Roles</p>
                    </a>
                </li>
                @endif

                <!-- Location Settings -->
                @if(auth()->user()?->hasPermission('view_location_settings'))
                <li class="nav-item">
                    <a href="{{ route('location.hub') }}"
                       class="nav-link {{ request()->routeIs('location.hub', 'division.*', 'district.*', 'police-station.*', 'post-office.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Location Settings</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
