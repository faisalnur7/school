<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PaymentInformation;
use Illuminate\Http\Request;

class PaymentInformationController extends Controller
{
    public function show(int $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $info     = $employee->paymentInformation;
        return view('hr.employees.partials.payment-info', compact('employee', 'info'));
    }

    public function createOrEdit(int $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $info     = $employee->paymentInformation;
        return view('hr.employees.partials.payment-form', compact('employee', 'info'));
    }

    public function store(Request $request, int $employeeId)
    {
        $method = $request->payment_method;
        $rules  = ['payment_method' => 'required|in:bank,cash,mobile_wallet'];

        if ($method === 'bank') {
            $rules['bank_name']      = 'required|string';
            $rules['account_number'] = 'required|string';
        } elseif ($method === 'mobile_wallet') {
            $rules['mobile_wallet_number'] = 'required|string';
        }

        $data = $request->validate($rules);
        $data['employee_id'] = $employeeId;

        PaymentInformation::updateOrCreate(['employee_id' => $employeeId], $data);
        return redirect()->route('hr.employees.show', $employeeId)->with('success', 'Payment info saved.');
    }
}
