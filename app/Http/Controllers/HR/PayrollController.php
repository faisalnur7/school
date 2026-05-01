<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrPayroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $service) {}

    public function index()
    {
        $months = HrPayroll::selectRaw('payroll_month, payroll_year, COUNT(*) as count, SUM(gross_salary) as total_gross, SUM(net_salary) as total_net, SUM(status="paid") as paid_count')
            ->groupBy('payroll_year', 'payroll_month')
            ->orderByDesc('payroll_year')->orderByDesc('payroll_month')
            ->get();
        return view('hr.payroll.index', compact('months'));
    }

    public function preview(Request $request)
    {
        $request->validate(['month' => 'required|integer|min:1|max:12', 'year' => 'required|integer|min:2000']);
        $rows    = $this->service->preview($request->month, $request->year);
        $month   = $request->month;
        $year    = $request->year;
        return view('hr.payroll.preview', compact('rows', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $request->validate(['month' => 'required|integer|min:1|max:12', 'year' => 'required|integer|min:2000']);
        $result = $this->service->generate($request->month, $request->year);
        return redirect()->route('hr.payroll.show', [$request->month, $request->year])
            ->with('success', "Generated {$result['created']} payrolls. Skipped: {$result['skipped']}.");
    }

    public function show($month, $year)
    {
        $month = max(1, min(12, (int) $month));
        $year  = (int) $year;
        if ($year < 2000) {
            $year = (int) now()->year;
        }

        $payrolls = HrPayroll::forMonth($month, $year)->with('employee.designation')->get();
        $summary  = $this->service->getSummary($month, $year);
        return view('hr.payroll.show', compact('payrolls', 'month', 'year', 'summary'));
    }

    public function markPaid(int $id)
    {
        $this->service->markPaid($id);
        return back()->with('success', 'Marked as paid.');
    }

    public function lock(Request $request)
    {
        $request->validate(['month' => 'required|integer', 'year' => 'required|integer']);
        $count = $this->service->lock($request->month, $request->year);
        return back()->with('success', "{$count} payrolls locked.");
    }

    public function slip(int $id)
    {
        $payroll = HrPayroll::with(['employee.designation', 'employee.salaryStructure'])->findOrFail($id);
        return view('hr.payroll.slip', compact('payroll'));
    }
}
