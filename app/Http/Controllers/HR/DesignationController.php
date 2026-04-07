<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $designations = Designation::query()
            ->when($request->employee_type, fn($q) => $q->where('employee_type', $request->employee_type))
            ->withCount('employees')
            ->orderBy('employee_type')->orderBy('hierarchy_level')
            ->paginate(20)->withQueryString();

        return view('hr.designations.index', compact('designations'));
    }

    public function create() { return view('hr.designations.create'); }

    public function store(StoreDesignationRequest $request)
    {
        Designation::create($request->validated());
        return redirect()->route('hr.designations.index')->with('success', 'Designation created.');
    }

    public function edit(Designation $designation)
    {
        return view('hr.designations.edit', compact('designation'));
    }

    public function update(StoreDesignationRequest $request, Designation $designation)
    {
        $designation->update($request->validated());
        return redirect()->route('hr.designations.index')->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->employees()->exists()) {
            return back()->with('error', 'Cannot delete — employees are linked to this designation.');
        }
        $designation->delete();
        return redirect()->route('hr.designations.index')->with('success', 'Designation deleted.');
    }

    public function toggleStatus(Designation $designation)
    {
        $designation->update(['status' => $designation->status === 'active' ? 'inactive' : 'active']);
        if (request()->wantsJson()) return response()->json(['status' => $designation->status]);
        return back();
    }
}
