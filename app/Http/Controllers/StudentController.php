<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\Division;
use App\Models\District;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Fee;
use App\Models\Profession;
use App\Models\SchoolSetting;
use App\Models\StudentAcademicInformation;
use App\Models\Transport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
{
    private function pdfColumnOptions(): array
    {
        return [
            'student_cid' => 'Student ID',
            'roll' => 'Roll',
            'full_name_en' => 'Student Name',
            'full_name_bn' => 'Student Name (Bangla)',
            'class' => 'Class',
            'section' => 'Section',
            'group' => 'Group',
            'gender' => 'Gender',
            'religion' => 'Religion',
            'date_of_birth' => 'Date of Birth',
            'blood_group' => 'Blood Group',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name',
            'father_phone' => 'Father Phone',
            'mother_phone' => 'Mother Phone',
            'guardian_phone' => 'Guardian Phone',
            'status' => 'Status',
        ];
    }

    private function filteredStudentsQuery(Request $request)
    {
        $query = Student::with([
            'academicInformations.academicSession',
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group'
        ]);

        if ($request->filled('academic_session_id')) {
            $query->whereHas('academicInformations', function($q) use ($request) {
                $q->where('academic_session_id', $request->academic_session_id);
            });
        }

        if ($request->filled('school_class_id')) {
            $query->whereHas('academicInformations', function($q) use ($request) {
                $q->where('school_class_id', $request->school_class_id);
            });
        }

        if ($request->filled('section_id')) {
            $query->whereHas('academicInformations', function($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('group_id')) {
            $query->whereHas('academicInformations', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->filled('permanent_division_id')) {
            $query->where('permanent_division_id', $request->permanent_division_id);
        }

        if ($request->filled('permanent_district_id')) {
            $query->where('permanent_district_id', $request->permanent_district_id);
        }

        if ($request->filled('permanent_police_station_id')) {
            $query->where('permanent_police_station_id', $request->permanent_police_station_id);
        }

        if ($request->filled('permanent_post_office_id')) {
            $query->where('permanent_post_office_id', $request->permanent_post_office_id);
        }

        if ($request->filled('phone')) {
            $phone = $request->phone;
            $query->where(function($q) use ($phone) {
                $q->where('father_phone', 'like', "%{$phone}%")
                  ->orWhere('mother_phone', 'like', "%{$phone}%")
                  ->orWhere('guardian_phone', 'like', "%{$phone}%");
            });
        }

        if ($request->filled('age_from') || $request->filled('age_to')) {
            $today = Carbon::today();

            if ($request->filled('age_from')) {
                $dateFrom = $today->copy()->subYears($request->age_from)->endOfYear();
                $query->where('date_of_birth', '<=', $dateFrom);
            }

            if ($request->filled('age_to')) {
                $dateTo = $today->copy()->subYears($request->age_to)->startOfYear();
                $query->where('date_of_birth', '>=', $dateTo);
            }
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name_en', 'like', "%{$search}%")
                  ->orWhere('full_name_bn', 'like', "%{$search}%")
                  ->orWhere('birth_certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('academicInformations', function($subQ) use ($search) {
                      $subQ->where('roll', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    private function applyStudentOrdering($query, Request $request)
    {
        $query->with(['academicInformations' => function ($q) {
            $q->orderByRaw('CAST(roll AS UNSIGNED) ASC');
        }]);

        $hasSession = $request->filled('academic_session_id');
        $hasClass = $request->filled('school_class_id');
        $hasSection = $request->filled('section_id');
        $hasGroup = $request->filled('group_id');

        if ($hasSession && ! $hasClass && ! $hasSection && ! $hasGroup) {
            return $query
                ->orderByRaw("(SELECT school_class_id FROM student_academic_information WHERE student_id = students.id AND academic_session_id = ? ORDER BY id DESC LIMIT 1) ASC", [$request->academic_session_id])
                ->orderByRaw("(SELECT CAST(roll AS UNSIGNED) FROM student_academic_information WHERE student_id = students.id AND academic_session_id = ? ORDER BY id DESC LIMIT 1) ASC", [$request->academic_session_id]);
        }

        if ($hasSession || $hasClass || $hasSection) {
            return $query
                ->orderByRaw("(SELECT CAST(roll AS UNSIGNED) FROM student_academic_information WHERE student_id = students.id ORDER BY id DESC LIMIT 1) ASC");
        }

        return $query
            ->orderByRaw("(SELECT CAST(roll AS UNSIGNED) FROM student_academic_information WHERE student_id = students.id ORDER BY id DESC LIMIT 1) ASC");
    }

    /**
     * Display a listing of students with filters
     */
    public function index(Request $request)
    {
        $query = $this->filteredStudentsQuery($request);

        $students = $this->applyStudentOrdering($query, $request)->paginate(15);

        // Get filter data
        $academicSessions = AcademicSession::where('status', 1)->get();
        $classes = SchoolClass::where('status', 1)->get();
        $sections = Section::where('status', 1)->get();
        $groups = Group::where('status', 1)->get();
        $pdfColumnOptions = $this->pdfColumnOptions();
        $divisions = Division::where('status', 1)->get();
        
        // Get districts based on selected division or all
        if ($request->filled('permanent_division_id')) {
            $districts = District::where('division_id', $request->permanent_division_id)
                                ->where('status', 1)
                                ->get();
        } else {
            $districts = District::where('status', 1)->get();
        }
        
        // Get police stations based on selected district or all
        if ($request->filled('permanent_district_id')) {
            $policeStations = PoliceStation::where('district_id', $request->permanent_district_id)
                                          ->where('status', 1)
                                          ->get();
        } else {
            $policeStations = PoliceStation::where('status', 1)->get();
        }
        
        // Get post offices based on selected police station or all
        if ($request->filled('permanent_police_station_id')) {
            $postOffices = PostOffice::where('police_station_id', $request->permanent_police_station_id)
                                    ->where('status', 1)
                                    ->get();
        } else {
            $postOffices = PostOffice::where('status', 1)->get();
        }

        return view('pages.students.index', compact(
            'students',
            'academicSessions',
            'classes',
            'sections',
            'groups',
            'divisions',
            'districts',
            'policeStations',
            'postOffices',
            'pdfColumnOptions'
        ));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $data['academicSessions'] = AcademicSession::all();
        $data['classes'] = SchoolClass::all();
        $data['sections'] = Section::all();
        $data['groups'] = Group::all();
        $data['divisions'] = Division::all();
        $data['districts'] = District::all();
        $data['policeStations'] = PoliceStation::all();
        $data['postOffices'] = PostOffice::all();
        $data['feeSets'] = FeeSet::all();
        $data['professions'] = Profession::orderBy('name')->get();
        return view('pages.students.create', $data);
    }

    /**
     * Store a newly created student
     */

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated, $request) {

            // ====== Upload Image ======
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/students'), $filename);
                $validated['student']['image'] = 'uploads/students/'.$filename;
            }

            // ====== Create Student ======
            $student = Student::create($validated['student']);

            // ====== Create Academic Info ======
            StudentAcademicInformation::create([
                'student_id'          => $student->id,
                'academic_session_id' => $request->academic_session_id,
                'school_class_id'     => $request->school_class_id,
                'section_id'          => $request->section_id,
                'group_id'            => $request->group_id,
                'roll'                => $request->roll,
            ]);

            // ====== Apply Fee Sets with Due Dates ======
            $this->applyFeeSetsToStudentWithDueDates(
                $student,
                $request->school_class_id,
                $request->academic_session_id,
                $request->group_id
            );
        });

        return redirect()->route('students.index')->with('success', 'Student created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateData($request);
        $student = Student::findOrFail($id);

        DB::transaction(function () use ($validated, $request, $student) {

            // ====== Handle Image Upload ======
            if ($request->hasFile('image')) {
                if ($student->image && file_exists(public_path($student->image))) {
                    unlink(public_path($student->image));
                }

                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/students'), $filename);
                $validated['student']['image'] = 'uploads/students/' . $filename;
            }

            // ====== Update Student ======
            $student->update($validated['student']);

            // ====== Get Old Academic Info ======
            $oldAcademic = $student->academicInformations()->first();

            // ====== Update Academic Info ======
            $student->academicInformations()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    'academic_session_id' => $request->academic_session_id,
                    'school_class_id'     => $request->school_class_id,
                    'section_id'          => $request->section_id,
                    'group_id'            => $request->group_id,
                    'roll'                => $request->roll,
                ]
            );

            // ====== Check if class/group/session changed ======
            $changed = !$oldAcademic
                || $oldAcademic->school_class_id != $request->school_class_id
                || $oldAcademic->academic_session_id != $request->academic_session_id
                || $oldAcademic->group_id != $request->group_id;

            if ($changed) {
                // Delete old fees
                Fee::where('student_id', $student->id)->delete();

                // Apply new fee sets with due dates
                $this->applyFeeSetsToStudentWithDueDates(
                    $student,
                    $request->school_class_id,
                    $request->academic_session_id,
                    $request->group_id
                );
            }
        });

        return redirect()->route('students.index')->with('success', 'Student updated successfully');
    }

    /**
     * Apply Fee Sets to a Student with Due Dates
     */
    protected function applyFeeSetsToStudentWithDueDates($student, $classId, $sessionId, $groupId = null)
    {
        $feeSets = FeeSet::with('items')
            ->where('school_class_id', $classId)
            ->where('academic_session_id', $sessionId)
            ->get();

        foreach ($feeSets as $feeSet) {

            $totalAmount = $feeSet->items->sum('amount');

            // Generate due dates
            $dueDates = $this->generateDueDates($feeSet->frequency);

            foreach ($dueDates as $dueDate) {
                Fee::create([
                    'student_id' => $student->id,
                    'fee_set_id' => $feeSet->id,
                    'amount'     => $totalAmount,
                    'due_date'   => $dueDate,
                    'status'     => 'pending',
                ]);
            }
        }
    }

    public function show($id)
    {
        $student = Student::with([
            'academicInformations.academicSession',
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group',
            'fees.feeSet',
            'payments',
        ])->findOrFail($id);

        $studentFees = $student->fees()->with(['feeSet', 'feeSet.items'])->latest()->get();

        $transportCategory = FeeCategory::where('is_transport', 1)->first();
        $transportFeeSetIds = collect();
        if ($transportCategory) {
            $transportFeeSetIds = FeeSetItem::where('fee_category_id', $transportCategory->id)
                ->pluck('fee_set_id');
        }

        // Use feeSet_id and fallback to name heuristic for transport fees
        $transportFeesFromBilling = $studentFees->filter(function ($fee) use ($transportFeeSetIds) {
            $isByCategory = $transportFeeSetIds->contains($fee->fee_set_id);
            $isByName = str_contains(strtolower($fee->feeSet->name ?? ''), 'transport fee');
            return $isByCategory || $isByName;
        })->sortBy('due_date');

        $regularFees = $studentFees->reject(function ($fee) use ($transportFeeSetIds) {
            $isByCategory = $transportFeeSetIds->contains($fee->fee_set_id);
            $isByName = str_contains(strtolower($fee->feeSet->name ?? ''), 'transport fee');
            return $isByCategory || $isByName;
        })->sortBy('due_date');

        $totalDue = $studentFees->sum('due_amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalAmount = $studentFees->sum('amount');

        $transports = Transport::with(['academicSession', 'feeCategory'])
            ->where('student_id', $id)
            ->get();

        return view(
            'pages.students.show',
            compact('student', 'regularFees', 'transportFeesFromBilling', 'totalDue', 'totalPaid', 'totalAmount', 'transports')
        );
    }

    public function pdf($id)
    {
        $student = Student::with([
            'academicInformations.academicSession',
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group',
            'fathersProfession',
            'mothersProfession',
            'guardianProfession',
        ])->findOrFail($id);

        $html = view('pages.students.pdf', compact('student'))->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 8, 'margin_bottom' => 8, 'margin_left' => 10, 'margin_right' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-' . $student->student_cid . '.pdf', 'D');
    }

    public function listPdf(Request $request)
    {
        $pdfColumnOptions = $this->pdfColumnOptions();
        $selectedColumns = collect($request->input('pdf_columns', array_keys($pdfColumnOptions)))
            ->filter(fn ($column) => array_key_exists($column, $pdfColumnOptions))
            ->values()
            ->all();

        if (empty($selectedColumns)) {
            $selectedColumns = array_keys($pdfColumnOptions);
        }

        $students = $this->filteredStudentsQuery($request)
            ;

        $students = $this->applyStudentOrdering($students, $request)->get();

        $setting = SchoolSetting::first();
        $filterHeading = [
            'session' => $request->filled('academic_session_id')
                ? optional(AcademicSession::find($request->academic_session_id))->name_en
                : null,
            'class' => $request->filled('school_class_id')
                ? optional(SchoolClass::find($request->school_class_id))->name_en
                : null,
            'section' => $request->filled('section_id')
                ? optional(Section::find($request->section_id))->name_en
                : null,
            'group' => $request->filled('group_id')
                ? optional(Group::find($request->group_id))->name_en
                : null,
        ];

        $html = view('pages.students.list-pdf', compact('students', 'setting', 'selectedColumns', 'pdfColumnOptions', 'filterHeading'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('student-list.pdf', 'D');
    }

    public function edit($id)
    {
        $student = Student::with('academicInformations')->findOrFail($id);

        $data['student'] = $student;
        $data['academicInfo'] = $student->academicInformations->last();

        // Dropdown Data (same as create)
        $data['academicSessions'] = AcademicSession::all();
        $data['classes']          = SchoolClass::all();
        $data['sections']         = Section::all();
        $data['groups']           = Group::all();
        $data['divisions']        = Division::all();
        $data['districts']        = District::all();
        $data['policeStations']   = PoliceStation::all();
        $data['postOffices']      = PostOffice::all();
        $data['feeSets']      = FeeSet::all();
        $data['professions']  = Profession::orderBy('name')->get();

        return view('pages.students.edit', $data);
    }



    /**
     * Toggle student status
     */
    public function toggleStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->status = !$student->status;
        $student->save();
        
        return back()->with('success', 'Student status updated successfully');
    }

    /**
     * Export students (placeholder for Excel export)
     */
    public function export(Request $request)
    {
        // You can implement Excel export here using Laravel Excel package
        // For now, returning a simple response
        return back()->with('info', 'Export functionality will be implemented');
    }

    /**
     * Validation + separation
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            // ================= BASIC INFO =================
            'full_name_bn' => 'nullable|string|max:255',
            'full_name_en' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|integer',
            'birth_certificate_number' => 'nullable|string|max:255',
            'religion' => 'nullable|integer',
            'blood_group' => 'nullable|integer',
            'disable' => 'nullable|boolean',

            // ================= FATHER =================
            'father_name'            => 'nullable|string|max:255',
            'father_nid_number'      => 'nullable|string|max:255',
            'fathers_profession_id'  => 'nullable|integer|exists:professions,id',
            'father_phone'           => 'nullable|string|max:50',
            'father_email'           => 'nullable|email|max:255',

            // ================= MOTHER =================
            'mother_name'            => 'nullable|string|max:255',
            'mother_nid_number'      => 'nullable|string|max:255',
            'mothers_profession_id'  => 'nullable|integer|exists:professions,id',
            'mother_phone'           => 'nullable|string|max:50',
            'mother_email'           => 'nullable|email|max:255',

            // ================= INCOME =================
            'annual_income' => 'nullable|string|max:255',

            // ================= PRESENT ADDRESS =================
            'present_address' => 'nullable|string',
            'present_division_id' => 'nullable|integer|exists:divisions,id',
            'present_district_id' => 'nullable|integer|exists:districts,id',
            'present_police_station_id' => 'nullable|integer|exists:police_stations,id',
            'present_post_office_id' => 'nullable|integer|exists:post_offices,id',

            // ================= PERMANENT ADDRESS =================
            'permanent_address' => 'nullable|string',
            'permanent_division_id' => 'nullable|integer|exists:divisions,id',
            'permanent_district_id' => 'nullable|integer|exists:districts,id',
            'permanent_police_station_id' => 'nullable|integer|exists:police_stations,id',
            'permanent_post_office_id' => 'nullable|integer|exists:post_offices,id',

            // ================= GUARDIAN =================
            'guardian_type'          => 'nullable|integer|in:1,2,3',
            'guardian_name'          => 'nullable|string|max:255',
            'guardian_relation'      => 'nullable|string|max:255',
            'guardian_profession_id' => 'nullable|integer|exists:professions,id',
            'guardian_address'       => 'nullable|string',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_email' => 'nullable|email|max:255',

            // ================= PREVIOUS ACADEMIC =================
            'previous_school' => 'nullable|string|max:255',
            'previous_class_appeared' => 'nullable|string|max:255',
            'tc_number' => 'nullable|string|max:255',

            // ================= ACADEMIC =================
            'academic_session_id' => 'nullable|integer|exists:academic_sessions,id',
            'school_class_id' => 'nullable|integer|exists:school_classes,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'roll' => 'nullable|string|max:50',
        ]);

        // Separate student fields
        $studentFields = collect($validated)->except([
            'academic_session_id',
            'school_class_id',
            'section_id',
            'group_id',
            'roll',
            'image', // Exclude image as it's handled separately
        ])->toArray();

        return [
            'student' => $studentFields,
        ];
    }

    private function generateDueDates($frequency, $months = [], $year = null)
    {
        // dd($frequency, $months, $year);
        $year = $year ?? now()->year;
        $dates = [];

        switch ($frequency) {

            case 'monthly':
                for ($m = 1; $m <= 12; $m++) {
                    $dates[] = Carbon::create($year, $m, 1)->endOfMonth();
                }
                break;

            case 'yearly':
                $dates[] = Carbon::create($year, 12, 31);
                break;

            case 'others':
                if (!empty($months)) {
                    foreach ($months as $m) {
                        $dates[] = Carbon::create($year, $m, 1)->endOfMonth();
                    }
                }
                break;
        }

        return $dates;
    }
}
