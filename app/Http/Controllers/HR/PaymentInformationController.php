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
        $method = $request->payment_method === 'bank' ? 'bank_transfer' : $request->payment_method;
        $rules  = ['payment_method' => 'required|in:bank_transfer,cash,mobile_wallet'];

        if ($method === 'bank_transfer') {
            $rules['bank_name']      = 'required|string';
            $rules['account_number'] = 'required|string';
        } elseif ($method === 'mobile_wallet') {
            $rules['mobile_wallet_number'] = 'required|string';
        }

        $data = $request->validate($rules);
        $data['bank_name'] = $method === 'bank_transfer' ? ($data['bank_name'] ?? null) : null;
        $data['account_number'] = $method === 'bank_transfer' ? ($data['account_number'] ?? null) : null;
        $data['mobile_wallet_provider'] = $method === 'mobile_wallet' ? ($data['mobile_wallet_provider'] ?? null) : null;
        $data['mobile_wallet_number'] = $method === 'mobile_wallet' ? ($data['mobile_wallet_number'] ?? null) : null;
        $data['employee_id'] = $employeeId;

        PaymentInformation::updateOrCreate(['employee_id' => $employeeId], $data);
        return redirect()->route('hr.employees.show', $employeeId)->with('success', 'Payment info saved.');
    }
}
