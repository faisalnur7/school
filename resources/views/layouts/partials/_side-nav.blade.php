<!-- Main Sidebar Container -->
<aside class="main-sidebar modern-sidebar" id="mainSidebar">
    <!-- Brand Logo -->
    @php
        $sidebarSetting = \App\Models\SchoolSetting::current();
        $sidebarLogo = !empty($sidebarSetting->logo) ? asset($sidebarSetting->logo) : asset('assets/dist/img/AdminLTELogo.png');
        $sidebarInstituteName = $sidebarSetting->short_name;
        if (empty($sidebarInstituteName)) {
            $sidebarInstituteName = 'GCSC';
        }
        if (empty($sidebarInstituteName)) {
            $sidebarInstituteName = config('app.name', 'Institute');
        }
    @endphp
    <a href="{{ route('dashboard') }}" class="brand-link-modern">
        <div class="brand-icon-wrapper">
            <img src="{{ $sidebarLogo }}" alt="Logo" class="brand-image-modern">
        </div>
        <div class="brand-text-modern brand-copy-modern">
            <span class="brand-name-modern">{{ $sidebarInstituteName }}</span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar-modern">
        <!-- Sidebar user panel -->
        <div class="user-panel-modern">
            @php $currentUser = auth()->user(); @endphp
            <div class="user-avatar">
                <img src="{{ $currentUser?->image_url ?? asset('assets/dist/img/user2-160x160.jpg') }}" alt="User Image">
            </div>
            <div class="user-info">
                <span class="user-name">{{ $currentUser?->name ?? __('Guest') }}</span>
                <span class="user-role">{{ $currentUser?->roles?->first()?->name ?? __('User') }}</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-3">
            <ul class="nav-modern">

                <!-- Dashboard -->
                @if(auth()->user()?->hasPermission('view_dashboard'))
                <li class="nav-item-modern">
                    <a href="{{ route('dashboard') }}" class="nav-link-modern {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">{{ __('Dashboard') }}</span>
                        @if(request()->routeIs('dashboard'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Academics -->
                @if(auth()->user()?->hasPermission('view_academics'))
                <li class="nav-item-modern">
                    <a href="{{ route('academics.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('academics.hub', 'classes.*', 'sections.*', 'groups.*', 'sessions.*', 'subjects.*', 'classrooms.*', 'routines.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-school"></i></span>
                        <span class="nav-text">{{ __('Academics') }}</span>
                        
                        @if(request()->routeIs('academics.hub', 'classes.*', 'sections.*', 'groups.*', 'sessions.*', 'subjects.*', 'classrooms.*', 'routines.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Students -->
                @if(auth()->user()?->hasPermission('view_students'))
                <li class="nav-item-modern">
                    <a href="{{ route('students.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('students.hub', 'students.*', 'teacher-section-assignments.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
                        <span class="nav-text">{{ __('Students') }}</span>
                        
                        @if(request()->routeIs('students.hub', 'students.*', 'teacher-section-assignments.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Result Management -->
                @if(auth()->user()?->hasPermission('view_results'))
                <li class="nav-item-modern">
                    <a href="{{ route('results.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('results.hub', 'exams.*', 'student-subjects.*','result.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-poll"></i></span>
                        <span class="nav-text">{{ __('Result Management') }}</span>
                        
                        @if(request()->routeIs('results.hub', 'exams.*', 'student-subjects.*','result.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Attendance -->
                @if(auth()->user()?->hasPermission('view_attendance'))
                <li class="nav-item-modern">
                    <a href="{{ route('attendance.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('attendance.hub', 'attendance.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span>
                        <span class="nav-text">{{ __('Attendance') }}</span>
                        
                        @if(request()->routeIs('attendance.hub', 'attendance.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Fee Collection -->
                @if(auth()->user()?->hasPermission('view_fees'))
                <li class="nav-item-modern">
                    <a href="{{ route('fees.hub') }}"
                        class="nav-link-modern {{ request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'fees.*', 'payments.*', 'fees.payment-report', 'fees.payment-report.pdf') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-coins"></i></span>
                        <span class="nav-text">{{ __('Fee Collection') }}</span>
                        
                        @if(request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'fees.*', 'payments.*', 'fees.payment-report', 'fees.payment-report.pdf'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Accounts -->
                @if(auth()->user()?->hasPermission('view_accounts'))
                <li class="nav-item-modern">
                    <a href="{{ route('accounts.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('accounts.hub', 'account-groups.*', 'accounts-list.*', 'ledger.*', 'accounting-periods.*', 'journal-entries.*', 'reports.*', 'bank-accounts.*', 'mobile-banking-accounts.*', 'hand-cash.*', 'transactions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-book"></i></span>
                        <span class="nav-text">{{ __('Accounts') }}</span>
                        
                        @if(request()->routeIs('accounts.hub', 'account-groups.*', 'accounts-list.*', 'ledger.*', 'accounting-periods.*', 'journal-entries.*', 'reports.*', 'bank-accounts.*', 'mobile-banking-accounts.*', 'hand-cash.*', 'transactions.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Inventory -->
                @if(auth()->user()?->hasPermission('view_inventory'))
                <li class="nav-item-modern">
                    <a href="{{ route('inventory.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('inventory.hub', 'inventory.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-boxes"></i></span>
                        <span class="nav-text">{{ __('Inventory') }}</span>
                        
                        @if(request()->routeIs('inventory.hub', 'inventory.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Financials -->
                @if(auth()->user()?->hasPermission('view_financials'))
                <li class="nav-item-modern">
                    <a href="{{ route('financials.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-text">{{ __('Financials') }}</span>
                        
                        @if(request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Shareholders -->
                @if(auth()->user()?->hasPermission('view_shareholders'))
                <li class="nav-item-modern">
                    <a href="{{ route('shareholders.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('shareholders.hub', 'shareholders.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                        <span class="nav-text">{{ __('Shareholders') }}</span>
                        
                        @if(request()->routeIs('shareholders.hub', 'shareholders.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- HR & Payroll -->
                @if(auth()->user()?->hasPermission('view_hr'))
                <li class="nav-item-modern">
                    <a href="{{ route('hr.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('hr.hub', 'hr.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">{{ __('HR & Payroll') }}</span>
                        
                        @if(request()->routeIs('hr.hub', 'hr.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Assets -->
                @if(auth()->user()?->hasPermission('view_assets'))
                <li class="nav-item-modern">
                    <a href="{{ route('assets.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-building"></i></span>
                        <span class="nav-text">{{ __('Assets') }}</span>
                        
                        @if(request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Budget Control -->
                @if(auth()->user()?->hasPermission('view_budget'))
                {{-- <li class="nav-item-modern">
                    <a href="{{ route('budget.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('budget.hub', 'budget-allocations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Budget Control</span>
                        
                        @if(request()->routeIs('budget.hub', 'budget-allocations.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li> --}}
                @endif

                <!-- Institute Settings -->
                @if(auth()->user()?->hasPermission('view_institute_settings'))
                <li class="nav-item-modern">
                    <a href="{{ route('institute.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('institute.hub', 'school-settings.*', 'certificates.*', 'id-card-templates.*', 'buildings.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                        <span class="nav-text">{{ __('Institute Settings') }}</span>
                        
                        @if(request()->routeIs('institute.hub', 'school-settings.*', 'certificates.*', 'id-card-templates.*', 'buildings.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Users & Roles -->
                @if(auth()->user()?->hasPermission('view_users'))
                <li class="nav-item-modern">
                    <a href="{{ route('users.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                        <span class="nav-text">{{ __('User & Roles') }}</span>
                        
                        @if(request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Audit Trail -->
                @if(auth()->user()?->hasPermission('view_audit_trail'))
                <li class="nav-item-modern">
                    <a href="{{ route('audit-trails.index') }}"
                       class="nav-link-modern {{ request()->routeIs('audit-trails.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                        <span class="nav-text">{{ __('Audit Trail') }}</span>

                        @if(request()->routeIs('audit-trails.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Location Settings -->
                @if(auth()->user()?->hasPermission('view_location_settings'))
                <li class="nav-item-modern">
                    <a href="{{ route('location.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('location.hub', 'division.*', 'district.*', 'police-station.*', 'post-office.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span class="nav-text">{{ __('Location Settings') }}</span>
                        
                        @if(request()->routeIs('location.hub', 'division.*', 'district.*', 'police-station.*', 'post-office.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Communications -->
                @if(auth()->user()?->hasPermission('view_communications'))
                <li class="nav-item-modern">
                    <a href="{{ route('communications.index') }}"
                       class="nav-link-modern {{ request()->routeIs('communications.*', 'chat.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-comments"></i></span>
                        <span class="nav-text">{{ __('Communications') }}</span>
                        @if(request()->routeIs('communications.*', 'chat.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Website Management -->
                {{-- @if(auth()->user()?->hasPermission('view_website_management'))
                <li class="nav-item-modern">
                    <a href="{{ route('website.cms.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('website.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-globe"></i></span>
                        <span class="nav-text">Website Management</span>
                        
                        @if(request()->routeIs('website.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif --}}

            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
