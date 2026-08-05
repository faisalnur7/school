<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\Payment;
use App\Models\Fee;
use App\Models\Exam;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Income;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Asset;
use App\Models\Notice;
use App\Models\Division;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        $data['title'] = $data['page_title'] = "Dashboard";
        
        // Quick Stats
        $data['total_students'] = Student::count();
        $data['total_teachers'] = Employee::where('employee_type','teacher')->count();
        $data['total_staff'] = Employee::where('employee_type','staff')->count();
        $data['total_classes'] = SchoolClass::count();
        
        // Today's Attendance
        $today = Carbon::today();
        $todayAttendance = Attendance::whereDate('date', $today)->first();
        $data['today_present'] = $todayAttendance ? AttendanceItem::where('attendance_id', $todayAttendance->id)->where('status', 'present')->count() : 0;
        $data['today_absent'] = $todayAttendance ? AttendanceItem::where('attendance_id', $todayAttendance->id)->where('status', 'absent')->count() : 0;
        $data['today_leave'] = $todayAttendance ? AttendanceItem::where('attendance_id', $todayAttendance->id)->where('status', 'leave')->count() : 0;
        $data['attendance_percentage'] = $data['total_students'] > 0 ? round(($data['today_present'] / $data['total_students']) * 100, 2) : 0;
        
        // Fee Statistics
        $data['total_fees_due'] = Fee::where('is_active', true)->sum('amount');
        $data['total_fees_paid'] = Payment::sum('amount');
        $data['total_fees_pending'] = $data['total_fees_due'] - $data['total_fees_paid'];
        $data['fee_collection_rate'] = $data['total_fees_due'] > 0 ? round(($data['total_fees_paid'] / $data['total_fees_due']) * 100, 2) : 0;
        
        // Financial Overview
        $data['total_income'] = Income::sum('amount');
        $data['total_expense'] = Expense::sum('amount');
        $data['net_balance'] = $data['total_income'] - $data['total_expense'];
        
        // Assets
        $data['total_assets'] = Asset::count();
        
        // Classwise Attendance (Last 7 days)
        $data['classwise_attendance'] = $this->getClasswiseAttendance();
        
        // Monthly Attendance Trend
        $data['monthly_attendance'] = $this->getMonthlyAttendanceTrend();
        
        // Fee Collection Trend
        $data['fee_trend'] = $this->getFeeTrend();
        
        // Income vs Expense
        $data['income_expense'] = $this->getIncomeExpenseTrend();
        
        // Student Distribution by Class
        $data['student_distribution'] = $this->getStudentDistribution();

        // Student Distribution by Division
        $data['divisionwise_students'] = $this->getDivisionwiseStudentDistribution();

        // Recent Exams
        $data['recent_exams'] = Exam::latest()->take(5)->get();
        
        // Recent Notices
        $data['recent_notices'] = Notice::published()->latest('published_at')->latest()->take(5)->get();
        
        // Monthly Fee Collection
        $data['monthly_fee_collection'] = $this->getMonthlyFeeCollection();
        
        return view('dashboard', $data);
    }
    
    private function getClasswiseAttendance()
    {
        $today = Carbon::today();
        $classes = SchoolClass::all(['id', 'name_en']);

        $itemStats = AttendanceItem::query()
            ->join('attendances', 'attendance_items.attendance_id', '=', 'attendances.id')
            ->whereDate('attendances.date', $today)
            ->selectRaw('attendances.class_id as class_id, attendance_items.status as status, COUNT(*) as total')
            ->groupBy('attendances.class_id', 'attendance_items.status')
            ->get()
            ->groupBy('class_id')
            ->map(fn ($rows) => $rows->pluck('total', 'status'));

        return $classes->map(function ($class) use ($itemStats) {
            $stats = $itemStats->get($class->id, collect());
            $present = (int) ($stats->get('present') ?? 0);
            $total = (int) ($stats->sum());
            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            return [
                'class' => $class->name_en,
                'percentage' => $percentage,
            ];
        })->values()->all();
    }

    private function getMonthlyAttendanceTrend()
    {
        $start = Carbon::today()->subDays(6)->startOfDay();
        $end = Carbon::today()->endOfDay();
        $days = [];
        $percentages = [];

        $stats = AttendanceItem::query()
            ->join('attendances', 'attendance_items.attendance_id', '=', 'attendances.id')
            ->whereBetween('attendances.date', [$start, $end])
            ->selectRaw('DATE(attendances.date) as day, attendance_items.status as status, COUNT(*) as total')
            ->groupBy('day', 'attendance_items.status')
            ->get()
            ->groupBy('day')
            ->map(fn ($rows) => $rows->pluck('total', 'status'));

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('M d');

            $dayStats = $stats->get($date->toDateString(), collect());
            $present = (int) ($dayStats->get('present') ?? 0);
            $total = (int) ($dayStats->sum());

            $percentages[] = $total > 0 ? round(($present / $total) * 100, 2) : 0;
        }

        return [
            'days' => $days,
            'percentages' => $percentages
        ];
    }
    
    private function getFeeTrend()
    {
        $start = Carbon::today()->subMonthsNoOverflow(5)->startOfMonth();
        $end = Carbon::today()->endOfMonth();
        $months = [];
        $amounts = [];

        $payments = Payment::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subMonthsNoOverflow($i);
            $key = $date->format('Y-m');
            $months[] = $date->format('M Y');
            $amounts[] = (float) ($payments[$key] ?? 0);
        }

        return [
            'months' => $months,
            'amounts' => $amounts
        ];
    }
    
    private function getIncomeExpenseTrend()
    {
        $start = Carbon::today()->subMonthsNoOverflow(5)->startOfMonth();
        $end = Carbon::today()->endOfMonth();
        $months = [];
        $incomes = [];
        $expenses = [];

        $incomeByMonth = Income::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $expenseByMonth = Expense::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subMonthsNoOverflow($i);
            $key = $date->format('Y-m');
            $months[] = $date->format('M');
            $incomes[] = (float) ($incomeByMonth[$key] ?? 0);
            $expenses[] = (float) ($expenseByMonth[$key] ?? 0);
        }

        return [
            'months' => $months,
            'incomes' => $incomes,
            'expenses' => $expenses
        ];
    }
    
    private function getStudentDistribution()
    {
        $classes = SchoolClass::all(['id', 'name_en']);
        $counts = Student::query()
            ->join('student_academic_information', 'students.id', '=', 'student_academic_information.student_id')
            ->selectRaw('student_academic_information.school_class_id as class_id, COUNT(DISTINCT students.id) as total')
            ->groupBy('student_academic_information.school_class_id')
            ->pluck('total', 'class_id');

        return $classes->map(function ($class) use ($counts) {
            return [
                'name' => $class->name_en,
                'count' => (int) ($counts[$class->id] ?? 0),
            ];
        })->values()->all();
    }

    private function getDivisionwiseStudentDistribution()
    {
        $divisions = Division::query()->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $counts = Student::query()
            ->whereNotNull('present_division_id')
            ->selectRaw('present_division_id as division_id, COUNT(*) as total')
            ->groupBy('present_division_id')
            ->pluck('total', 'division_id');

        $totalStudents = (int) $counts->sum();

        return $divisions->map(function ($division) use ($counts, $totalStudents) {
            $count = (int) ($counts[$division->id] ?? 0);

            return [
                'name' => $division->name,
                'count' => $count,
                'percentage' => $totalStudents > 0 ? round(($count / $totalStudents) * 100, 2) : 0,
            ];
        })->filter(fn ($division) => $division['count'] > 0)->values()->all();
    }

    private function getMonthlyFeeCollection()
    {
        $start = Carbon::today()->subMonthsNoOverflow(11)->startOfMonth();
        $end = Carbon::today()->endOfMonth();
        $months = [];
        $collections = [];

        $payments = Payment::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->subMonthsNoOverflow($i);
            $key = $date->format('Y-m');
            $months[] = $date->format('M');
            $collections[] = (float) ($payments[$key] ?? 0);
        }

        return [
            'months' => $months,
            'collections' => $collections
        ];
    }
}
