<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryStructureRequest;
use App\Models\DesignationSalaryDefault;
use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    public function index()
    {
        $structures = SalaryStructure::with(['employee.designation'])
            ->orderBy('designation_id')->paginate(20);
        return view('hr.salary.index', compact('structures'));
    }

    public function create(Request $request)
    {
        $employee = Employee::with('designation')->findOrFail($request->employee_id ?? abort(400, 'employee_id required'));
        $default  = DesignationSalaryDefault::where('designation_id', $employee->designation_id)->first();
        return view('hr.salary.create-edit', compact('employee', 'default'));
    }

    public function store(StoreSalaryStructureRequest $request)
    {
        SalaryStructure::create($request->validated());
        return redirect()->route('hr.salary-structures.index')->with('success', 'Salary structure saved.');
    }

    public function edit(SalaryStructure $salaryStructure)
    {
        $salaryStructure->load('employee.designation');
        $employee = $salaryStructure->employee;
        $default  = null;
        return view('hr.salary.create-edit', compact('salaryStructure', 'employee', 'default'));
    }

    public function update(StoreSalaryStructureRequest $request, SalaryStructure $salaryStructure)
    {
        $salaryStructure->update($request->validated());
        return redirect()->route('hr.salary-structures.index')->with('success', 'Salary structure updated.');
    }

    public function destroy(SalaryStructure $salaryStructure)
    {
        $salaryStructure->delete();
        return redirect()->route('hr.salary-structures.index')->with('success', 'Salary structure deleted.');
    }

    public function loadDefaults(int $designationId)
    {
        $default = DesignationSalaryDefault::where('designation_id', $designationId)->first();
        return response()->json($default);
    }
}
