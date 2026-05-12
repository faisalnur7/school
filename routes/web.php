<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CommonController,
    FreeStudentshipController,
    FeeCategoryController,
    FeeSetController,
    FeeCollectionController,
    IncomeCategoryController,
    ExpenseCategoryController,
    IncomeController,
    ExpenseController,
    BankAccountController,
    MobileBankingAccountController,
    HandCashController,
    DashboardController,
    SchoolClassController,
    SectionController,
    GroupController,
    AcademicSessionController,
    SubjectController,
    RoutineController,
    ClassroomController,
    LessonController,
    TopicController,
    LessonPlanController,
    StudentController,
    AttendanceController,
    TeacherSectionAssignmentController,
    ReportController,
    FeeController,
    PaymentController,
    AccountController,
    ExamController,
    MarkController,
    OnlineExamController,
    LibraryController,
    TransportController,
    DormitoryController,
    ChatController,
    NoticeController,
    EmailSmsController,
    UserController,
    RoleController,
    PermissionController,
    PermissionCategoryController,
    SettingController,
    TeacherController,
    StaffController,
    PayrollController,
    ResultController,
    AdmissionController,
    LMSController,
    HealthController,
    DisciplineController,
    ActivityController,
    SportsController,
    ClubController,
    CounselingController,
    ScholarshipController,
    FinancialAidController,
    ProcurementController,
    AnalyticsController,
    AssetController,
    AssetCategoryController,
    AssetPurchaseController,
    VisitorController,
    MobileController,
    SecurityController,
    PrivacyController,
    ParentController,
    AlumniController,
    InventoryController,
    InventoryItemController,
    InventoryCategoryController,
    SupplierController,
    StockController,
    PurchaseOrderController,
    EventController,
    IdCardController,
    CertificateController,
    TimetableController,
    MessageController,
    DivisionController,
    DistrictController,
    PoliceStationController,
    PostOfficeController,
    ProfileController,
    StudentDueReportController,
    BuildingController,
    RoomController,
    AcademicsHubController,
    AttendanceHubController,
    StudentsHubController,
    FeesHubController,
    FinancialsHubController,
    ResultsHubController,
    HrHubController,
    AccountsHubController,
    AssetsHubController,
    InstituteHubController,
    LocationHubController,
    ShareholdersHubController,
    BudgetHubController,
    UsersHubController
};

Route::get('/reboot', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'rebooted & caches cleared!';
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('homepage');
    // ------------------- Dashboard -------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------- Admissions -------------------
    Route::prefix('admissions')->group(function () {
        Route::get('/applications', [AdmissionController::class, 'applications'])->name('admissions.applications');
        Route::get('/applications/create', [AdmissionController::class, 'createApplication'])->name('admissions.applications.create');
        Route::post('/applications/store', [AdmissionController::class, 'storeApplication'])->name('admissions.applications.store');
        Route::get('/process', [AdmissionController::class, 'processTracking'])->name('admissions.process');
        Route::get('/documents', [AdmissionController::class, 'documentVerification'])->name('admissions.documents');
        Route::get('/interviews', [AdmissionController::class, 'interviewScheduling'])->name('admissions.interviews');
        Route::get('/portal', [AdmissionController::class, 'onlinePortal'])->name('admissions.portal');
    });

    // ------------------- Hub Pages -------------------
    Route::get('/academics',         [AcademicsHubController::class,  'index'])->name('academics.hub');
    Route::get('/attendance/hub',    [AttendanceHubController::class, 'index'])->name('attendance.hub');
    Route::get('/students/hub',      [StudentsHubController::class,   'index'])->name('students.hub');
    Route::get('/fees/hub',          [FeesHubController::class,       'index'])->name('fees.hub');
    Route::get('/financials/hub',    [FinancialsHubController::class, 'index'])->name('financials.hub');
    Route::get('/results/hub',       [ResultsHubController::class,    'index'])->name('results.hub');
    Route::get('/hr/hub',            [HrHubController::class,         'index'])->name('hr.hub');
    Route::get('/accounts/hub',      [AccountsHubController::class,   'index'])->name('accounts.hub');
    Route::get('/assets/hub',        [AssetsHubController::class,     'index'])->name('assets.hub');
    Route::get('/institute/hub',     [InstituteHubController::class,  'index'])->name('institute.hub');
    Route::get('/location/hub',      [LocationHubController::class,   'index'])->name('location.hub');
    Route::get('/shareholders/hub',  [ShareholdersHubController::class, 'index'])->name('shareholders.hub');
    Route::get('/budget/hub',        [BudgetHubController::class,       'index'])->name('budget.hub');
    Route::get('/users/hub',         [UsersHubController::class,        'index'])->name('users.hub');
    Route::get('/inventory/hub',     [InventoryController::class,       'hub'])->name('inventory.hub');

    // ------------------- Academics -------------------
    Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [SchoolClassController::class, 'create'])->name('classes.create');
    Route::post('/classes/store', [SchoolClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}/edit', [SchoolClassController::class, 'edit'])->name('classes.edit');
    Route::post('/classes/{id}/update', [SchoolClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}/delete', [SchoolClassController::class, 'destroy'])->name('classes.delete');
    Route::post('/classes/{id}/toggle_status', [SchoolClassController::class, 'toggleStatus'])->name('classes.toggle-status');


    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/sections/store', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/sections/{id}/edit', [SectionController::class, 'edit'])->name('sections.edit');
    Route::post('/sections/{id}/update', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}/delete', [SectionController::class, 'destroy'])->name('sections.delete');
    Route::post('/sections/{id}/toggle_status', [SectionController::class, 'toggleStatus'])->name('sections.toggle-status');


    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups/store', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::post('/groups/{id}/update', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{id}/delete', [GroupController::class, 'destroy'])->name('groups.delete');
    Route::post('/groups/{id}/toggle_status', [GroupController::class, 'toggleStatus'])->name('groups.toggle-status');


    Route::get('/sessions', [AcademicSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [AcademicSessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions/store', [AcademicSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{id}/edit', [AcademicSessionController::class, 'edit'])->name('sessions.edit');
    Route::post('/sessions/{id}/update', [AcademicSessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{id}/delete', [AcademicSessionController::class, 'destroy'])->name('sessions.delete');
    Route::post('/sessions/{id}/toggle_status', [AcademicSessionController::class, 'toggleStatus'])->name('sessions.toggle-status');

    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects/store', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.delete');
    Route::post('/subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])->name('subjects.toggle-status');
    Route::delete('/subjects/assignment/{id}', [SubjectController::class, 'removeAssignment'])->name('subjects.removeAssignment');

    // Subject Assignment routes
    Route::post('/subjects/assign-to-class', [SubjectController::class, 'assignToClass'])->name('subjects.assign');
    Route::get('/subjects/by-class', [SubjectController::class, 'getSubjectsByClass'])->name('subjects.by-class');

    Route::get('/routines', [RoutineController::class, 'index'])->name('routines.index');
    Route::get('/routines/create', [RoutineController::class, 'create'])->name('routines.create');
    Route::post('/routines/store', [RoutineController::class, 'store'])->name('routines.store');
    Route::get('/routines/{id}/edit', [RoutineController::class, 'edit'])->name('routines.edit');
    Route::post('/routines/{id}/update', [RoutineController::class, 'update'])->name('routines.update');
    Route::delete('/routines/{id}/delete', [RoutineController::class, 'destroy'])->name('routines.delete');

    Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('/classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
    Route::post('/classrooms/store', [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::get('/classrooms/{id}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
    Route::post('/classrooms/{id}/update', [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::delete('/classrooms/{id}/delete', [ClassroomController::class, 'destroy'])->name('classrooms.delete');

    // ------------------- Online Learning & LMS -------------------
    Route::prefix('lms')->group(function () {
        Route::get('/courses', [LMSController::class, 'courses'])->name('lms.courses');
        Route::get('/courses/create', [LMSController::class, 'createCourse'])->name('lms.courses.create');
        Route::post('/courses/store', [LMSController::class, 'storeCourse'])->name('lms.courses.store');
        Route::get('/assignments', [LMSController::class, 'assignments'])->name('lms.assignments');
        Route::get('/assignments/create', [LMSController::class, 'createAssignment'])->name('lms.assignments.create');
        Route::post('/assignments/store', [LMSController::class, 'storeAssignment'])->name('lms.assignments.store');
        Route::get('/digital-classroom', [LMSController::class, 'digitalClassroom'])->name('lms.digital_classroom');
        Route::get('/video-conference', [LMSController::class, 'videoConference'])->name('lms.video_conference');
        Route::get('/content-management', [LMSController::class, 'contentManagement'])->name('lms.content_management');
    });

    // ------------------- Lesson Plan -------------------
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons/store', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{id}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::post('/lessons/{id}/update', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{id}/delete', [LessonController::class, 'destroy'])->name('lessons.delete');

    Route::prefix('lessonplan')->group(function () {
        Route::get('/', [LessonPlanController::class, 'index'])->name('lessonplan.index');
        Route::get('/create', [LessonPlanController::class, 'create'])->name('lessonplan.create');
        Route::post('/store', [LessonPlanController::class, 'store'])->name('lessonplan.store');
        Route::get('/{id}/edit', [LessonPlanController::class, 'edit'])->name('lessonplan.edit');
        Route::post('/{id}/update', [LessonPlanController::class, 'update'])->name('lessonplan.update');
        Route::delete('/{id}', [LessonPlanController::class, 'destroy'])->name('lessonplan.destroy');
        Route::get('/overview', [LessonPlanController::class, 'overview'])->name('lessonplan.overview');
        Route::get('/{id}/view', [LessonPlanController::class, 'view'])->name('lessonplan.view');
    });

    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/topics/store', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/topics/{id}/edit', [TopicController::class, 'edit'])->name('topics.edit');
    Route::post('/topics/{id}/update', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{id}/delete', [TopicController::class, 'destroy'])->name('topics.delete');

    Route::get('/lessonplans', [LessonPlanController::class, 'index'])->name('lessonplans.index');
    Route::get('/lessonplans/create', [LessonPlanController::class, 'create'])->name('lessonplans.create');
    Route::post('/lessonplans/store', [LessonPlanController::class, 'store'])->name('lessonplans.store');
    Route::get('/lessonplans/{id}/edit', [LessonPlanController::class, 'edit'])->name('lessonplans.edit');
    Route::post('/lessonplans/{id}/update', [LessonPlanController::class, 'update'])->name('lessonplans.update');
    Route::delete('/lessonplans/{id}/delete', [LessonPlanController::class, 'destroy'])->name('lessonplans.delete');

    // ------------------- Students -------------------
    Route::get('/students/birthdays', [\App\Http\Controllers\StudentBirthdayController::class, 'index'])->name('students.birthdays');
    Route::get('/students/id-cards', [\App\Http\Controllers\GenerateIdCardController::class, 'index'])->name('students.id-cards');
    Route::get('/students/id-cards/pdf', [\App\Http\Controllers\GenerateIdCardController::class, 'pdf'])->name('students.id-cards.pdf');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/pdf', [StudentController::class, 'pdf'])->name('students.pdf');
    Route::get('/students/pdf/list', [StudentController::class, 'listPdf'])->name('students.list-pdf');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::post('/students/{id}/update', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}/delete', [StudentController::class, 'destroy'])->name('students.delete');
    Route::get('/students/progress', [StudentController::class, 'progressTracking'])->name('students.progress');
    Route::get('/students/progress', [StudentController::class, 'progressTracking'])->name('students.progress');
    Route::get('/students/destroy/{id}', [StudentController::class, 'progressTracking'])->name('students.destroy');
    Route::post('students/{id}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');

    Route::prefix('teacher')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/load', [AttendanceController::class, 'load'])->name('attendance.load');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::match(['put', 'patch'], '/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/report/monthly', [\App\Http\Controllers\MonthlyAttendanceReportController::class, 'index'])->name('attendance.report.monthly');
    Route::get('/attendance/report/monthly/load', [\App\Http\Controllers\MonthlyAttendanceReportController::class, 'load'])->name('attendance.report.monthly.load');
    Route::get('/attendance/report/monthly/pdf', [\App\Http\Controllers\MonthlyAttendanceReportController::class, 'pdf'])->name('attendance.report.monthly.pdf');
    });

    // Attendance Settings (admin only)
    Route::prefix('attendance/settings')->name('attendance.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AttendanceSettingsController::class, 'index'])->name('index');
        Route::post('/weekends', [\App\Http\Controllers\AttendanceSettingsController::class, 'saveWeekends'])->name('weekends');
        Route::post('/holidays', [\App\Http\Controllers\AttendanceSettingsController::class, 'storeHoliday'])->name('holidays.store');
        Route::delete('/holidays/{holiday}', [\App\Http\Controllers\AttendanceSettingsController::class, 'destroyHoliday'])->name('holidays.destroy');
    });

    Route::get('/teacher-section-assignments', [TeacherSectionAssignmentController::class, 'index'])->name('teacher-section-assignments.index');
    Route::post('/teacher-section-assignments', [TeacherSectionAssignmentController::class, 'store'])->name('teacher-section-assignments.store');
    Route::delete('/teacher-section-assignments/{teacherSectionAssignment}', [TeacherSectionAssignmentController::class, 'destroy'])->name('teacher-section-assignments.destroy');

    // ------------------- Student Services -------------------
    Route::prefix('health')->group(function () {
        Route::get('/', [HealthController::class, 'index'])->name('health.index');
        Route::get('/create', [HealthController::class, 'create'])->name('health.create');
        Route::post('/store', [HealthController::class, 'store'])->name('health.store');
        Route::get('/{id}/edit', [HealthController::class, 'edit'])->name('health.edit');
        Route::post('/{id}/update', [HealthController::class, 'update'])->name('health.update');
        Route::delete('/{id}', [HealthController::class, 'destroy'])->name('health.destroy');
    });

    Route::prefix('discipline')->group(function () {
        Route::get('/', [DisciplineController::class, 'index'])->name('discipline.index');
        Route::get('/create', [DisciplineController::class, 'create'])->name('discipline.create');
        Route::post('/store', [DisciplineController::class, 'store'])->name('discipline.store');
        Route::get('/incidents', [DisciplineController::class, 'incidents'])->name('discipline.incidents');
        Route::get('/reports', [DisciplineController::class, 'reports'])->name('discipline.reports');
    });

    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/create', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/store', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::post('/{id}/update', [ActivityController::class, 'update'])->name('activities.update');
    });

    Route::prefix('sports')->group(function () {
        Route::get('/', [SportsController::class, 'index'])->name('sports.index');
        Route::get('/create', [SportsController::class, 'create'])->name('sports.create');
        Route::post('/store', [SportsController::class, 'store'])->name('sports.store');
        Route::get('/teams', [SportsController::class, 'teams'])->name('sports.teams');
        Route::get('/tournaments', [SportsController::class, 'tournaments'])->name('sports.tournaments');
    });

    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('clubs.index');
        Route::get('/create', [ClubController::class, 'create'])->name('clubs.create');
        Route::post('/store', [ClubController::class, 'store'])->name('clubs.store');
        Route::get('/memberships', [ClubController::class, 'memberships'])->name('clubs.memberships');
    });

    Route::prefix('counseling')->group(function () {
        Route::get('/', [CounselingController::class, 'index'])->name('counseling.index');
        Route::get('/sessions', [CounselingController::class, 'sessions'])->name('counseling.sessions');
        Route::get('/records', [CounselingController::class, 'records'])->name('counseling.records');
    });

    // ------------------- Reports -------------------
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/student', [ReportController::class, 'student'])->name('reports.student');

    // ------------------- Fees & Accounts -------------------

    Route::prefix('fee-categories')->group(function () {
        Route::get('/', [FeeCategoryController::class, 'index'])->name('fee-categories.index');
        Route::get('/create', [FeeCategoryController::class, 'create'])->name('fee-categories.create');
        Route::post('/', [FeeCategoryController::class, 'store'])->name('fee-categories.store');
        Route::get('{id}/edit', [FeeCategoryController::class, 'edit'])->name('fee-categories.edit');
        Route::put('{id}', [FeeCategoryController::class, 'update'])->name('fee-categories.update');
        Route::post('{id}/toggle-status', [FeeCategoryController::class, 'toggleStatus'])->name('fee-categories.toggle-status');
        Route::delete('{id}', [FeeCategoryController::class, 'destroy'])->name('fee-categories.delete');
    });

    Route::prefix('fee-sets')->group(function () {
        Route::get('/', [FeeSetController::class, 'index'])->name('fee-sets.index');
        Route::get('/create', [FeeSetController::class, 'create'])->name('fee-sets.create');
        Route::post('/', [FeeSetController::class, 'store'])->name('fee-sets.store');
        Route::get('{id}/edit', [FeeSetController::class, 'edit'])->name('fee-sets.edit');
        Route::put('{id}', [FeeSetController::class, 'update'])->name('fee-sets.update');
        Route::delete('{id}', [FeeSetController::class, 'destroy'])->name('fee-sets.destroy');
    });


    Route::prefix('fees')->group(function () {
        Route::get('/', [FeeController::class, 'index'])->name('fees.index');
        Route::get('/create', [FeeController::class, 'create'])->name('fees.create');
        Route::post('/store', [FeeController::class, 'store'])->name('fees.store');
        Route::get('/{id}/edit', [FeeController::class, 'edit'])->name('fees.edit');
        Route::post('/{id}/update', [FeeController::class, 'update'])->name('fees.update');
        Route::post('/{id}/toggle-status', [FeeController::class, 'toggleStatus'])->name('fees.toggle-status');
        Route::delete('/{id}/delete', [FeeController::class, 'destroy'])->name('fees.delete');
    });

    Route::prefix('income-categories')->group(function () {
        Route::get('/', [IncomeCategoryController::class, 'index'])->name('income-categories.index');
        Route::get('/create', [IncomeCategoryController::class, 'create'])->name('income-categories.create');
        Route::post('/store', [IncomeCategoryController::class, 'store'])->name('income-categories.store');
        Route::get('/{incomeCategory}/edit', [IncomeCategoryController::class, 'edit'])->name('income-categories.edit');
        Route::put('/{incomeCategory}/update', [IncomeCategoryController::class, 'update'])->name('income-categories.update');
        Route::delete('/{incomeCategory}/destroy', [IncomeCategoryController::class, 'destroy'])->name('income-categories.destroy');
    });

    Route::prefix('expense-categories')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
        Route::get('/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
        Route::post('/store', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
        Route::get('/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
        Route::put('/{expenseCategory}/update', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
        Route::delete('/{expenseCategory}/destroy', [ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');
    });

    // Incomes
    Route::prefix('incomes')->group(function () {
        Route::get('/',                  [IncomeController::class,  'index'])->name('incomes.index');
        Route::get('/create',            [IncomeController::class,  'create'])->name('incomes.create');
        Route::post('/store',            [IncomeController::class,  'store'])->name('incomes.store');
        Route::get('/{income}/edit',     [IncomeController::class,  'edit'])->name('incomes.edit');
        Route::put('/{income}/update',   [IncomeController::class,  'update'])->name('incomes.update');
        Route::delete('/{income}/destroy', [IncomeController::class, 'destroy'])->name('incomes.destroy');
    });

    // Expenses
    Route::prefix('expenses')->group(function () {
        Route::get('/',                    [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/create',              [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/store',              [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/{expense}/edit',      [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/{expense}/update',    [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/{expense}/destroy',[ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // Bank Accounts
    Route::prefix('bank-accounts')->group(function () {
        Route::get('/',                      [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::get('/create',                [BankAccountController::class, 'create'])->name('bank-accounts.create');
        Route::post('/store',                [BankAccountController::class, 'store'])->name('bank-accounts.store');
        Route::get('/{bankAccount}/edit',    [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
        Route::put('/{bankAccount}/update',  [BankAccountController::class, 'update'])->name('bank-accounts.update');
        Route::delete('/{bankAccount}/destroy', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    });

    // Mobile Banking Accounts
    Route::prefix('mobile-banking-accounts')->group(function () {
        Route::get('/',                             [MobileBankingAccountController::class, 'index'])->name('mobile-banking-accounts.index');
        Route::get('/create',                       [MobileBankingAccountController::class, 'create'])->name('mobile-banking-accounts.create');
        Route::post('/store',                       [MobileBankingAccountController::class, 'store'])->name('mobile-banking-accounts.store');
        Route::get('/{mobileBankingAccount}/edit',  [MobileBankingAccountController::class, 'edit'])->name('mobile-banking-accounts.edit');
        Route::put('/{mobileBankingAccount}/update',[MobileBankingAccountController::class, 'update'])->name('mobile-banking-accounts.update');
        Route::delete('/{mobileBankingAccount}/destroy', [MobileBankingAccountController::class, 'destroy'])->name('mobile-banking-accounts.destroy');
    });

    // Hand Cash
    Route::prefix('hand-cash')->group(function () {
        Route::get('/',                  [HandCashController::class, 'index'])->name('hand-cash.index');
        Route::get('/create',            [HandCashController::class, 'create'])->name('hand-cash.create');
        Route::post('/store',            [HandCashController::class, 'store'])->name('hand-cash.store');
        Route::get('/{handCash}/edit',   [HandCashController::class, 'edit'])->name('hand-cash.edit');
        Route::put('/{handCash}/update', [HandCashController::class, 'update'])->name('hand-cash.update');
        Route::delete('/{handCash}/destroy', [HandCashController::class, 'destroy'])->name('hand-cash.destroy');
    });

    // Fee collection
    Route::get('/fees/due-report', [\App\Http\Controllers\FeeDueReportController::class, 'index'])->name('fees.due-report');
    Route::get('/fees/due-report/pdf', [\App\Http\Controllers\FeeDueReportController::class, 'pdf'])->name('fees.due-report.pdf');
    Route::get('/fees/student-due-report', [\App\Http\Controllers\StudentDueReportController::class, 'index'])->name('fees.student-due-report');
    Route::get('/fees/student-due-report/pdf', [\App\Http\Controllers\StudentDueReportController::class, 'pdf'])->name('fees.student-due-report.pdf');
    Route::get('/fees/discount-list', [\App\Http\Controllers\DiscountListController::class, 'index'])->name('fees.discount-list');
    Route::get('/fees/discount-list/pdf', [\App\Http\Controllers\DiscountListController::class, 'pdf'])->name('fees.discount-list.pdf');
    Route::get('/fees/collect', [FeeCollectionController::class, 'index'])->name('fees.collect');
    Route::get('/fees/collect_payment/{student_id}', [FeeCollectionController::class, 'collect_payment'])->name('fees.collect_payment');
    Route::post('/fees/switch-student', [FeeCollectionController::class, 'switchStudent'])->name('fees.switch_student');
    Route::get('/fees/search-student', [FeeCollectionController::class, 'search'])->name('fees.search');
    Route::post('/fees/pay', [FeeCollectionController::class, 'pay'])->name('fees.pay');
    Route::get('/fees/category-pay', [FeeCollectionController::class,'categoryPay'])->name('fees.category.pay');
    Route::get('/fees/load-category-fees', [FeeCollectionController::class,'loadCategoryFees'])->name('fees.load.category');
    Route::post('/fees/category-pay', [FeeCollectionController::class,'storeCategoryPayment'])->name('fees.category.store');
    Route::get('/fees/search', [FeeCollectionController::class,'search'])->name('fees.search');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payment-items/{paymentItem}', [PaymentController::class, 'removeItem'])->name('payment-items.remove');
    
    Route::get('/accounts', [AccountController::class, 'getAccounts'])->name('accounts.index');

    Route::prefix('scholarships')->group(function () {
        Route::get('/', [ScholarshipController::class, 'index'])->name('scholarships.index');
        Route::get('/pdf', [ScholarshipController::class, 'pdf'])->name('scholarships.pdf');
        Route::get('/create', [ScholarshipController::class, 'create'])->name('scholarships.create');
        Route::get('/students', [ScholarshipController::class, 'getStudents'])->name('scholarships.students');
        Route::post('/bulk', [ScholarshipController::class, 'storeBulk'])->name('scholarships.storeBulk');
        Route::delete('/{scholarship}', [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');
    });

    Route::prefix('free-studentships')->group(function () {
        Route::get('/', [FreeStudentshipController::class, 'index'])->name('free-studentships.index');
        Route::get('/pdf', [FreeStudentshipController::class, 'pdf'])->name('free-studentships.pdf');
        Route::get('/create', [FreeStudentshipController::class, 'create'])->name('free-studentships.create');
        Route::get('/students', [FreeStudentshipController::class, 'getStudents'])->name('free-studentships.students');
        Route::post('/bulk', [FreeStudentshipController::class, 'storeBulk'])->name('free-studentships.storeBulk');
        Route::delete('/{freeStudentship}', [FreeStudentshipController::class, 'destroy'])->name('free-studentships.destroy');
    });

    Route::prefix('financial-aid')->group(function () {
        Route::get('/', [FinancialAidController::class, 'index'])->name('financial_aid.index');
        Route::get('/applications', [FinancialAidController::class, 'applications'])->name('financial_aid.applications');
        Route::get('/disbursements', [FinancialAidController::class, 'disbursements'])->name('financial_aid.disbursements');
    });

    // ------------------- Procurement & Budget -------------------
    Route::prefix('procurement')->group(function () {
        Route::get('/orders', [ProcurementController::class, 'orders'])->name('procurement.orders');
        Route::get('/orders/create', [ProcurementController::class, 'createOrder'])->name('procurement.orders.create');
        Route::post('/orders/store', [ProcurementController::class, 'storeOrder'])->name('procurement.orders.store');
        Route::get('/vendors', [ProcurementController::class, 'vendors'])->name('procurement.vendors');
        Route::get('/vendors/create', [ProcurementController::class, 'createVendor'])->name('procurement.vendors.create');
        Route::post('/vendors/store', [ProcurementController::class, 'storeVendor'])->name('procurement.vendors.store');
        Route::get('/budget', [ProcurementController::class, 'budget'])->name('procurement.budget');
        Route::get('/allocation', [ProcurementController::class, 'allocation'])->name('procurement.allocation');
    });

    // ------------------- Exams -------------------
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ExamController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ExamController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [\App\Http\Controllers\ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [\App\Http\Controllers\ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [\App\Http\Controllers\ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [\App\Http\Controllers\ExamController::class, 'destroy'])->name('destroy');
        Route::get('/{exam}/marks-entry', [\App\Http\Controllers\ExamController::class, 'marksEntry'])->name('marks-entry');
        Route::post('/{exam}/marks-entry', [\App\Http\Controllers\ExamController::class, 'saveMarks'])->name('save-marks');
        Route::get('/{exam}/preview', [\App\Http\Controllers\ExamController::class, 'preview'])->name('preview');
        Route::get('/{exam}/preview/pdf', [\App\Http\Controllers\ExamController::class, 'previewPdf'])->name('preview-pdf');
        Route::get('/{exam}/terminal-result', [\App\Http\Controllers\ExamController::class, 'terminalResult'])->name('terminal-result');
        Route::get('/{exam}/terminal-result/pdf', [\App\Http\Controllers\ExamController::class, 'terminalResultPdf'])->name('terminal-result-pdf');
    });
    Route::get('/ajax/sections-by-class', [\App\Http\Controllers\ExamController::class, 'getSectionsByClass'])->name('ajax.sections-by-class');
    Route::get('/marks', [MarkController::class, 'index'])->name('marks.index');
    Route::post('/marks/store', [MarkController::class, 'store'])->name('marks.store');
    Route::get('/onlineexams', [OnlineExamController::class, 'index'])->name('onlineexams.index');

    // ------------------- Student Subject Assignment -------------------
    Route::prefix('student-subjects')->name('student-subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StudentSubjectController::class, 'index'])->name('index');
        Route::get('/{student}/assign', [\App\Http\Controllers\StudentSubjectController::class, 'assign'])->name('assign');
        Route::post('/{student}/save', [\App\Http\Controllers\StudentSubjectController::class, 'saveAssignment'])->name('save');
        Route::post('/bulk-assign', [\App\Http\Controllers\StudentSubjectController::class, 'bulkAssign'])->name('bulk-assign');
    });

    // ------------------- Result Management -------------------
    Route::prefix('results')->group(function () {
        Route::get('/', [ResultController::class, 'index'])->name('results.index');
        Route::get('/create', [ResultController::class, 'create'])->name('results.create');
        Route::post('/store', [ResultController::class, 'store'])->name('results.store');
        Route::get('/{id}/edit', [ResultController::class, 'edit'])->name('results.edit');
        Route::post('/{id}/update', [ResultController::class, 'update'])->name('results.update');
        Route::delete('/{id}', [ResultController::class, 'destroy'])->name('results.destroy');
        Route::get('/reports', [ResultController::class, 'reports'])->name('results.reports');
    });

    // ------------------- Assessment & Analytics -------------------
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
        Route::get('/performance', [AnalyticsController::class, 'performance'])->name('analytics.performance');
        Route::get('/trends', [AnalyticsController::class, 'trends'])->name('analytics.trends');
        Route::get('/predictive', [AnalyticsController::class, 'predictive'])->name('analytics.predictive');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');
    });

    // ------------------- Library -------------------
    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');

    // ------------------- Transport -------------------
    Route::prefix('transports')->group(function () {
        Route::get('/', [TransportController::class, 'index'])->name('transports.index');
        Route::get('/create', [TransportController::class, 'create'])->name('transports.create');
        Route::get('/edit/{id}', [TransportController::class, 'edit'])->name('transports.edit');
        Route::post('/toggle-status/{transport}', [TransportController::class, 'toggleStatus'])->name('transports.toggle-status');
        Route::get('/students', [TransportController::class, 'getStudents'])->name('transports.get-students');
        Route::post('/bulk', [TransportController::class, 'storeBulk'])->name('transports.store-bulk');
        Route::delete('/{transport}', [TransportController::class, 'destroy'])->name('transports.destroy');
    });

    Route::get('/transport', [TransportController::class, 'index'])->name('transport.index');

    // ------------------- Dormitory -------------------
    Route::get('/dormitory', [DormitoryController::class, 'index'])->name('dormitory.index');

    // ------------------- Professions -------------------
    Route::resource('professions', \App\Http\Controllers\ProfessionController::class)->except(['create', 'edit', 'show']);

    // ------------------- Shareholders -------------------
    Route::resource('shareholders', \App\Http\Controllers\ShareholderController::class);
    Route::resource('shareholder-transactions', \App\Http\Controllers\ShareholderTransactionController::class);

    // ------------------- Ledger -------------------
    Route::get('/ledger', [\App\Http\Controllers\LedgerController::class, 'index'])->name('ledger.index');

    // ------------------- Accounts Module -------------------
    Route::resource('account-groups', \App\Http\Controllers\AccountGroupController::class)->except(['create','edit','show']);
    Route::resource('accounts-list',  \App\Http\Controllers\AccountsController::class)->except(['show']);

    // ------------------- Reports -------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/trial-balance',         [\App\Http\Controllers\ReportsController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/balance-sheet',           [\App\Http\Controllers\ReportsController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/cash-book',               [\App\Http\Controllers\ReportsController::class, 'cashBook'])->name('cash-book');
        Route::get('/day-book',                [\App\Http\Controllers\ReportsController::class, 'dayBook'])->name('day-book');
        Route::get('/income-expenditure',      [\App\Http\Controllers\ReportsController::class, 'incomeExpenditure'])->name('income-expenditure');
        Route::get('/cash-summary',            [\App\Http\Controllers\ReportsController::class, 'cashSummary'])->name('cash-summary');
        Route::get('/receipt-payment',         [\App\Http\Controllers\ReportsController::class, 'receiptPayment'])->name('receipt-payment');
        Route::get('/cash-flow',               [\App\Http\Controllers\ReportsController::class, 'cashFlow'])->name('cash-flow');
        Route::get('/chart-of-accounts',       [\App\Http\Controllers\ReportsController::class, 'chartOfAccounts'])->name('chart-of-accounts');
        Route::get('/headwise-transactions',   [\App\Http\Controllers\ReportsController::class, 'headwiseTransactions'])->name('headwise-transactions');
        // PDF exports
        Route::get('/trial-balance/pdf',       [\App\Http\Controllers\ReportsController::class, 'trialBalancePdf'])->name('trial-balance.pdf');
        Route::get('/balance-sheet/pdf',       [\App\Http\Controllers\ReportsController::class, 'balanceSheetPdf'])->name('balance-sheet.pdf');
        Route::get('/cash-book/pdf',           [\App\Http\Controllers\ReportsController::class, 'cashBookPdf'])->name('cash-book.pdf');
        Route::get('/day-book/pdf',            [\App\Http\Controllers\ReportsController::class, 'dayBookPdf'])->name('day-book.pdf');
        Route::get('/income-expenditure/pdf',  [\App\Http\Controllers\ReportsController::class, 'incomeExpenditurePdf'])->name('income-expenditure.pdf');
        Route::get('/cash-summary/pdf',        [\App\Http\Controllers\ReportsController::class, 'cashSummaryPdf'])->name('cash-summary.pdf');
        Route::get('/receipt-payment/pdf',     [\App\Http\Controllers\ReportsController::class, 'receiptPaymentPdf'])->name('receipt-payment.pdf');
        Route::get('/cash-flow/pdf',           [\App\Http\Controllers\ReportsController::class, 'cashFlowPdf'])->name('cash-flow.pdf');
        Route::get('/chart-of-accounts/pdf',   [\App\Http\Controllers\ReportsController::class, 'chartOfAccountsPdf'])->name('chart-of-accounts.pdf');
        Route::get('/headwise-transactions/pdf',[\App\Http\Controllers\ReportsController::class, 'headwiseTransactionsPdf'])->name('headwise-transactions.pdf');
    });

    // ------------------- Asset Tracking -------------------
    Route::resource('asset-issues', \App\Http\Controllers\AssetIssueController::class)->except(['show','edit','update']);
    Route::patch('/asset-issues/{assetIssue}/return', [\App\Http\Controllers\AssetIssueController::class, 'returnAsset'])->name('asset-issues.return');
    Route::get('/asset-stock', [\App\Http\Controllers\AssetIssueController::class, 'stock'])->name('asset-issues.stock');

    // ------------------- Budget Control -------------------
    Route::resource('budget-allocations', \App\Http\Controllers\BudgetAllocationController::class)->except(['create','edit','show']);
    Route::get('/budget-allocations/report',     [\App\Http\Controllers\BudgetAllocationController::class, 'report'])->name('budget-allocations.report');
    Route::get('/budget-allocations/report/pdf', [\App\Http\Controllers\BudgetAllocationController::class, 'reportPdf'])->name('budget-allocations.report.pdf');

    // ------------------- HR & Payroll -------------------
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\HR\DashboardController::class, 'index'])->name('dashboard');

        // Designations
        Route::resource('designations', \App\Http\Controllers\HR\DesignationController::class);
        Route::patch('designations/{designation}/toggle-status', [\App\Http\Controllers\HR\DesignationController::class, 'toggleStatus'])->name('designations.toggle-status');

        // Departments
        Route::resource('departments', \App\Http\Controllers\HR\DepartmentController::class)->except(['show']);

        // Employees
        Route::resource('employees', \App\Http\Controllers\HR\EmployeeController::class);
        Route::get('employees/{id}/payment', [\App\Http\Controllers\HR\PaymentInformationController::class, 'createOrEdit'])->name('employees.payment.edit');
        Route::post('employees/{id}/payment', [\App\Http\Controllers\HR\PaymentInformationController::class, 'store'])->name('employees.payment.store');
        Route::get('employees/{id}/documents', [\App\Http\Controllers\HR\EmployeeDocumentController::class, 'index'])->name('employees.documents');
        Route::post('employees/{id}/documents', [\App\Http\Controllers\HR\EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
        Route::delete('documents/{document}', [\App\Http\Controllers\HR\EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');

        // Salary
        Route::resource('salary-structures', \App\Http\Controllers\HR\SalaryStructureController::class)->except(['show']);
        Route::get('salary/load-defaults/{designation}', [\App\Http\Controllers\HR\SalaryStructureController::class, 'loadDefaults'])->name('salary.load-defaults');
        Route::get('salary/defaults', [\App\Http\Controllers\HR\DesignationSalaryDefaultController::class, 'index'])->name('salary.defaults.index');
        Route::post('salary/defaults', [\App\Http\Controllers\HR\DesignationSalaryDefaultController::class, 'store'])->name('salary.defaults.store');
        Route::get('salary/defaults/{id}', [\App\Http\Controllers\HR\DesignationSalaryDefaultController::class, 'show'])->name('salary.defaults.show');

        // Payroll
        Route::get('payroll', [\App\Http\Controllers\HR\PayrollController::class, 'index'])->name('payroll.index');
        Route::post('payroll/preview', [\App\Http\Controllers\HR\PayrollController::class, 'preview'])->name('payroll.preview');
        Route::post('payroll/generate', [\App\Http\Controllers\HR\PayrollController::class, 'generate'])->name('payroll.generate');
        Route::get('payroll/{id}/slip', [\App\Http\Controllers\HR\PayrollController::class, 'slip'])->name('payroll.slip');
        Route::get('payroll/{month}/{year}', [\App\Http\Controllers\HR\PayrollController::class, 'show'])
            ->whereNumber('month')
            ->whereNumber('year')
            ->name('payroll.show');
        Route::patch('payroll/{id}/paid', [\App\Http\Controllers\HR\PayrollController::class, 'markPaid'])->name('payroll.paid');
        Route::post('payroll/lock', [\App\Http\Controllers\HR\PayrollController::class, 'lock'])->name('payroll.lock');

        // Leave
        Route::get('leave', [\App\Http\Controllers\HR\LeaveController::class, 'index'])->name('leave.index');
        Route::get('leave/create', [\App\Http\Controllers\HR\LeaveController::class, 'create'])->name('leave.create');
        Route::post('leave', [\App\Http\Controllers\HR\LeaveController::class, 'store'])->name('leave.store');
        Route::patch('leave/{id}/approve', [\App\Http\Controllers\HR\LeaveController::class, 'approve'])->name('leave.approve');
        Route::patch('leave/{id}/reject', [\App\Http\Controllers\HR\LeaveController::class, 'reject'])->name('leave.reject');
        Route::get('leave/balances', [\App\Http\Controllers\HR\LeaveController::class, 'balances'])->name('leave.balances');
        Route::post('leave/balances/set', [\App\Http\Controllers\HR\LeaveController::class, 'setBalance'])->name('leave.balances.set');

        // Reports
        Route::get('reports/salary-sheet',   [\App\Http\Controllers\HR\ReportController::class, 'salarySheet'])->name('reports.salary-sheet');
        Route::get('reports/payroll-summary',[\App\Http\Controllers\HR\ReportController::class, 'payrollSummary'])->name('reports.payroll-summary');
        Route::get('reports/leave',          [\App\Http\Controllers\HR\ReportController::class, 'leaveReport'])->name('reports.leave');
        Route::get('reports/hierarchy',      [\App\Http\Controllers\HR\ReportController::class, 'hierarchyReport'])->name('reports.hierarchy');
    });

    Route::get('/school-settings', [\App\Http\Controllers\SchoolSettingController::class, 'index'])->name('school-settings.index');
    Route::put('/school-settings', [\App\Http\Controllers\SchoolSettingController::class, 'update'])->name('school-settings.update');
     Route::resource('id-card-templates', \App\Http\Controllers\IdCardTemplateController::class)->except(['show']);
     Route::resource('buildings', BuildingController::class)->except(['show']);
     Route::resource('rooms', RoomController::class)->except(['show']);

    // ------------------- Journal Entries (read-only — auto-posted by system) -------------------
    Route::get('journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journal-entries.index');
    Route::get('journal-entries/{journalEntry}', [\App\Http\Controllers\JournalEntryController::class, 'show'])->name('journal-entries.show');
    Route::delete('journal-entries/{journalEntry}', [\App\Http\Controllers\JournalEntryController::class, 'destroy'])->name('journal-entries.destroy');

    // ------------------- Accounting Periods -------------------
    Route::resource('accounting-periods', \App\Http\Controllers\AccountingPeriodController::class)->except(['create','edit','show']);
    Route::post('/accounting-periods/{accountingPeriod}/close', [\App\Http\Controllers\AccountingPeriodController::class, 'close'])->name('accounting-periods.close');



    // unified transaction routes for all transaction types
    Route::get('/transactions',     [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/pdf',  [\App\Http\Controllers\TransactionController::class, 'pdf'])->name('transactions.pdf');
    Route::get('/transactions/create', [\App\Http\Controllers\ShareholderTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [\App\Http\Controllers\ShareholderTransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{shareholderTransaction}/edit', [\App\Http\Controllers\ShareholderTransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{shareholderTransaction}', [\App\Http\Controllers\ShareholderTransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{shareholderTransaction}', [\App\Http\Controllers\ShareholderTransactionController::class, 'destroy'])->name('transactions.destroy');

    // ------------------- Asset Management -------------------
    Route::prefix('asset-categories')->group(function () {
        Route::get('/', [AssetCategoryController::class, 'index'])->name('asset-categories.index');
        Route::post('/', [AssetCategoryController::class, 'store'])->name('asset-categories.store');
        Route::put('/{assetCategory}', [AssetCategoryController::class, 'update'])->name('asset-categories.update');
        Route::delete('/{assetCategory}', [AssetCategoryController::class, 'destroy'])->name('asset-categories.destroy');
    });

    Route::prefix('manage-assets')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });

    Route::prefix('asset-purchases')->group(function () {
        Route::get('/accounts/load', [AssetPurchaseController::class, 'getAccounts'])->name('asset-purchases.accounts');
        Route::get('/', [AssetPurchaseController::class, 'index'])->name('asset-purchases.index');
        Route::get('/create', [AssetPurchaseController::class, 'create'])->name('asset-purchases.create');
        Route::post('/', [AssetPurchaseController::class, 'store'])->name('asset-purchases.store');
        Route::get('/{assetPurchase}', [AssetPurchaseController::class, 'show'])->name('asset-purchases.show');
    });

    Route::prefix('facilities')->group(function () {
        Route::get('/booking', [AssetController::class, 'facilityBooking'])->name('facilities.booking');
        Route::get('/booking/create', [AssetController::class, 'createBooking'])->name('facilities.booking.create');
        Route::post('/booking/store', [AssetController::class, 'storeBooking'])->name('facilities.booking.store');
    });

    // ------------------- Visitor Management -------------------
    Route::prefix('visitors')->group(function () {
        Route::get('/registration', [VisitorController::class, 'registration'])->name('visitors.registration');
        Route::get('/registration/create', [VisitorController::class, 'createRegistration'])->name('visitors.registration.create');
        Route::post('/registration/store', [VisitorController::class, 'storeRegistration'])->name('visitors.registration.store');
        Route::get('/tracking', [VisitorController::class, 'tracking'])->name('visitors.tracking');
        Route::get('/appointments', [VisitorController::class, 'appointments'])->name('visitors.appointments');
        Route::get('/security', [VisitorController::class, 'security'])->name('visitors.security');
    });

    // ------------------- Teachers -------------------
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/store', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::post('/{id}/update', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    // ------------------- HR & Payroll -------------------
    Route::prefix('hr')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::post('/staff/{id}/update', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');

        Route::get('/attendance', [StaffController::class, 'attendance'])->name('attendance.staff');
        Route::post('/attendance/store', [StaffController::class, 'attendanceStore'])->name('attendance.staff.store');


    });

    // ------------------- Communication -------------------
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/notice', [NoticeController::class, 'index'])->name('notice.index');
    Route::get('/emailsms', [EmailSmsController::class, 'index'])->name('emailsms.index');

    Route::prefix('communication')->group(function () {
        Route::get('/notices', [NoticeController::class, 'index'])->name('communication.notices');
        Route::get('/messages', [MessageController::class, 'index'])->name('communication.messages');
        Route::get('/emailsms', [EmailSmsController::class, 'index'])->name('communication.emailsms');
        Route::get('/events', [EventController::class, 'index'])->name('communication.events');
        Route::get('/parents', [ParentController::class, 'index'])->name('communication.parent');
        Route::get('/alumni', [AlumniController::class, 'index'])->name('communication.alumni');
        Route::get('/notifications', [MobileController::class, 'notifications'])->name('communication.notifications');
        Route::get('/social-integration', [MobileController::class, 'socialIntegration'])->name('social.integration');
    });

    // ------------------- Parent Portal -------------------
    Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
    Route::get('/parents/create', [ParentController::class, 'create'])->name('parents.create');
    Route::post('/parents/store', [ParentController::class, 'store'])->name('parents.store');

    // ------------------- Alumni -------------------
    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/create', [AlumniController::class, 'create'])->name('alumni.create');
    Route::post('/alumni/store', [AlumniController::class, 'store'])->name('alumni.store');

    // ------------------- Inventory -------------------
    Route::prefix('inventory')->group(function () {
        // Categories
        Route::get('/categories', [InventoryCategoryController::class, 'index'])->name('inventory.categories.index');
        Route::get('/categories/create', [InventoryCategoryController::class, 'create'])->name('inventory.categories.create');
        Route::post('/categories', [InventoryCategoryController::class, 'store'])->name('inventory.categories.store');
        Route::get('/categories/{id}/edit', [InventoryCategoryController::class, 'edit'])->name('inventory.categories.edit');
        Route::put('/categories/{id}', [InventoryCategoryController::class, 'update'])->name('inventory.categories.update');
        Route::delete('/categories/{id}', [InventoryCategoryController::class, 'destroy'])->name('inventory.categories.destroy');

        // Products
        Route::get('/products', [InventoryItemController::class, 'index'])->name('inventory.products.index');
        Route::get('/products/create', [InventoryItemController::class, 'create'])->name('inventory.products.create');
        Route::post('/products', [InventoryItemController::class, 'store'])->name('inventory.products.store');
        Route::get('/products/{id}/edit', [InventoryItemController::class, 'edit'])->name('inventory.products.edit');
        Route::put('/products/{id}', [InventoryItemController::class, 'update'])->name('inventory.products.update');
        Route::delete('/products/{id}', [InventoryItemController::class, 'destroy'])->name('inventory.products.destroy');

        // Suppliers
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('inventory.suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('inventory.suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('inventory.suppliers.store');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('inventory.suppliers.edit');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('inventory.suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('inventory.suppliers.destroy');

        // Purchases
        Route::get('/purchases', [PurchaseOrderController::class, 'index'])->name('inventory.purchases.index');
        Route::get('/purchases/create', [PurchaseOrderController::class, 'create'])->name('inventory.purchases.create');
        Route::post('/purchases', [PurchaseOrderController::class, 'store'])->name('inventory.purchases.store');
        Route::get('/purchases/{id}', [PurchaseOrderController::class, 'show'])->name('inventory.purchases.show');

        // Reports
        Route::get('/reports/stock', [StockController::class, 'stockReport'])->name('inventory.reports.stock');
        Route::get('/reports/low-stock', [StockController::class, 'lowStock'])->name('inventory.reports.lowStock');
    });

    // ------------------- Event Management -------------------
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/store', [EventController::class, 'store'])->name('events.store');
        Route::get('/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::post('/{id}/update', [EventController::class, 'update'])->name('events.update');
        Route::delete('/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // ------------------- ID Cards & Certificates -------------------
    Route::prefix('idcards')->group(function () {
        Route::get('/', [IdCardController::class, 'index'])->name('idcards.index');
        Route::get('/create', [IdCardController::class, 'create'])->name('idcards.create');
        Route::post('/store', [IdCardController::class, 'store'])->name('idcards.store');
        Route::get('/{id}/edit', [IdCardController::class, 'edit'])->name('idcards.edit');
        Route::post('/{id}/update', [IdCardController::class, 'update'])->name('idcards.update');
        Route::delete('/{id}', [IdCardController::class, 'destroy'])->name('idcards.destroy');
    });

    Route::prefix('certificates')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/create', [CertificateController::class, 'create'])->name('certificates.create');
        Route::post('/store', [CertificateController::class, 'store'])->name('certificates.store');
        Route::get('/{id}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
        Route::post('/{id}/update', [CertificateController::class, 'update'])->name('certificates.update');
        Route::delete('/{id}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    });

    // ------------------- Timetable Generator -------------------
    Route::prefix('timetable')->group(function () {
        Route::get('/', [TimetableController::class, 'index'])->name('timetable.index');
        Route::get('/create', [TimetableController::class, 'create'])->name('timetable.create');
        Route::post('/store', [TimetableController::class, 'store'])->name('timetable.store');
        Route::get('/{id}/edit', [TimetableController::class, 'edit'])->name('timetable.edit');
        Route::post('/{id}/update', [TimetableController::class, 'update'])->name('timetable.update');
        Route::delete('/{id}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
    });

    // ------------------- Mobile App Management -------------------
    Route::prefix('mobile')->group(function () {
        Route::get('/notifications', [MobileController::class, 'notifications'])->name('mobile.notifications');
        Route::post('/notifications/send', [MobileController::class, 'sendNotification'])->name('mobile.notifications.send');
        Route::get('/settings', [MobileController::class, 'settings'])->name('mobile.settings');
        Route::post('/settings/update', [MobileController::class, 'updateSettings'])->name('mobile.settings.update');
        Route::get('/offline', [MobileController::class, 'offlineManagement'])->name('mobile.offline');
    });

    // ------------------- Security & Compliance -------------------
    Route::prefix('security')->group(function () {
        Route::get('/access-control', [SecurityController::class, 'accessControl'])->name('security.access_control');
        Route::get('/access-control/create', [SecurityController::class, 'createAccessControl'])->name('security.access_control.create');
        Route::post('/access-control/store', [SecurityController::class, 'storeAccessControl'])->name('security.access_control.store');
        Route::get('/cctv', [SecurityController::class, 'cctvIntegration'])->name('security.cctv');
        Route::get('/emergency', [SecurityController::class, 'emergencyResponse'])->name('security.emergency');
        Route::get('/emergency/protocols', [SecurityController::class, 'emergencyProtocols'])->name('security.emergency.protocols');
        Route::get('/audit', [SecurityController::class, 'auditTrails'])->name('security.audit');
    });

    Route::prefix('privacy')->group(function () {
        Route::get('/gdpr', [PrivacyController::class, 'gdprCompliance'])->name('privacy.gdpr');
        Route::get('/data-protection', [PrivacyController::class, 'dataProtection'])->name('privacy.data_protection');
        Route::get('/privacy-settings', [PrivacyController::class, 'privacySettings'])->name('privacy.settings');
    });

    // ------------------- Users & Roles -------------------
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.delete');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{id}/update', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/{id}/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('/permission-categories', [PermissionCategoryController::class, 'index'])->name('permission-categories.index');
    Route::get('/permission-categories/create', [PermissionCategoryController::class, 'create'])->name('permission-categories.create');
    Route::post('/permission-categories/store', [PermissionCategoryController::class, 'store'])->name('permission-categories.store');
    Route::get('/permission-categories/{permissionCategory}/edit', [PermissionCategoryController::class, 'edit'])->name('permission-categories.edit');
    Route::post('/permission-categories/{permissionCategory}/update', [PermissionCategoryController::class, 'update'])->name('permission-categories.update');
    Route::delete('/permission-categories/{permissionCategory}', [PermissionCategoryController::class, 'destroy'])->name('permission-categories.destroy');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/general', [SettingController::class, 'general'])->name('general.settings');
    Route::post('/settings/general/update', [SettingController::class, 'updateGeneral'])->name('general.settings.update');
    Route::get('/settings/email', [SettingController::class, 'email'])->name('email.settings');
    Route::post('/settings/email/update', [SettingController::class, 'updateEmail'])->name('email.settings.update');
    Route::get('/settings/payment', [SettingController::class, 'payment'])->name('payment.settings');
    Route::post('/settings/payment/update', [SettingController::class, 'updatePayment'])->name('payment.settings.update');
    Route::get('/settings/backup', [SettingController::class, 'backup'])->name('backup.index');
    Route::post('/settings/backup/create', [SettingController::class, 'createBackup'])->name('backup.create');
    Route::get('/settings/backup/download/{file}', [SettingController::class, 'downloadBackup'])->name('backup.download');

    // ------------------- Admin Section -------------------
    Route::prefix('admin')->group(function () {
        Route::get('/admission-query', [DashboardController::class, 'admissionQuery'])->name('admission.query');
        Route::get('/visitor-book', [DashboardController::class, 'visitorBook'])->name('visitor.book');
        Route::get('/phone-logs', [DashboardController::class, 'phoneLogs'])->name('phone.logs');
        Route::get('/postal-receive', [DashboardController::class, 'postalReceive'])->name('postal.receive');
        Route::get('/postal-dispatch', [DashboardController::class, 'postalDispatch'])->name('postal.dispatch');
        Route::get('/complaints', [DashboardController::class, 'complainIndex'])->name('complain.index');
        Route::get('/student-certificate', [CertificateController::class, 'index'])->name('student.certificate');
        Route::get('/student-idcard', [IdCardController::class, 'index'])->name('student.idcard');
    });

    Route::prefix('division')->group(function () {
        Route::get('/list', [DivisionController::class, 'index'])->name('division.index');
        Route::get('/create', [DivisionController::class, 'create'])->name('division.create');
        Route::post('/store', [DivisionController::class, 'store'])->name('division.store');
        Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('division.edit');
        Route::post('/update/{id}', [DivisionController::class, 'update'])->name('division.update');
        Route::delete('/delete/{id}', [DivisionController::class, 'destroy'])->name('division.destroy');
    });

    Route::prefix('district')->group(function () {
        Route::get('/list', [DistrictController::class, 'index'])->name('district.index');
        Route::get('/create', [DistrictController::class, 'create'])->name('district.create');
        Route::post('/store', [DistrictController::class, 'store'])->name('district.store');
        Route::get('/edit/{id}', [DistrictController::class, 'edit'])->name('district.edit');
        Route::post('/update/{id}', [DistrictController::class, 'update'])->name('district.update');
        Route::delete('/delete/{id}', [DistrictController::class, 'destroy'])->name('district.destroy');
    });

    Route::prefix('police-station')->group(function () {
        Route::get('/list', [PoliceStationController::class, 'index'])->name('police-station.index');
        Route::get('/create', [PoliceStationController::class, 'create'])->name('police-station.create');
        Route::post('/store', [PoliceStationController::class, 'store'])->name('police-station.store');
        Route::get('/edit/{id}', [PoliceStationController::class, 'edit'])->name('police-station.edit');
        Route::post('/update/{id}', [PoliceStationController::class, 'update'])->name('police-station.update');
        Route::delete('/delete/{id}', [PoliceStationController::class, 'destroy'])->name('police-station.destroy');
    });

    Route::prefix('post-office')->group(function () {
        Route::get('/list', [PostOfficeController::class, 'index'])->name('post-office.index');
        Route::get('/create', [PostOfficeController::class, 'create'])->name('post-office.create');
        Route::post('/store', [PostOfficeController::class, 'store'])->name('post-office.store');
        Route::get('/edit/{id}', [PostOfficeController::class, 'edit'])->name('post-office.edit');
        Route::post('/update/{id}', [PostOfficeController::class, 'update'])->name('post-office.update');
        Route::delete('/delete/{id}', [PostOfficeController::class, 'destroy'])->name('post-office.destroy');
    });

    // ------------------- Homework -------------------
    Route::get('/homework', [LessonPlanController::class, 'homework'])->name('homework.index');

    // ------------------- AJAX Calls -------------------
    Route::get('load_districts', [CommonController::class,'load_districts'])->name('load_districts');
    Route::get('load_police_stations',[CommonController::class,'load_police_stations'])->name('load_police_stations');
    Route::get('load_post_offices',[CommonController::class,'load_post_offices'])->name('load_post_offices');
    Route::get('load_section_groups',[CommonController::class,'load_section_groups'])->name('load_section_groups');
    Route::get('load_groups',[CommonController::class,'load_groups'])->name('load_groups');
    Route::get('get_next_roll_cid',[CommonController::class,'getNextRollAndCid'])->name('get_next_roll_cid');

});
