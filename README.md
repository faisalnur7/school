# School Management System

A comprehensive Laravel-based school management software designed to handle all aspects of educational institution administration.

## Technology Stack

- **Framework**: Laravel 11.31 (PHP 8.2+)
- **Frontend**: Tailwind CSS, Blade Templates, JavaScript
- **Database**: MySQL (via Laravel Eloquent ORM)
- **Authentication**: Laravel Breeze + Custom Role-Based Access Control
- **PDF Generation**: mPDF v8.3
- **API**: Laravel Sanctum for mobile app integration
- **Development**: Laravel Sail (Docker)

---

## Project Overview

This is a full-featured school management system built with Laravel that covers:

- ✅ Academic Management
- ✅ Student Information System
- ✅ Fee & Finance Management
- ✅ Human Resources & Payroll
- ✅ Asset Management
- ✅ Inventory System
- ✅ Library Management
- ✅ Examination & Results
- ✅ Attendance Tracking
- ✅ Website CMS (Public Facing)
- ✅ Mobile App Support

---

## Application Structure

### Core Directories

```
app/
├── Enums/              # Enum classes for fixed values
├── Helpers/            # Global helper functions
├── Http/
│   ├── Controllers/    # Business logic controllers
│   ├── Middleware/    # Custom middleware
│   └── Requests/      # Form request validation
├── Mail/               # Email classes
├── Models/             # Eloquent models
├── Providers/          # Service providers
├── Services/           # Business services
├── Traits/             # Reusable traits
└── View/              # View composers
```

### Models Count: 150+

The system includes 150+ Eloquent models covering:

- **Academic**: AcademicSession, SchoolClass, Section, Group, Subject, Course, Lesson, LessonPlan, Topic
- **Students**: Student, StudentAcademicInformation, StudentAttendance, StudentProgress, StudentSubject
- **Staff**: Teacher, Employee, StaffAttendance
- **Finance**: Account, AccountGroup, AccountTransaction, Payment, Income, Expense, JournalEntry
- **Fees**: Fee, FeeCategory, FeeSet, FeeSetItem, Scholarship, FreeStudentship
- **HR/Payroll**: Payroll, SalaryStructure, Designation, Department, LeaveBalance, LeaveRequest, HrTransaction
- **Exams**: Exam, ExamSubject, ExamMark, Result, SeatPlan, AdmitCard
- **Attendance**: Attendance, AttendanceItem, AttendanceSetting
- **Library**: Book, LibraryIssue
- **Assets**: Asset, AssetCategory, AssetPurchase, AssetIssue
- **Inventory**: InventoryItem, InventoryCategory, PurchaseOrder, InventorySale
- **Transport**: Transport, Route
- **Accommodation**: Dormitory, DormitoryRoom
- **Website CMS**: WebsitePage, WebsiteSection, WebsiteSetting, AcademicCalendar
- **And many more...

### Controllers: 180+

Organized into several category groups:

- **Academics Hub**: AcademicsHubController, AttendanceHubController, StudentsHubController
- **Finance Hub**: FeesHubController, FinancialsHubController, AccountsHubController
- **HR Hub**: HrHubController
- **Reports**: ReportController, StudentDueReportController, FeeDueReportController
- **Admin Controllers**: Located in `app/Http/Controllers/Admin/` and `app/Http/Controllers/HR/`
- **Website**: PublicFacing controller for public pages

### Services

Specialized business logic services in `app/Services/`:

- GradingService - Grade calculations
- JournalService - Accounting journal entries
- PayrollService - Salary calculations
- StudentPaymentLedgerService - Student fee ledger
- And others for specialized operations

---

## Database Schema

### Migration Files: 100+

Located in `database/migrations/`. Key migration categories:

1. **Core Tables**: users, cache, jobs, personal_access_tokens
2. **Geographic**: divisions, districts, police_stations, post_offices
3. **Academic Core**: school_classes, sections, groups, subjects, academic_sessions
4. **Student Management**: students, student_attendances, student_progress, student_subjects
5. **Fee Management**: fees, fee_categories, fee_sets, payments, scholarships
6. **Finance**: accounts, incomes, expenses, transactions, bank_accounts, hand_cash
7. **Accounting**: journal_entries, accounting_periods
8. **HR & Payroll**: employees, designations, departments, payrolls, leave_balances
9. **Examinations**: exams, exam_subjects, exam_marks, seat_plans
10. **Assets & Inventory**: assets, asset_categories, inventory_items, purchase_orders
11. **Website CMS**: website_pages, website_sections, website_settings, academic_calendars

---

## Route Organization (routes/web.php)

The application defines 1000+ routes organized by modules:

### Authentication Routes
- Login, Registration, Password Reset (via auth.php)

### Hub Pages (Dashboard Aggregators)
- `/academics` - Academics overview
- `/attendance/hub` - Attendance dashboard  
- `/students/hub` - Student management hub
- `/fees/hub` - Fee management hub
- `/financials/hub` - Finance overview
- `/hr/hub` - Human Resources hub
- `/accounts/hub` - Chart of Accounts hub

### Major Modules

| Module | Prefix | Key Features |
|--------|--------|---------------|
| Admissions | `/admissions` | Applications, documents, interviews |
| Academics | `/classes`, `/sections`, `/subjects` | Class/Section/Subject management |
| Students | `/students` | CRUD, promotion, checkout, certificates |
| Attendance | `/teacher/attendance` | Daily attendance, monthly reports |
| Fees | `/fees`, `/fee-categories`, `/fee-sets` | Fee setup, collection, reporting |
| Finance | `/incomes`, `/expenses`, `/accounts` | Income/Expense tracking, bank accounts |
| Exams | `/exams` | Exam management, marks entry, seat planning |
| Results | `/result/*` | Progress reports, yearly finals, tutorials |
| HR | `/hr/*` | Employees, payroll, leaves, departments |
| Library | `/library` | Book management, issues |
| Transport | `/transports` | Routes, student assignments |
| Assets | `/manage-assets`, `/asset-categories` | Asset tracking, issues |
| Inventory | `/inventory/*` | Products, suppliers, purchases |
| Website | `/website-management` | CMS page/section management |

### Permission-Based Access Control

Routes are protected using middleware:
- `auth` - Authentication required
- `permission:X` - Specific permission check

Available permissions include: `view_dashboard`, `view_academics`, `view_students`, `manage_classes`, `manage_fee_categories`, `view_financials`, `manage_exams`, `view_hr`, and 100+ more.

### Public Website Routes

Public-facing pages (no auth required):
- `/` - Home page
- `/about` - About us
- `/teachers` - Teacher listings
- `/staff` - Staff listings  
- `/notices` - Public notices
- `/events` - Events
- `/academic-calendar` - Academic calendar
- `/contact` - Contact form
- `/result` - Result checking

---

## Configuration Files

```
config/
├── app.php         # Application settings
├── auth.php        # Authentication guards
├── cache.php       # Cache drivers
├── database.php    # Database connections
├── filesystems.php # File storage
├── logging.php    # Log channels
├── mail.php       # Mail drivers
├── queue.php      # Queue drivers
├── sanctum.php    # API token config
├── session.php    # Session settings
└── services.php   # Third-party services
```

---

## Resource Views

Located in `resources/views/`:

- **Layouts**: Base layouts with navigation
- **Components**: Reusable Blade components
- **Pages**: Module-specific views
- **Website**: Public website templates
- **Emails**: Email blade templates
- **Vendor**: Third-party package views

Typical frontend stack:
- Tailwind CSS for styling
- Alpine.js for interactivity
- Blade templates for server-side rendering

---

## Key Features Detail

### 1. Student Lifecycle Management
Complete student lifecycle handling:
- Admission form submission
- Student promotion to next class
- Data correction requests
- Checkout/withdrawal
- Transfer certificate generation
- Student history tracking

### 2. Fee Management
Comprehensive fee handling:
- Multiple fee categories (tuition, exam, library, transport, etc.)
- Monthly/com一次性 fee sets
- Discount and scholarship management
- Free studentship programs
- Payment collection and receipt generation
- Due reports, receivable reports, payment reports

### 3. Financial Accounting
Double-entry accounting system:
- Chart of accounts with groups
- Journal entry management
- Accounting periods
- Trial balance, cash book, day book
- Income & expenditure reports
- Balance sheet

### 4. HR & Payroll
Complete HR management:
- Employee records with documents
- Designations and departments
- Salary structures
- Monthly payroll generation
- Leave balance and request management
- Staff attendance tracking

### 5. Examination System
Full exam management:
- Exam scheduling with section pairing
- Subject assignment
- Marks entry with grade/absent handling
- Seat plan generation
- Admit card generation
- Terminal results

### 6. Attendance System
Daily attendance tracking:
- Per-teacher section assignments
- Daily attendance marking
- Monthly attendance reports
- Weekend and holiday settings
- Absent email notifications

### 7. Asset Management
Fixed asset tracking:
- Asset categories and items
- Purchase orders
- Issue to departments/staff
- Return tracking
- Depreciation calculation

### 8. Inventory System
Product inventory management:
- Category and product management
- Supplier management
- Purchase orders
- Stock movement tracking
- Low stock alerts

### 9. Website CMS
Manage public website:
- Custom pages creation
- Page sections with content
- Website settings (logo, colors, contact info)
- Academic calendar management

---

## Custom Helpers & Traits

### Helpers (app/Helpers/helpers.php)
Global helper functions are autoloaded via composer.json to provide utility functions throughout the application.

### Traits (app/Traits/)
- HasTransactions - Trait for financial transaction handling

### Enums (app/Enums/)
- ExamStatus - Exam status values
- ExamType - Exam type values  
- RoomType - Room type values

---

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM (for frontend)

### Installation

1. Clone the repository
```bash
git clone <repository-url>
```

2. Install PHP dependencies
```bash
composer install
```

3. Install frontend dependencies
```bash
npm install
```

4. Copy environment file
```bash
cp .env.example .env
```

5. Generate application key
```bash
php artisan key:generate
```

6. Run migrations
```bash
php artisan migrate
```

### Development Server

Using Laravel Sail (Docker):
```bash
./vendor/bin/sail up -d
```

Or traditional PHP server:
```bash
php artisan serve
```

### Build Frontend
```bash
npm run dev
```

---

## API Documentation

The application uses Laravel Sanctum for API token authentication. API routes are defined in `routes/api.php`.

### Authentication Flow
1. User registers/login via web or API
2. Receive Sanctum token
3. Use token in Authorization header: `Bearer <token>`

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Credits

Built with Laravel Framework - https://laravel.com

---

## Documentation Status

This README was generated through code analysis of the project structure and may not reflect runtime behavior. For accurate understanding, refer to actual code implementation.

*Last analyzed: May 2026*