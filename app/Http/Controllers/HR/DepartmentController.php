<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::query()
            ->when($request->employee_type, fn($q) => $q->where('employee_type', $request->employee_type))
            ->withCount('employees')
            ->orderBy('employee_type')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('hr.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('hr.departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        Department::create($request->validated());

        return redirect()->route('hr.departments.index')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('hr.departments.edit', compact('department'));
    }

    public function update(StoreDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());

        return redirect()->route('hr.departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete — employees are linked to this department.');
        }

        $department->delete();

        return redirect()->route('hr.departments.index')->with('success', 'Department deleted.');
    }
}