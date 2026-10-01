<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\AttendanceItem;
use App\Models\ClassRoutine;
use App\Models\ExamMark;
use App\Models\Fee;
use App\Models\MobileNotificationLog;
use App\Models\Notice;
use App\Models\Holiday;
use App\Models\WeekendSetting;
use App\Models\Student;
use App\Models\StudentDevice;
use App\Models\Payment;
use App\Models\SubjectClassAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PaymentController;
use App\Services\StudentAudienceService;
use App\Services\GradingService;
use App\Services\ResultRankingService;

class StudentApiController extends Controller
{
    public function __construct(private readonly StudentAudienceService $audience) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:255'],
            'device_token' => ['nullable', 'string', 'max:4096'],
            'platform' => ['nullable', 'in:android,ios'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ]);

        $student = Student::query()
            ->where('student_cid', $data['login'])
            ->orWhereHas('user', fn ($query) => $query->where('email', $data['login']))
            ->with(['user', 'latestAcademicInformation.schoolClass', 'latestAcademicInformation.section'])
            ->first();

        $user = $student?->user;
        if (!$user || !$user->is_active || !Hash::check($data['password'], $user->password) || !$this->audience->isActive($student)) {
            throw ValidationException::withMessages(['login' => ['The student credentials are invalid or the student account is inactive.']]);
        }

        $token = $user->createToken('student-mobile', ['student'], now()->addDays(30))->plainTextToken;
        if (!empty($data['device_token'])) {
            $this->saveDevice($user, $student, $data);
        }

        return response()->json(['token' => $token, 'user' => $this->userPayload($user, $student)]);
    }

    public function logout(Request $request): JsonResponse
    {
        $data = $request->validate(['device_token' => ['nullable', 'string', 'max:512']]);
        if (!$request->user()) {
            return response()->json(['message' => 'Already logged out.']);
        }
        if (!empty($data['device_token'])) {
            $request->user()->studentDevices()->where('fcm_token', $data['device_token'])->update(['is_active' => false]);
        }
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        return response()->json(['user' => $this->userPayload($request->user(), $student)]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $attendance = AttendanceItem::query()->where('student_id', $student->id);
        $totalAttendance = (clone $attendance)->count();
        $absentAttendance = (clone $attendance)->where('status', 'absent')->count();
        $due = Fee::query()->where('student_id', $student->id)->where('is_active', true)->get()
            ->sum(fn (Fee $fee) => max(0, (float) $fee->due_amount));
        $todayAttendance = AttendanceItem::query()->with('attendance:id,date')->where('student_id', $student->id)
            ->whereHas('attendance', fn ($query) => $query->whereDate('date', today()))->first();
        $todayRoutine = $this->routineQuery($student)->where('day', now()->format('l'))->get();

        return response()->json([
            'student' => $this->studentPayload($student),
            'summary' => [
                'attendance_total' => $totalAttendance,
                'attendance_absent' => $absentAttendance,
                'attendance_percentage' => $totalAttendance ? round((($totalAttendance - $absentAttendance) / $totalAttendance) * 100, 2) : null,
                'fee_due' => round((float) $due, 2),
                'latest_result' => $this->latestResult($student),
                'today_attendance' => $todayAttendance?->status,
                'today_routine' => $todayRoutine->map(fn ($item) => $this->routinePayload($item))->values(),
                'latest_notices' => Notice::published()->latest('published_at')->limit(3)->get(['id', 'title', 'published_at'])->map(fn ($notice) => $this->noticePayload($notice)),
            ],
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $data = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $filterAttendance = function ($attendance) use ($data): void {
            $attendance->when($data['month'] ?? null, fn ($query, $month) => $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month]))
                ->when($data['from'] ?? null, fn ($query, $from) => $query->whereDate('date', '>=', $from))
                ->when($data['to'] ?? null, fn ($query, $to) => $query->whereDate('date', '<=', $to));
        };
        $itemsQuery = AttendanceItem::query()->with('attendance:id,date')->where('student_id', $student->id)
            ->whereHas('attendance', $filterAttendance)
            ->latest('id');
        $items = $itemsQuery->paginate(30);
        $all = AttendanceItem::query()->where('student_id', $student->id)
            ->whereHas('attendance', $filterAttendance);
        $total = (clone $all)->count();
        $absent = (clone $all)->where('status', 'absent')->count();

        $items->getCollection()->transform(fn ($item) => $this->attendancePayload($item));
        return response()->json(['summary' => ['total' => $total, 'absent' => $absent, 'present' => $total - $absent], 'items' => $items]);
    }

    public function routine(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $routine = $this->routineQuery($student)
            ->orderByRaw("FIELD(day, 'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday')")
            ->orderBy('start_time')->get();

        return response()->json([
            'weekend_days' => array_map(
                static fn ($day) => (int) $day,
                WeekendSetting::current()->days()
            ),
            'items' => $routine->map(fn ($item) => $this->routinePayload($item))->values(),
        ]);
    }

    public function holidays(Request $request): JsonResponse
    {
        $this->activeStudent($request);
        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $holidays = Holiday::query()
            ->when(
                $data['from'] ?? null,
                fn ($query, $from) => $query->whereDate('date', '>=', $from)
            )
            ->when(
                $data['to'] ?? null,
                fn ($query, $to) => $query->whereDate('date', '<=', $to)
            )
            ->orderBy('date')
            ->get();

        return response()->json([
            'items' => $holidays->map(fn (Holiday $holiday) => [
                'id' => $holiday->id,
                'date' => $holiday->date?->toDateString(),
                'title' => $holiday->title,
                'description' => $holiday->description,
            ])->values(),
        ]);
    }

    public function results(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $data = $request->validate([
            'session_id' => ['nullable', 'integer'],
            'exam_type' => ['nullable', 'string', 'max:30'],
            'exam_id' => ['nullable', 'integer'],
        ]);
        $marks = ExamMark::query()->with(['exam:id,name,type,year,status,exam_category,academic_session_id', 'subject:id,name,code'])
            ->where('student_id', $student->id)->whereHas('exam', fn ($query) => $query->where('status', 'published'))
            ->when($data['session_id'] ?? null, fn ($query, $id) => $query->whereHas('exam', fn ($exam) => $exam->where('academic_session_id', $id)))
            ->when($data['exam_type'] ?? null, fn ($query, $type) => $query->whereHas('exam', fn ($exam) => $exam->where('exam_category', $type)))
            ->when($data['exam_id'] ?? null, fn ($query, $id) => $query->where('exam_id', $id))
            ->latest('id')->get();
        $marks = $this->filterResultMarks($student, $marks);
        $marks = $marks->groupBy('exam_id')->map(function ($examMarks) {
            $exam = $examMarks->first()->exam;
            $summary = $this->resultSummary($examMarks);
            return ['exam' => $this->examPayload($exam), 'summary' => $summary, 'subjects' => $examMarks->map(fn ($mark) => $this->markPayload($mark))->values()];
        })->values();

        $marks = $marks->map(function (array $result) use ($student) {
            $exam = Exam::find($result['exam']['id']);
            if ($exam) {
                $result['summary']['position'] = $this->resultPositions($student, $exam)[$student->id] ?? null;
            }
            return $result;
        })->values();

        return response()->json(['items' => $marks]);
    }

    public function resultOptions(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $exams = Exam::query()->with('academicSession:id,name_en,name_bn')
            ->where('status', 'published')
            ->whereHas('marks', fn ($query) => $query->where('student_id', $student->id))
            ->latest('year')->latest('id')->get();

        $sessions = $exams->groupBy('academic_session_id')->map(function ($sessionExams) {
            $session = $sessionExams->first()->academicSession;
            return [
                'id' => $session?->id,
                'name' => $session?->name_en ?: $session?->name_bn ?: 'Academic session',
                'types' => $sessionExams->groupBy(fn ($exam) => $exam->exam_category)->map(fn ($typeExams, $type) => [
                    'value' => $type,
                    'label' => $type === 'terminal' ? 'Terminal Exam' : ($type === 'tutorial' ? 'Tutorial Exam' : ucfirst($type)),
                    'exams' => $typeExams->map(fn ($exam) => ['id' => $exam->id, 'name' => $exam->name, 'year' => $exam->year])->values(),
                ])->values(),
            ];
        })->values();

        return response()->json(['sessions' => $sessions]);
    }

    public function fees(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $fees = Fee::query()->with('feeSet:id,name')->where('student_id', $student->id)->where('is_active', true)->latest('id')->get();
        $payments = $student->payments()->latest('payment_date')->latest('id')->limit(50)->get(['id', 'amount', 'payment_date', 'payment_method', 'receipt_no', 'remarks']);
        return response()->json(['summary' => ['total' => round((float) $fees->sum(fn ($fee) => $fee->net_amount), 2), 'paid' => round((float) $fees->sum('paid_amount'), 2), 'due' => round((float) $fees->sum(fn ($fee) => max(0, $fee->due_amount)), 2)], 'fees' => $fees->map(fn ($fee) => ['id' => $fee->id, 'name' => $fee->feeSet?->name, 'amount' => (float) $fee->amount, 'discount' => (float) $fee->scholarship_discount, 'paid_amount' => (float) $fee->paid_amount, 'due_amount' => max(0, (float) $fee->due_amount), 'due_date' => $fee->due_date]), 'payments' => $payments->map(fn ($payment) => ['id' => $payment->id, 'amount' => (float) $payment->amount, 'payment_date' => $payment->payment_date, 'payment_method' => $payment->payment_method, 'receipt_no' => $payment->receipt_no])]);
    }

    public function payments(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        return response()->json(['items' => $student->payments()->latest('payment_date')->latest('id')->paginate(30)->through(fn ($payment) => ['id' => $payment->id, 'amount' => (float) $payment->amount, 'payment_date' => $payment->payment_date, 'payment_method' => $payment->payment_method, 'receipt_no' => $payment->receipt_no])]);
    }

    public function receipt(Request $request, int $payment)
    {
        $student = $this->activeStudent($request);
        $record = Payment::query()
            ->where('id', $payment)
            ->where('student_id', $student->id)
            ->firstOrFail();

        return app(PaymentController::class)->receiptPdf($record);
    }

    public function notices(Request $request): JsonResponse
    {
        $this->activeStudent($request);
        $items = Notice::published()->latest('published_at')->paginate(20);
        $items->getCollection()->transform(fn ($notice) => $this->noticePayload($notice));
        return response()->json(['items' => $items]);
    }

    public function result(Request $request, int $result): JsonResponse
    {
        $student = $this->activeStudent($request);
        $marks = ExamMark::query()->with(['exam:id,name,type,year,status,exam_category,academic_session_id', 'subject:id,name,code'])
            ->where('student_id', $student->id)->where('exam_id', $result)
            ->whereHas('exam', fn ($query) => $query->where('status', 'published'))->get();
        $marks = $this->filterResultMarks($student, $marks);
        abort_if($marks->isEmpty(), 404);
        $exam = $marks->first()->exam;
        return response()->json(['exam' => $this->examPayload($exam), 'summary' => $this->resultSummary($marks), 'subjects' => $marks->map(fn ($mark) => $this->markPayload($mark))->values()]);
    }

    public function homework(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $academic = $student->latestAcademicInformation;
        $query = Assignment::query()->with('course:id,title_en,title_bn,school_class_id,section_id')
            ->where('status', 'published')
            ->whereHas('course', function ($course) use ($academic) {
                $course->where('status', 'active')
                    ->where(function ($q) use ($academic) {
                        $q->whereNull('school_class_id')->orWhere('school_class_id', $academic->school_class_id);
                    })
                    ->where(function ($q) use ($academic) {
                        $q->whereNull('section_id')->orWhere('section_id', $academic->section_id);
                    });
            })->latest('due_date');
        $items = $query->paginate(20);
        $items->getCollection()->transform(fn ($assignment) => [
            'id' => $assignment->id,
            'title' => $assignment->title_en ?: $assignment->title_bn,
            'description' => $assignment->description,
            'due_date' => $assignment->due_date,
            'attachment' => $assignment->attachment,
            'course' => $assignment->course ? ['id' => $assignment->course->id, 'title' => $assignment->course->title_en ?: $assignment->course->title_bn] : null,
        ]);
        return response()->json(['items' => $items]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $student = $this->activeStudent($request);
        $items = MobileNotificationLog::query()->where('student_id', $student->id)->latest()->paginate(30);
        $items->getCollection()->transform(fn ($item) => [
            'id' => $item->id,
            'type' => $item->notification_type,
            'title' => $item->title,
            'body' => $item->body,
            'status' => $item->delivery_status,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
        ]);
        return response()->json(['items' => $items]);
    }

    public function registerDevice(Request $request): JsonResponse
    {
        $data = $request->validate(['device_token' => ['required', 'string', 'max:4096'], 'platform' => ['required', 'in:android,ios'], 'app_version' => ['nullable', 'string', 'max:50']]);
        $student = $this->activeStudent($request);
        $device = $this->saveDevice($request->user(), $student, $data);
        return response()->json(['device' => $device], 201);
    }

    public function removeDevice(Request $request, StudentDevice $device): JsonResponse
    {
        abort_unless($device->user_id === $request->user()->id, 404);
        $device->update(['is_active' => false]);
        return response()->json(['message' => 'Device disabled.']);
    }

    public function markNotificationRead(Request $request, MobileNotificationLog $notification): JsonResponse
    {
        abort_unless($notification->student_id === $this->activeStudent($request)->id, 404);
        $notification->update(['delivery_status' => 'read']);
        return response()->json(['message' => 'Notification marked as read.']);
    }

    private function activeStudent(Request $request): Student
    {
        $student = $request->user()->student()->with(['latestAcademicInformation.schoolClass', 'latestAcademicInformation.section'])->first();
        abort_unless($student && $this->audience->isActive($student), 403, 'This student account is no longer active.');
        return $student;
    }

    private function saveDevice($user, Student $student, array $data): StudentDevice
    {
        return StudentDevice::updateOrCreate(['user_id' => $user->id, 'fcm_token' => $data['device_token']], ['student_id' => $student->id, 'platform' => $data['platform'] ?? 'android', 'app_version' => $data['app_version'] ?? null, 'last_seen_at' => now(), 'is_active' => true]);
    }

    private function userPayload($user, Student $student): array
    {
        return ['id' => $user->id, 'email' => $user->email, 'student' => $this->studentPayload($student)];
    }

    private function studentPayload(Student $student): array
    {
        $academic = $student->latestAcademicInformation;
        return ['id' => $student->id, 'cid' => $student->student_cid, 'name' => $student->full_name_en ?: $student->full_name_bn, 'photo_url' => $student->photo_url, 'class' => $academic?->schoolClass?->name_en, 'section' => $academic?->section?->name_en, 'roll' => $academic?->roll];
    }

    private function latestResult(Student $student): ?array
    {
        $mark = ExamMark::query()->with('exam:id,name,type,year')->where('student_id', $student->id)->whereHas('exam', fn ($q) => $q->where('status', 'published'))->latest()->first();
        if (!$mark) return null;
        $marks = ExamMark::query()->with('exam:id,name,type,year,academic_session_id')->where('student_id', $student->id)->where('exam_id', $mark->exam_id)->get();
        $marks = $this->filterResultMarks($student, $marks);
        if ($marks->isEmpty()) return null;
        return ['exam' => $mark->exam?->name, ...$this->resultSummary($marks)];
    }

    private function filterResultMarks(Student $student, Collection $marks): Collection
    {
        return $marks->groupBy('exam_id')->flatMap(function (Collection $examMarks) use ($student) {
            $exam = $examMarks->first()?->exam;
            if (!$exam) return collect();
            $academic = $this->academicInfoForResult($student, $exam);
            if (!$academic) return collect();

            $allowedSubjectIds = SubjectClassAssignment::query()
                ->where('school_class_id', $academic->school_class_id)
                ->where('is_active', true)
                ->where(function ($query) use ($academic) {
                    $query->whereNull('group_id')
                        ->orWhere('group_id', $academic->group_id);
                })
                ->get(['subject_id', 'gender', 'religion'])
                ->filter(fn (SubjectClassAssignment $assignment) => $assignment->appliesToStudent($student->gender, $student->religion))
                ->pluck('subject_id');

            return $examMarks->filter(fn (ExamMark $mark) => $allowedSubjectIds->contains((int) $mark->subject_id));
        })->values();
    }

    private function academicInfoForResult(Student $student, Exam $exam)
    {
        return $student->academicInformations()
            ->where('academic_session_id', $exam->academic_session_id)
            ->where('is_current', true)
            ->first()
            ?? $student->latestAcademicInformation;
    }

    private function routineQuery(Student $student)
    {
        $academic = $student->latestAcademicInformation;
        return ClassRoutine::query()->with(['subject:id,name,code', 'teacher:id,name', 'classroom:id,name', 'timeSchedule:id,name,start_time,end_time'])
            ->where('school_class_id', $academic->school_class_id)->where('section_id', $academic->section_id)
            ->when($academic->academic_session_id, fn ($query) => $query->where('academic_session_id', $academic->academic_session_id));
    }

    private function attendancePayload(AttendanceItem $item): array
    {
        return ['id' => $item->id, 'date' => $item->attendance?->date?->toDateString(), 'status' => $item->status, 'note' => $item->note];
    }

    private function routinePayload(ClassRoutine $item): array
    {
        return [
            'id' => $item->id,
            'day' => $item->day,
            'start_time' => $item->start_time,
            'end_time' => $item->end_time,
            'subject' => $item->subject ? ['id' => $item->subject->id, 'name' => $item->subject->name, 'code' => $item->subject->code] : null,
            'teacher' => $item->teacher ? ['id' => $item->teacher->id, 'name' => $item->teacher->name] : null,
            'classroom' => $item->classroom ? ['id' => $item->classroom->id, 'name' => $item->classroom->name] : null,
            'time_schedule' => $item->timeSchedule ? ['id' => $item->timeSchedule->id, 'name' => $item->timeSchedule->name, 'start_time' => $item->timeSchedule->start_time, 'end_time' => $item->timeSchedule->end_time] : null,
        ];
    }

    private function examPayload($exam): array
    {
        return ['id' => $exam->id, 'name' => $exam->name, 'type' => $exam->type, 'exam_type' => $exam->exam_category, 'year' => $exam->year, 'academic_session_id' => $exam->academic_session_id];
    }

    private function markPayload(ExamMark $mark): array
    {
        return ['id' => $mark->id, 'subject' => $mark->subject ? ['id' => $mark->subject->id, 'name' => $mark->subject->name, 'code' => $mark->subject->code] : null, 'cq_marks' => $mark->cq_marks, 'mcq_marks' => $mark->mcq_marks, 'practical_marks' => $mark->practical_marks, 'viva_marks' => $mark->viva_marks, 'tutorial_marks' => $mark->tutorial_marks, 'total' => $mark->total, 'is_absent' => (bool) $mark->is_absent, 'letter_grade' => $mark->letter_grade, 'gpa' => $mark->gpa];
    }

    private function noticePayload(Notice $notice): array
    {
        return ['id' => $notice->id, 'title' => $notice->title, 'content' => $notice->content, 'published_at' => $notice->published_at];
    }

    private function resultSummary($marks): array
    {
        $failed = $marks->filter(fn ($mark) => $mark->is_absent || (float) $mark->gpa === 0.0 || $mark->letter_grade === 'F')->count();
        $gpa = GradingService::calculateGpa(
            $marks->map(fn ($mark) => $mark->is_absent ? 0 : (float) $mark->gpa)->values()->all(),
            $failed > 0,
        );
        return ['total' => round((float) $marks->sum('total'), 2), 'gpa' => $gpa, 'grade' => GradingService::getGpaLabel($gpa), 'failed_subjects' => $failed];
    }

    private function resultPositions(Student $student, Exam $exam): array
    {
        $academic = $this->academicInfoForResult($student, $exam);
        if (!$academic) return [];

        $students = Student::query()
            ->where('status', 1)
            ->whereHas('academicInformations', function ($query) use ($exam, $academic) {
                $query->where('academic_session_id', $exam->academic_session_id)
                    ->where('school_class_id', $academic->school_class_id)
                    ->where('section_id', $academic->section_id)
                    ->where('is_current', true)
                    ->where('academic_status', 'active');
            })
            ->get();
        $allMarks = ExamMark::query()
            ->with(['exam:id,name,type,year,status,exam_category,academic_session_id', 'subject:id,name,code'])
            ->where('exam_id', $exam->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $rows = [];
        foreach ($students as $cohortStudent) {
            $studentMarks = $this->filterResultMarks(
                $cohortStudent,
                $allMarks->get($cohortStudent->id, collect())
            );
            if ($studentMarks->isEmpty()) continue;
            $summary = $this->resultSummary($studentMarks);
            $rows[$cohortStudent->id] = [
                'student' => $cohortStudent,
                'summary' => $summary,
            ];
        }

        $ranked = app(ResultRankingService::class)->rank(
            $rows,
            fn (array $row) => [
                'failed_subjects' => $row['summary']['failed_subjects'] ?? 0,
                'total' => $row['summary']['total'] ?? 0,
                'gpa' => $row['summary']['gpa'] ?? 0,
            ],
            fn (array $row) => $row['student']->student_cid ?? $row['student']->id,
        );

        return collect($ranked)->mapWithKeys(
            fn (array $row, $studentId) => [$studentId => $row['rank']]
        )->all();
    }
}
