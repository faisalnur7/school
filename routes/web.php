<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ClassController,
    SectionController,
    GroupController,
    SessionController,
    SubjectController,
    RoutineController,
    ClassroomController,
    LessonController,
    TopicController,
    LessonPlanController,
    StudentController,
    AttendanceController,
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
    InventoryRequestController,
    StockController,
    EventController,
    IdCardController,
    CertificateController,
    TimetableController,
    MessageController
};

Route::get('/', [DashboardController::class, 'index'])->name('homepage');

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

    // ------------------- Academics -------------------
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes/store', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    Route::post('/classes/{id}/update', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}/delete', [ClassController::class, 'destroy'])->name('classes.delete');

    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/sections/store', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/sections/{id}/edit', [SectionController::class, 'edit'])->name('sections.edit');
    Route::post('/sections/{id}/update', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}/delete', [SectionController::class, 'destroy'])->name('sections.delete');

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups/store', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::post('/groups/{id}/update', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{id}/delete', [GroupController::class, 'destroy'])->name('groups.delete');

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions/store', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{id}/edit', [SessionController::class, 'edit'])->name('sessions.edit');
    Route::post('/sessions/{id}/update', [SessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{id}/delete', [SessionController::class, 'destroy'])->name('sessions.delete');

    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects/store', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::post('/subjects/{id}/update', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}/delete', [SubjectController::class, 'destroy'])->name('subjects.delete');

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
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::post('/students/{id}/update', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}/delete', [StudentController::class, 'destroy'])->name('students.delete');
    Route::get('/students/progress', [StudentController::class, 'progressTracking'])->name('students.progress');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

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
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/create', [FeeController::class, 'create'])->name('fees.create');
    Route::post('/fees/store', [FeeController::class, 'store'])->name('fees.store');
    Route::get('/fees/{id}/edit', [FeeController::class, 'edit'])->name('fees.edit');
    Route::post('/fees/{id}/update', [FeeController::class, 'update'])->name('fees.update');
    Route::delete('/fees/{id}/delete', [FeeController::class, 'destroy'])->name('fees.delete');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');

    Route::prefix('scholarships')->group(function () {
        Route::get('/', [ScholarshipController::class, 'index'])->name('scholarships.index');
        Route::get('/create', [ScholarshipController::class, 'create'])->name('scholarships.create');
        Route::post('/store', [ScholarshipController::class, 'store'])->name('scholarships.store');
        Route::get('/applications', [ScholarshipController::class, 'applications'])->name('scholarships.applications');
        Route::get('/awards', [ScholarshipController::class, 'awards'])->name('scholarships.awards');
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
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/marks', [MarkController::class, 'index'])->name('marks.index');
    Route::post('/marks/store', [MarkController::class, 'store'])->name('marks.store');
    Route::get('/onlineexams', [OnlineExamController::class, 'index'])->name('onlineexams.index');

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
    Route::get('/transport', [TransportController::class, 'index'])->name('transport.index');

    // ------------------- Dormitory -------------------
    Route::get('/dormitory', [DormitoryController::class, 'index'])->name('dormitory.index');

    // ------------------- Asset Management -------------------
    Route::prefix('assets')->group(function () {
        Route::get('/property', [AssetController::class, 'property'])->name('assets.property');
        Route::get('/equipment', [AssetController::class, 'equipment'])->name('assets.equipment');
        Route::get('/equipment/create', [AssetController::class, 'createEquipment'])->name('assets.equipment.create');
        Route::post('/equipment/store', [AssetController::class, 'storeEquipment'])->name('assets.equipment.store');
        Route::get('/maintenance', [AssetController::class, 'maintenance'])->name('assets.maintenance');
        Route::get('/depreciation', [AssetController::class, 'depreciation'])->name('assets.depreciation');
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

        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll/store', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
        Route::post('/payroll/{id}/update', [PayrollController::class, 'update'])->name('payroll.update');
        Route::delete('/payroll/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
        Route::get('/payroll/reports', [PayrollController::class, 'reports'])->name('payroll.reports');
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
        Route::get('/items', [InventoryItemController::class, 'index'])->name('inventory.items');
        Route::get('/items/create', [InventoryItemController::class, 'create'])->name('inventory.items.create');
        Route::post('/items', [InventoryItemController::class, 'store'])->name('inventory.items.store');
        Route::get('/items/{id}/edit', [InventoryItemController::class, 'edit'])->name('inventory.items.edit');
        Route::put('/items/{id}', [InventoryItemController::class, 'update'])->name('inventory.items.update');
        Route::delete('/items/{id}', [InventoryItemController::class, 'destroy'])->name('inventory.items.destroy');

        Route::get('/categories', [InventoryCategoryController::class, 'index'])->name('inventory.categories');
        Route::get('/categories/create', [InventoryCategoryController::class, 'create'])->name('inventory.categories.create');
        Route::post('/categories', [InventoryCategoryController::class, 'store'])->name('inventory.categories.store');
        Route::get('/categories/{id}/edit', [InventoryCategoryController::class, 'edit'])->name('inventory.categories.edit');
        Route::put('/categories/{id}', [InventoryCategoryController::class, 'update'])->name('inventory.categories.update');
        Route::delete('/categories/{id}', [InventoryCategoryController::class, 'destroy'])->name('inventory.categories.destroy');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('inventory.suppliers');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('inventory.suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('inventory.suppliers.store');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('inventory.suppliers.edit');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('inventory.suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('inventory.suppliers.destroy');

        Route::get('/requests', [InventoryRequestController::class, 'index'])->name('inventory.requests');
        Route::get('/requests/create', [InventoryRequestController::class, 'create'])->name('inventory.requests.create');
        Route::post('/requests', [InventoryRequestController::class, 'store'])->name('inventory.requests.store');
        Route::get('/requests/{id}/edit', [InventoryRequestController::class, 'edit'])->name('inventory.requests.edit');
        Route::put('/requests/{id}', [InventoryRequestController::class, 'update'])->name('inventory.requests.update');
        Route::delete('/requests/{id}', [InventoryRequestController::class, 'destroy'])->name('inventory.requests.destroy');

        Route::get('/stock', [StockController::class, 'index'])->name('inventory.stock');
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

    // ------------------- Settings -------------------
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

    // ------------------- Homework -------------------
    Route::get('/homework', [LessonPlanController::class, 'homework'])->name('homework.index');

});