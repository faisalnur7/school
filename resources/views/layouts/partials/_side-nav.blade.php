<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 customer_side_nav">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">School Management System</span>
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
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
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
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Academics -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Academics<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('classes.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Classes</a></li>
                        <li><a href="{{ route('sections.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Sections</a></li>
                        <li><a href="{{ route('groups.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Groups</a></li>
                        <li><a href="{{ route('sessions.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Sessions</a></li>
                        <li><a href="{{ route('subjects.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Subjects</a></li>
                        <li><a href="{{ route('routines.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Class Routine</a></li>
                        <li><a href="{{ route('classrooms.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Class Room</a></li>
                    </ul>
                </li>

                <!-- Online Learning & LMS -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Lesson Plan -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Students -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Students<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('students.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Student List</a></li>
                        <li><a href="{{ route('students.create') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Add Student</a></li>
                        <li><a href="{{ route('attendance.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Attendance</a></li>
                        <li><a href="{{ route('students.progress') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Progress Tracking</a></li>
                        <li><a href="{{ route('reports.student') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Reports</a></li>
                    </ul>
                </li>

                <!-- Student Services -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Fees & Accounts -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Fees & Accounts<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('fees.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Fees</a></li>
                        <li><a href="{{ route('payments.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Payments</a></li>
                        <li><a href="{{ route('accounts.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Accounts</a></li>
                        <li><a href="{{ route('scholarships.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Scholarships</a></li>
                        <li><a href="{{ route('financial_aid.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Financial Aid</a></li>
                    </ul>
                </li>

                <!-- Procurement & Budget -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Exams -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Exams<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('exams.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Exam List</a></li>
                        <li><a href="{{ route('marks.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Marks</a></li>
                        <li><a href="{{ route('onlineexams.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Online Exam</a></li>
                    </ul>
                </li>

                <!-- Result Management -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>Result Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('results.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>All Results</a></li>
                        <li><a href="{{ route('results.create') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Add Result</a></li>
                        <li><a href="{{ route('results.reports') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Result Reports</a></li>
                    </ul>
                </li>

                <!-- Assessment & Analytics -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Library -->
                <li class="nav-item">
                    <a href="{{ route('library.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-book-reader"></i>
                        <p>Library</p>
                    </a>
                </li>

                <!-- Transport -->
                <li class="nav-item">
                    <a href="{{ route('transport.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-bus"></i>
                        <p>Transport</p>
                    </a>
                </li>

                <!-- Dormitory -->
                <li class="nav-item">
                    <a href="{{ route('dormitory.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-bed"></i>
                        <p>Dormitory</p>
                    </a>
                </li>

                <!-- Asset Management -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Visitor Management -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Reports -->
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Reports</p>
                    </a>
                </li>

                <!-- Teachers -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Teachers<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('teachers.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Teacher List</a></li>
                        <li><a href="{{ route('teachers.create') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Add Teacher</a></li>
                    </ul>
                </li>

                <!-- HR & Payroll -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>HR & Payroll<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('staff.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Staff Directory</a></li>
                        <li><a href="{{ route('attendance.staff') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Staff Attendance</a></li>
                        <li><a href="{{ route('payroll.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Payroll</a></li>
                        <li><a href="{{ route('payroll.reports') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Payroll Reports</a></li>
                    </ul>
                </li>

                <!-- Communication -->
                <li class="nav-item has-treeview">
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
                </li>

                <!-- Parent Portal -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Parent Portal<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('parents.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Parents List</a></li>
                        <li><a href="{{ route('communication.parent') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Communication</a></li>
                    </ul>
                </li>

                <!-- Alumni -->
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
                </li>

                <!-- ID Cards & Certificates -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-id-card"></i>
                        <p>ID Cards & Certificates<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('idcards.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>ID Cards</a></li>
                        <li><a href="{{ route('certificates.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Certificates</a></li>
                    </ul>
                </li>

                <!-- Timetable Generator -->
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
                <li class="nav-item has-treeview">
                    <a href="{{ route('settings.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>System Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li><a href="{{ route('general.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>General</a></li>
                        <li><a href="{{ route('email.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Email</a></li>
                        <li><a href="{{ route('payment.settings') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Payments</a></li>
                        <li><a href="{{ route('backup.index') }}" class="nav-link"><i class="far fa-circle nav-icon"></i>Backup</a></li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>