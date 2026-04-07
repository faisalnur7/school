<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\DesignationSalaryDefault;
use Illuminate\Http\Request;

class DesignationSalaryDefaultController extends Controller
{
    public function index()
    {
        $designations = Designation::active()->with('salaryDefault')
            ->orderBy('employee_type')->orderBy('hierarchy_level')->get();
        return view('hr.salary.defaults.index', compact('designations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'designation_id'       => 'required|exists:designations,id',
            'basic_salary'         => 'required|numeric|min:0',
            'house_rent'           => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'transport_allowance'  => 'nullable|numeric|min:0',
            'special_allowance'    => 'nullable|numeric|min:0',
            'bonus'                => 'nullable|numeric|min:0',
            'other_deductions'     => 'nullable|numeric|min:0',
        ]);

        DesignationSalaryDefault::updateOrCreate(
            ['designation_id' => $data['designation_id']],
            $data
        );

        return back()->with('success', 'Salary defaults saved.');
    }

    public function show(int $id)
    {
        return response()->json(DesignationSalaryDefault::where('designation_id', $id)->first());
    }
}
