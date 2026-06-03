<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\AcademicCalendar;
use App\Models\AcademicSession;
use App\Models\GalleryItem;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Notice;
use App\Models\SchoolSetting;
use App\Models\SchoolClass;
use App\Models\SeoMeta;
use App\Models\WebsitePage;
use App\Models\WebsiteSetting;
use App\Models\WebsiteBanner;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PublicWebsiteController extends Controller
{
    public function home()
    {
        $page = WebsitePage::query()->published()->where('page_type', 'home')->with('sections')->first();
        $featuredPages = WebsitePage::query()->published()->latest('published_at')->take(3)->get();
        $notices = Notice::query()->latest('published_at')->latest()->take(5)->get();
        $events = Event::query()->latest('event_date')->latest('published_at')->take(5)->get();
        $galleryItems = GalleryItem::query()->active()->latest('sort_order')->latest()->take(6)->get();
        $sliders = WebsiteBanner::query()->active()->get();
        $stats = [
            ['icon' => 'M12 4.354v4.812-4.812a4 4 0 00-3.584-5.253 4 4 0 015.253 3.584zM7 4v4a4 4 0 004 4h4a4 4 0 004-4V4a4 4 0 00-4-4H7zM4 12v4a4 4 0 004 4h4a4 4 0 004-4v-4a4 4 0 00-4-4H8a4 4 0 00-4 4zM20 12v4a4 4 0 01-4 4h-4a4 4 0 01-4-4v-4a4 4 0 014-4h4a4 4 0 014 4z', 'label' => 'Total Students', 'value' => Student::query()->where('status', 1)->count()],
            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Teachers', 'value' => Employee::query()->active()->whereHas('designation', fn($q) => $q->teachers())->count()],
            ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Classes', 'value' => SchoolClass::query()->where('status', 1)->count()],
            ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Notices', 'value' => Notice::count()],
        ];

        return view('website.pages.home', $this->shared('home') + compact('page', 'featuredPages', 'notices', 'events', 'sliders', 'galleryItems', 'stats'));
    }

    public function about()
    {
        $page = $this->publishedPage('about');
        
        // Get teachers (employees with teacher-type designations)
        $teachers = Employee::query()->active()
            ->whereHas('designation', fn($q) => $q->teachers())
            ->with(['designation', 'department'])
            ->latest()
            ->get();
        
        // If no teachers found via designation type, get active employees with teaching-related roles
        if ($teachers->isEmpty()) {
            $teachers = Employee::query()->active()
                ->whereHas('designation', fn($q) => $q->where('name', 'like', '%Teacher%')
                    ->orWhere('name', 'like', '%Professor%')
                    ->orWhere('name', 'like', '%Lecturer%')
                    ->orWhere('name', 'like', '%Instructor%'))
                ->with(['designation', 'department'])
                ->latest()
                ->get();
        }
        
        // Get staff (employees with staff-type designations)
        $staff = Employee::query()->active()
            ->whereHas('designation', fn($q) => $q->staff())
            ->with(['designation', 'department'])
            ->latest()
            ->get();

        return view('website.pages.about', $this->shared('about') + compact('page', 'teachers', 'staff'));
    }

    public function teachers()
    {
        $teachers = Employee::query()->active()
            ->whereHas('designation', fn($q) => $q->teachers())
            ->with(['designation', 'department'])
            ->latest()
            ->paginate(12);

        if ($teachers->isEmpty()) {
            $teachers = Employee::query()->active()
                ->whereHas('designation', fn($q) => $q->where('name', 'like', '%Teacher%')
                    ->orWhere('name', 'like', '%Professor%')
                    ->orWhere('name', 'like', '%Lecturer%')
                    ->orWhere('name', 'like', '%Instructor%'))
                ->with(['designation', 'department'])
                ->latest()
                ->paginate(12);
        }

        return view('website.pages.teachers', $this->shared('teachers') + compact('teachers'));
    }

    public function teacherProfile(Employee $employee)
    {
        $employee->load(['designation', 'department']);
        
        // Ensure this is a teacher
        if (!$employee->designation || !str_contains(strtolower($employee->designation->name), 'teacher') && 
            !str_contains(strtolower($employee->designation->name), 'professor') &&
            !str_contains(strtolower($employee->designation->name), 'lecturer')) {
            abort(404);
        }

        return view('website.pages.teacher-profile', $this->shared('teacher') + compact('employee'));
    }

    public function staff()
    {
        $staff = Employee::query()->active()
            ->whereHas('designation', fn($q) => $q->staff())
            ->with(['designation', 'department'])
            ->latest()
            ->paginate(12);

        return view('website.pages.staff', $this->shared('staff') + compact('staff'));
    }

    public function staffProfile(Employee $employee)
    {
        $employee->load(['designation', 'department']);
        return view('website.pages.staff-profile', $this->shared('staff-member') + compact('employee'));
    }

    public function resultCheck()
    {
        $sessions = AcademicSession::query()->where('is_active', true)->orderBy('name')->get();
        $examTypes = [
            Exam::TYPE_TERMINAL => 'Terminal Exam',
            Exam::TYPE_TUTORIAL => 'Tutorial Exam',
        ];
        return view('website.pages.result-check', $this->shared('result') + compact('sessions', 'examTypes'));
    }

    public function resultShow(Request $request)
    {
        $data = $request->validate([
            'student_cid' => ['required', 'string'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
            'exam_type' => ['required', 'in:term,tutorial'],
        ]);

        $student = Student::query()->where('student_cid', $data['student_cid'])->first();
        
        if (!$student) {
            return back()->with('error', 'Student not found with this CID.');
        }

        $exams = Exam::query()
            ->where('academic_session_id', $data['session_id'])
            ->where('type', $data['exam_type'])
            ->where('status', Exam::STATUS_PUBLISHED)
            ->orderBy('name')
            ->get();

        // Get marks for these exams and student
        $marksByExam = collect();
        if ($exams->isNotEmpty()) {
            $marksByExam = ExamMark::query()
                ->whereIn('exam_id', $exams->pluck('id'))
                ->where('student_id', $student->id)
                ->with('subject')
                ->get()
                ->groupBy('exam_id');
        }

        // Get terminal exam for final report
        $terminalExam = Exam::query()
            ->where('academic_session_id', $data['session_id'])
            ->where('type', Exam::TYPE_TERMINAL)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->first();

        $terminalMarks = collect();
        if ($terminalExam) {
            $terminalMarks = ExamMark::query()
                ->where('exam_id', $terminalExam->id)
                ->where('student_id', $student->id)
                ->with('subject')
                ->get();
        }

        return view('website.pages.result-show', $this->shared('result') + compact(
            'student', 
            'exams', 
            'data', 
            'marksByExam',
            'terminalExam',
            'terminalMarks'
        ));
    }

    public function academicCalendar()
    {
        $page = $this->publishedPage('calendar');
        $items = AcademicCalendar::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->paginate(12);
        return view('website.pages.academic-calendar', $this->shared('academic-calendar') + compact('items', 'page'));
    }

    public function notices()
    {
        $page = $this->publishedPage('notices');
        $notices = Notice::query()->latest('published_at')->latest()->paginate(10);
        return view('website.pages.notices', $this->shared('notices') + compact('notices', 'page'));
    }

    public function noticeShow(Notice $notice)
    {
        $page = $this->publishedPage('notices');

        return view('website.pages.notice-show', $this->shared('notices') + compact('notice', 'page'));
    }

    public function events()
    {
        $page = $this->publishedPage('events');
        $events = Event::query()->latest('event_date')->latest('published_at')->paginate(10);
        return view('website.pages.events', $this->shared('events') + compact('events', 'page'));
    }

    public function eventShow(Event $event)
    {
        $page = $this->publishedPage('events');

        return view('website.pages.event-show', $this->shared('events') + compact('event', 'page'));
    }

    public function gallery()
    {
        $page = $this->publishedPage('gallery');
        $items = GalleryItem::query()->active()->latest('sort_order')->latest()->paginate(12);

        return view('website.pages.gallery', $this->shared('gallery') + compact('items', 'page'));
    }

    public function holidays()
    {
        $page = $this->publishedPage('holidays');
        $holidays = Holiday::query()->orderBy('date')->paginate(15);

        return view('website.pages.holidays', $this->shared('holidays') + compact('holidays', 'page'));
    }

    public function examSchedule()
    {
        $page = $this->publishedPage('exam-schedule');
        $exams = Exam::query()
            ->where('status', Exam::STATUS_PUBLISHED)
            ->with('academicSession')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(12);

        return view('website.pages.exam-schedule', $this->shared('exam-schedule') + compact('exams', 'page'));
    }

    public function contact()
    {
        $page = $this->publishedPage('contact');
        return view('website.pages.contact', $this->shared('contact') + compact('page'));
    }

    public function page(string $slug)
    {
        $page = $this->publishedPage($slug, true);

        return view('website.pages.page', $this->shared($page->slug) + compact('page'));
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Message sent successfully.');
    }

    public function finalReport(Request $request)
    {
        $data = $request->validate([
            'student_cid' => ['required', 'string'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
        ]);

        $student = Student::query()->where('student_cid', $data['student_cid'])->first();
        
        if (!$student) {
            return back()->with('error', 'Student not found with this CID.');
        }

        // Get terminal exam marks for final report
        $terminalExams = Exam::query()
            ->where('academic_session_id', $data['session_id'])
            ->where('type', Exam::TYPE_TERMINAL)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->get();

        $terminalMarks = collect();
        if ($terminalExams->isNotEmpty()) {
            $terminalMarks = ExamMark::query()
                ->whereIn('exam_id', $terminalExams->pluck('id'))
                ->where('student_id', $student->id)
                ->with('subject')
                ->get();
        }

        return view('website.pages.final-report', $this->shared('result') + compact(
            'student', 
            'terminalExams',
            'terminalMarks'
        ));
    }

    protected function shared(string $slug): array
    {
        $settings = WebsiteSetting::query()->pluck('value', 'key');
        $schoolSetting = SchoolSetting::query()->first();
        $seo = SeoMeta::query()->where('page_slug', $slug)->first();
        $navLinks = $this->navigationLinks();

        $logo = $schoolSetting?->logo;
        if ($logo && !str_starts_with($logo, 'http://') && !str_starts_with($logo, 'https://')) {
            $logo = asset($logo);
        }

        $site = [
            'name' => $schoolSetting?->name ?: ($settings['school_name'] ?? config('app.name')),
            'tagline' => $schoolSetting?->slogan ?: ($settings['tagline'] ?? 'Learning for life'),
            'address' => $schoolSetting?->address ?: ($settings['address'] ?? null),
            'email' => $schoolSetting?->email ?: ($settings['contact_email'] ?? null),
            'phone_primary' => $schoolSetting?->contact_number_1 ?: ($settings['contact_phone'] ?? null),
            'phone_secondary' => $schoolSetting?->contact_number_2,
            'logo' => $logo,
            'footer_about' => $settings['footer_about'] ?? 'We are committed to providing quality education that empowers students to achieve their full potential and become responsible citizens.',
            'social_facebook' => $settings['social_facebook'] ?? null,
            'social_instagram' => $settings['social_instagram'] ?? null,
            'social_youtube' => $settings['social_youtube'] ?? null,
            'social_linkedin' => $settings['social_linkedin'] ?? null,
        ];

        return compact('settings', 'schoolSetting', 'site', 'seo', 'navLinks');
    }

    protected function publishedPage(string $typeOrSlug, bool $bySlug = false): ?WebsitePage
    {
        $query = WebsitePage::query()->published()->with('sections');

        return $bySlug
            ? $query->where('slug', $typeOrSlug)->firstOrFail()
            : $query->where('page_type', $typeOrSlug)->first();
    }

    protected function navigationLinks(): array
    {
        $pages = WebsitePage::query()
            ->published()
            ->whereIn('page_type', WebsitePage::systemNavigationTypes())
            ->get()
            ->keyBy('page_type');

        $links = [];

        foreach (WebsitePage::systemPageDefinitions() as $type => $meta) {
            $page = $pages->get($type);

            if (!$page && !in_array($type, ['home', 'about', 'notices', 'events', 'calendar', 'gallery', 'contact'], true)) {
                continue;
            }

            $routeName = $page ? WebsitePage::publicRouteNameFor($page) : $meta['route'];

            $links[] = [
                'type' => $type,
                'label' => $meta['label'],
                'route_name' => $routeName,
                'url' => $page ? WebsitePage::publicUrlFor($page) : route($meta['route']),
            ];
        }

        return $links;
    }
}
