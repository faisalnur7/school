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
        
        // Recent Exams
        $data['recent_exams'] = Exam::latest()->take(5)->get();
        
        // Recent Notices
        $data['recent_notices'] = Notice::latest()->take(5)->get();
        
        // Monthly Fee Collection
        $data['monthly_fee_collection'] = $this->getMonthlyFeeCollection();
        
        return view('dashboard', $data);
    }
    
    private function getClasswiseAttendance()
    {
        $today = Carbon::today();
        $classes = SchoolClass::all();
        $result = [];
        
        foreach ($classes as $class) {
            $attendance = Attendance::where('class_id', $class->id)
                ->whereDate('date', $today)
                ->first();
            
            if ($attendance) {
                $present = AttendanceItem::where('attendance_id', $attendance->id)
                    ->where('status', 'present')
                    ->count();
                $total = AttendanceItem::where('attendance_id', $attendance->id)->count();
                $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            } else {
                $percentage = 0;
            }
            
            $result[] = [
                'class' => $class->name_en,
                'percentage' => $percentage
            ];
        }
        
        return $result;
    }
    
    private function getMonthlyAttendanceTrend()
    {
        $days = [];
        $percentages = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('M d');
            
            $attendances = Attendance::whereDate('date', $date)->get();
            $totalPresent = 0;
            $totalStudents = 0;
            
            foreach ($attendances as $attendance) {
                $present = AttendanceItem::where('attendance_id', $attendance->id)
                    ->where('status', 'present')
                    ->count();
                $total = AttendanceItem::where('attendance_id', $attendance->id)->count();
                $totalPresent += $present;
                $totalStudents += $total;
            }
            
            $percentage = $totalStudents > 0 ? round(($totalPresent / $totalStudents) * 100, 2) : 0;
            $percentages[] = $percentage;
        }
        
        return [
            'days' => $days,
            'percentages' => $percentages
        ];
    }
    
    private function getFeeTrend()
    {
        $months = [];
        $amounts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $amount = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $amounts[] = $amount;
        }
        
        return [
            'months' => $months,
            'amounts' => $amounts
        ];
    }
    
    private function getIncomeExpenseTrend()
    {
        $months = [];
        $incomes = [];
        $expenses = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $months[] = $date->format('M');
            
            $income = Income::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $expense = Expense::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $incomes[] = $income;
            $expenses[] = $expense;
        }
        
        return [
            'months' => $months,
            'incomes' => $incomes,
            'expenses' => $expenses
        ];
    }
    
    private function getStudentDistribution()
    {
        $classes = SchoolClass::all();
        $result = [];
        
        foreach ($classes as $class) {
            $count = Student::whereHas('academicInformations', function ($q) use ($class) {
                $q->where('school_class_id', $class->id);
            })->count();
            
            $result[] = [
                'name' => $class->name_en,
                'count' => $count
            ];
        }
        
        return $result;
    }
    
    private function getMonthlyFeeCollection()
    {
        $months = [];
        $collections = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $months[] = $date->format('M');
            
            $amount = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $collections[] = $amount;
        }
        
        return [
            'months' => $months,
            'collections' => $collections
        ];
    }
}
