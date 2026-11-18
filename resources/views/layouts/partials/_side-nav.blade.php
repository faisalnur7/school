<!-- Sidebar styles (kept with the markup so they always load with it) -->
<style>
    /* ---- Compact layout: fits the full menu on ~768px-tall screens without scrolling ---- */

    /* Brand bar: 56px tall (was ~92px). It stays fixed; only the menu below scrolls. */
    #mainSidebar .brand-link-modern {
        flex-shrink: 0;
        min-height: 56px;
        padding: 10px 16px 10px 18px !important;
        gap: 10px;
    }

    #mainSidebar .brand-icon-wrapper {
        width: 36px !important;
        height: 36px !important;
        border-radius: 9px;
        flex-shrink: 0;
    }

    #mainSidebar .brand-image-modern {
        width: 30px !important;
        height: 30px !important;
    }

    #mainSidebar .brand-name-modern {
        font-size: 0.95rem;
    }

    /* Menu area */
    #mainSidebar .sidebar-modern {
        padding: 6px 10px 12px;
    }

    #mainSidebar .nav-modern {
        gap: 1px;
    }

    /* Menu rows: 34px tall */
    #mainSidebar .nav-link-modern {
        height: 34px;
        padding: 0 12px;
        line-height: 1;
    }

    body.sidebar-collapse #mainSidebar .brand-link-modern {
        padding: 10px 0 !important;
        justify-content: center;
    }

    body.sidebar-collapse #mainSidebar .nav-link-modern {
        padding: 0;
    }

    /* ---- Group headings ---- */
    #mainSidebar .nav-modern > li.nav-section-modern {
        display: block !important;
        list-style: none;
        margin: 0;
        padding: 12px 12px 4px !important;
        font-size: 0.65rem !important;
        line-height: 1.2;
        font-weight: 700 !important;
        letter-spacing: 0.08em;
        text-transform: uppercase !important;
        color: rgba(255, 255, 255, 0.5) !important;
        opacity: 1 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        pointer-events: none;
        user-select: none;
    }

    #mainSidebar .nav-modern > li.nav-section-modern .nav-section-text {
        color: inherit !important;
    }

    html[data-theme='light'] #mainSidebar .nav-modern > li.nav-section-modern {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* Collapsed sidebar: show a thin divider instead of the text */
    body.sidebar-collapse #mainSidebar .nav-modern > li.nav-section-modern {
        padding: 8px 14px !important;
    }

    body.sidebar-collapse #mainSidebar .nav-section-text {
        display: none !important;
    }

    body.sidebar-collapse #mainSidebar .nav-modern > li.nav-section-modern::after {
        content: '';
        display: block;
        height: 1px;
        background: rgba(255, 255, 255, 0.14);
    }
</style>

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
        {{-- The user panel was removed from the sidebar: the top navbar already shows the user's avatar and name. --}}
        @php
            $currentUser = auth()->user();

            // A group heading is only shown when the user can see at least one item in it.
            $navCan = fn (string ...$permissions) => collect($permissions)->contains(fn ($permission) => (bool) $currentUser?->hasPermission($permission));
            $isPlatformAdmin = (bool) $currentUser?->isPlatformAdmin();

            $showAcademicGroup = $navCan('view_academics', 'view_admission_management', 'view_students', 'view_attendance', 'view_results');
            // Budget is disabled for now, so it does not count towards the Finance heading.
            $showFinanceGroup = $navCan('view_fees', 'view_financials', 'view_accounts');
            $showOperationsGroup = $navCan('view_hr', 'view_inventory', 'view_assets', 'view_communications');
            $showAdministrationGroup = $navCan('view_users', 'view_institute_settings');
        @endphp

        <!-- Sidebar Menu -->
        <nav class="mt-1">
            <ul class="nav-modern">

                <!-- Dashboard -->
                @if($currentUser?->hasPermission('view_dashboard'))
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

                {{-- ================= ACADEMIC ================= --}}
                @if($showAcademicGroup)
                <li class="nav-section-modern"><span class="nav-section-text">{{ __('Academic') }}</span></li>
                @endif

                <!-- Academics -->
                @if($currentUser?->hasPermission('view_academics'))
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

                <!-- Admissions -->
                @if($currentUser?->hasPermission('view_admission_management'))
                <li class="nav-item-modern">
                    <a href="{{ route('admissions.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('admissions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                        <span class="nav-text">{{ __('Admissions') }}</span>
                        @if(request()->routeIs('admissions.*'))<span class="nav-indicator"></span>@endif
                    </a>
                </li>
                @endif

                <!-- Students -->
                @if($currentUser?->hasPermission('view_students'))
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

                <!-- Attendance -->
                @if($currentUser?->hasPermission('view_attendance'))
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

                <!-- Exams & Results -->
                @if($currentUser?->hasPermission('view_results'))
                <li class="nav-item-modern">
                    <a href="{{ route('results.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('results.hub', 'exams.*', 'student-subjects.*','result.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-poll"></i></span>
                        <span class="nav-text">{{ __('Exams & Results') }}</span>
                        @if(request()->routeIs('results.hub', 'exams.*', 'student-subjects.*','result.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                {{-- ================= FINANCE ================= --}}
                @if($showFinanceGroup)
                <li class="nav-section-modern"><span class="nav-section-text">{{ __('Finance') }}</span></li>
                @endif

                <!-- Fees -->
                @if($currentUser?->hasPermission('view_fees'))
                <li class="nav-item-modern">
                    <a href="{{ route('fees.hub') }}"
                        class="nav-link-modern {{ request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'fees.*', 'payments.*', 'fees.payment-report', 'fees.payment-report.pdf') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-coins"></i></span>
                        <span class="nav-text">{{ __('Fees') }}</span>
                        @if(request()->routeIs('fees.hub', 'fee-categories.*', 'fee-sets.*', 'scholarships.*', 'free-studentships.*', 'transports.*', 'fees.*', 'payments.*', 'fees.payment-report', 'fees.payment-report.pdf'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Income & Expense -->
                @if($currentUser?->hasPermission('view_financials'))
                <li class="nav-item-modern">
                    <a href="{{ route('financials.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-text">{{ __('Income & Expense') }}</span>
                        @if(request()->routeIs('financials.hub', 'incomes.*', 'expenses.*', 'income-categories.*', 'expense-categories.*', 'shareholder-transactions.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Accounts -->
                @if($currentUser?->hasPermission('view_accounts'))
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

                <!-- Budget (disabled) -->
                {{-- @if($currentUser?->hasPermission('view_budget'))
                <li class="nav-item-modern">
                    <a href="{{ route('budget.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('budget.hub', 'budget-allocations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-wallet"></i></span>
                        <span class="nav-text">{{ __('Budget') }}</span>
                        @if(request()->routeIs('budget.hub', 'budget-allocations.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif --}}

                {{-- ================= OPERATIONS ================= --}}
                @if($showOperationsGroup)
                <li class="nav-section-modern"><span class="nav-section-text">{{ __('Operations') }}</span></li>
                @endif

                <!-- HR & Payroll -->
                @if($currentUser?->hasPermission('view_hr'))
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

                <!-- Inventory -->
                @if($currentUser?->hasPermission('view_inventory'))
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

                <!-- Assets & Facilities -->
                @if($currentUser?->hasPermission('view_assets'))
                <li class="nav-item-modern">
                    <a href="{{ route('assets.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*', 'facilities.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-building"></i></span>
                        <span class="nav-text">{{ __('Assets & Facilities') }}</span>
                        @if(request()->routeIs('assets.hub', 'assets.*', 'asset-categories.*', 'asset-purchases.*', 'asset-issues.*', 'facilities.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Communications -->
                @if($currentUser?->hasPermission('view_communications'))
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
                {{-- @if($currentUser?->hasPermission('view_website_management'))
                <li class="nav-item-modern">
                    <a href="{{ route('website.cms.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('website.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-globe"></i></span>
                        <span class="nav-text">{{ __('Website Management') }}</span>
                        @if(request()->routeIs('website.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif --}}

                {{-- ================= ADMINISTRATION ================= --}}
                @if($showAdministrationGroup)
                <li class="nav-section-modern"><span class="nav-section-text">{{ __('Administration') }}</span></li>
                @endif

                <!-- Users & Roles -->
                @if($currentUser?->hasPermission('view_users'))
                <li class="nav-item-modern">
                    <a href="{{ route('users.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                        <span class="nav-text">{{ __('Users & Roles') }}</span>
                        @if(request()->routeIs('users.hub', 'users.*', 'roles.*', 'permissions.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Settings -->
                @if($currentUser?->hasPermission('view_institute_settings'))
                <li class="nav-item-modern">
                    <a href="{{ route('institute.hub') }}"
                       class="nav-link-modern {{ request()->routeIs('institute.hub', 'school-settings.*', 'certificates.*', 'id-card-templates.*', 'buildings.*', 'rooms.*', 'location.*', 'division.*', 'district.*', 'police-station.*', 'post-office.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                        <span class="nav-text">{{ __('Settings') }}</span>
                        @if(request()->routeIs('institute.hub', 'school-settings.*', 'certificates.*', 'id-card-templates.*', 'buildings.*', 'rooms.*', 'location.*', 'division.*', 'district.*', 'police-station.*', 'post-office.*'))
                        <span class="nav-indicator"></span>
                        @endif
                    </a>
                </li>
                @endif

                {{-- ================= PLATFORM (platform admins only) ================= --}}
                @if($isPlatformAdmin)
                @php $platformAdminsActive = request()->routeIs('platform-admins.*') && ! request()->routeIs('platform-admins.mobile-app.*'); @endphp
                <li class="nav-section-modern"><span class="nav-section-text">{{ __('Platform') }}</span></li>

                <!-- Platform Admins -->
                <li class="nav-item-modern">
                    <a href="{{ route('platform-admins.index') }}"
                       class="nav-link-modern {{ $platformAdminsActive ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                        <span class="nav-text">{{ __('Platform Admins') }}</span>
                        @if($platformAdminsActive)<span class="nav-indicator"></span>@endif
                    </a>
                </li>

                <!-- Mobile App Settings -->
                <li class="nav-item-modern">
                    <a href="{{ route('platform-admins.mobile-app.edit') }}"
                       class="nav-link-modern {{ request()->routeIs('platform-admins.mobile-app.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-mobile-alt"></i></span>
                        <span class="nav-text">{{ __('Mobile App Settings') }}</span>
                        @if(request()->routeIs('platform-admins.mobile-app.*'))<span class="nav-indicator"></span>@endif
                    </a>
                </li>

                <!-- Audit Trail -->
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

            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
