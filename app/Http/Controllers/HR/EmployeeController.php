<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['designation', 'department'])
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('employee_id', 'like', "%{$request->search}%")
            )
            ->when($request->employee_type, fn($q) => $q->where('employee_type', $request->employee_type))
            ->when($request->designation_id, fn($q) => $q->where('designation_id', $request->designation_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->missing_salary, fn($q) => $q->doesntHave('salaryStructure'))
            ->latest()->paginate(20)->withQueryString();

        $designations = Designation::active()->orderBy('name')->get();
        return view('hr.employees.index', compact('employees', 'designations'));
    }

    public function create()
    {
        $designations = Designation::active()->orderBy('employee_type')->orderBy('hierarchy_level')->get();
        $departments  = Department::orderBy('name')->get();
        $managers     = Employee::active()->with('designation')->get();
        return view('hr.employees.create', compact('designations', 'departments', 'managers'));
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
            $data['user_id']     = $user->id;
            $data['employee_id'] = $this->generateEmployeeId($request->employee_type);
            unset($data['email'], $data['password']);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/employees'), $filename);
                $data['photo'] = 'uploads/employees/' . $filename;
            }

            Employee::create($data);
        });

        return redirect()->route('hr.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation', 'manager', 'salaryStructures', 'leaveBalances', 'documents', 'paymentInformation']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $designations = Designation::active()->orderBy('employee_type')->orderBy('hierarchy_level')->get();
        $departments  = Department::orderBy('name')->get();
        $managers     = Employee::active()->where('id', '!=', $employee->id)->with('designation')->get();
        return view('hr.employees.edit', compact('employee', 'designations', 'departments', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            $employee->user->update(['name' => $request->name, 'email' => $request->email]);

            $data = $request->validated();
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
}
