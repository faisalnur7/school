<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeQualification;
use App\Models\AcademicSession;
use App\Models\EducationalQualification;
use App\Models\PaymentInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['designation', 'department'])
                        ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                        ->when($request->search, fn($q) =>
                            $q->where(function ($query) use ($request) {
                                $query->where('employees.name', 'like', "%{$request->search}%")
                                    ->orWhere('employees.employee_id', 'like', "%{$request->search}%");
                            })
                        )
                        ->when($request->employee_type, fn($q) => $q->where('employees.employee_type', $request->employee_type))
                        ->when($request->designation_id, fn($q) => $q->where('employees.designation_id', $request->designation_id))
                        ->when($request->status, fn($q) => $q->where('employees.status', $request->status))
                        ->when($request->missing_salary, fn($q) => $q->doesntHave('salaryStructure'))
                        ->orderBy('designations.hierarchy_level', 'asc')
                        ->orderBy('id', 'asc')
                        ->select('employees.*')
                        ->paginate(20)
                        ->withQueryString();

        $designations = Designation::active()->orderBy('name')->get();
        return view('hr.employees.index', compact('employees', 'designations'));
    }

    public function create()
    {
        $designations = Designation::active()->orderBy('employee_type')->orderBy('hierarchy_level')->get();
        $departments  = Department::orderBy('name')->get();
        $managers     = Employee::active()->with('designation')->get();
        $academicSessions = AcademicSession::orderByDesc('id')->get();
        $activeAcademicSessionId = AcademicSession::where('status', 1)->latest('id')->value('id')
            ?? AcademicSession::latest('id')->value('id');
        $qualificationOptions = EducationalQualification::active()->orderBy('name')->get();
        return view('hr.employees.create', compact('designations', 'departments', 'managers', 'academicSessions', 'activeAcademicSessionId', 'qualificationOptions'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $data = $request->validated();
            $academicSessionIds = $data['academic_session_ids'] ?? [];
            $qualificationRows = $data['qualifications'] ?? [];
            unset($data['academic_session_ids']);
            unset($data['qualifications']);
            $data['user_id']     = $user->id;
            $data['employee_id'] = $this->generateEmployeeId($request->employee_type);
            unset($data['email'], $data['password']);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/employees'), $filename);
                $data['photo'] = 'uploads/employees/' . $filename;
            }

            $employee = Employee::create($data);

            if ($employee->employee_type === 'teacher') {
                $employee->academicSessions()->sync($academicSessionIds);
            }

            $this->syncEmployeeQualifications($employee, $qualificationRows);

            $employee->paymentInformation()->create([
                ...$this->paymentData($request),
            ]);
        });

        return redirect()->route('hr.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user',
            'department',
            'designation',
            'manager',
            'academicSessions',
            'paymentInformation',
            'qualifications.qualification',
        ]);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $designations = Designation::active()->orderBy('employee_type')->orderBy('hierarchy_level')->get();
        $departments  = Department::orderBy('name')->get();
        $managers     = Employee::active()->where('id', '!=', $employee->id)->with('designation')->get();
        $academicSessions = AcademicSession::orderByDesc('id')->get();
        $activeAcademicSessionId = AcademicSession::where('status', 1)->latest('id')->value('id')
            ?? AcademicSession::latest('id')->value('id');
        $employee->load(['academicSessions', 'qualifications.qualification']);
        $assignedQualificationIds = $employee->qualifications->pluck('qualification_id');
        $qualificationOptions = EducationalQualification::query()
            ->where(fn ($query) => $query->where('status', 'active')->orWhereIn('id', $assignedQualificationIds))
            ->orderBy('name')
            ->get();
        return view('hr.employees.edit', compact('employee', 'designations', 'departments', 'managers', 'academicSessions', 'activeAcademicSessionId', 'qualificationOptions'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            $employee->user->update(['name' => $request->name, 'email' => $request->email]);

            $data = $request->validated();
            $academicSessionIds = $data['academic_session_ids'] ?? [];
            $qualificationRows = $data['qualifications'] ?? [];
            unset($data['academic_session_ids']);
            unset($data['qualifications']);
            unset($data['email']);

            if ($request->hasFile('photo')) {
                if ($employee->photo && file_exists(public_path($employee->photo))) {
                    unlink(public_path($employee->photo));
                }
                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/employees'), $filename);
                $data['photo'] = 'uploads/employees/' . $filename;
            } else {
                unset($data['photo']);
            }

            $employee->update($data);

            if ($employee->employee_type === 'teacher') {
                $employee->academicSessions()->sync($academicSessionIds);
            } else {
                $employee->academicSessions()->detach();
            }

            $this->syncEmployeeQualifications($employee, $qualificationRows);

            $employee->paymentInformation()->updateOrCreate(
                ['employee_id' => $employee->id],
                $this->paymentData($request)
            );
        });

        return redirect()->route('hr.employees.show', $employee)->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->payrolls()->exists() || $employee->leaveRequests()->exists()) {
            $employee->update(['status' => 'inactive']);
            return redirect()->route('hr.employees.index')->with('success', 'Employee deactivated (has linked records).');
        }
        $employee->user->delete();
        $employee->delete();
        return redirect()->route('hr.employees.index')->with('success', 'Employee deleted.');
    }

    private function generateEmployeeId(string $type): string
    {
        $prefix = $type === 'teacher' ? 'TCH' : 'STF';
        $year   = now()->year;
        $count  = Employee::where('employee_type', $type)
            ->whereYear('created_at', $year)->count() + 1;
        return "{$prefix}-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    private function paymentData(Request $request): array
    {
        $method = $request->input('payment_method', 'cash');

        return [
            'payment_method' => $method,
            'bank_name' => $method === 'bank_transfer' ? $request->input('bank_name') : null,
            'account_number' => $method === 'bank_transfer' ? $request->input('account_number') : null,
            'mobile_wallet_provider' => $method === 'mobile_wallet' ? $request->input('mobile_wallet_provider') : null,
            'mobile_wallet_number' => $method === 'mobile_wallet' ? $request->input('mobile_wallet_number') : null,
        ];
    }

    private function syncEmployeeQualifications(Employee $employee, array $rows): void
    {
        $existing = $employee->qualifications()->get()->keyBy('id');
        $keptIds = [];

        foreach ($rows as $row) {
            $rowId = (int) ($row['id'] ?? 0);
            if ($rowId && !$existing->has($rowId)) {
                continue;
            }

            $record = $existing->get($rowId) ?? new EmployeeQualification(['employee_id' => $employee->id]);
            $record->fill([
                'qualification_id' => $row['qualification_id'],
                'institution_name' => $row['institution_name'] ?? null,
                'passing_year' => $row['passing_year'] ?? null,
                'result' => $row['result'] ?? null,
            ]);

            if (!empty($row['remove_document']) && empty($row['document'])) {
                $this->deleteQualificationDocument($record);
                $record->document_path = null;
                $record->document_original_name = null;
            }

            if (!empty($row['document'])) {
                $this->deleteQualificationDocument($record);
                $file = $row['document'];
                $directory = public_path('uploads/employees/qualifications');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                $record->document_path = 'uploads/employees/qualifications/' . $filename;
                $record->document_original_name = $file->getClientOriginalName();
            }

            $record->save();
            $keptIds[] = $record->id;
        }

        $employee->qualifications()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(function (EmployeeQualification $record) {
                $this->deleteQualificationDocument($record);
                $record->delete();
            });
    }

    private function deleteQualificationDocument(EmployeeQualification $record): void
    {
        if ($record->document_path && file_exists(public_path($record->document_path))) {
            unlink(public_path($record->document_path));
        }
    }
}
