<?php

namespace Database\Seeders;

use App\Models\Designation;
use App\Models\DesignationSalaryDefault;
use App\Models\Employee;
use App\Models\HrPayroll;
use App\Models\LeaveBalance;
use App\Models\PaymentInformation;
use App\Models\SalaryStructure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HrSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Designations ───────────────────────────────────────────────
        $designations = [
            ['name' => 'Principal',          'employee_type' => 'teacher', 'hierarchy_level' => 1],
            ['name' => 'Asst. Principal',    'employee_type' => 'teacher', 'hierarchy_level' => 2],
            ['name' => 'Senior Teacher',     'employee_type' => 'teacher', 'hierarchy_level' => 3],
            ['name' => 'Assistant Teacher',  'employee_type' => 'teacher', 'hierarchy_level' => 4],
            ['name' => 'Junior Teacher',     'employee_type' => 'teacher', 'hierarchy_level' => 5],
            ['name' => 'Office Manager',     'employee_type' => 'staff',   'hierarchy_level' => 1],
            ['name' => 'Accountant',         'employee_type' => 'staff',   'hierarchy_level' => 2],
            ['name' => 'Lab Assistant',      'employee_type' => 'staff',   'hierarchy_level' => 3],
            ['name' => 'Office Assistant',   'employee_type' => 'staff',   'hierarchy_level' => 4],
            ['name' => 'Peon',               'employee_type' => 'staff',   'hierarchy_level' => 5],
        ];

        $desigMap = [];
        foreach ($designations as $d) {
            $rec = Designation::firstOrCreate(
                ['name' => $d['name'], 'employee_type' => $d['employee_type']],
                array_merge($d, ['status' => 'active'])
            );
            $desigMap[$d['name']] = $rec->id;
        }

        // ── 2. Designation Salary Defaults ────────────────────────────────
        $salaryDefaults = [
            'Principal'         => ['basic' => 30000, 'house' => 10000, 'medical' => 2000, 'transport' => 2000, 'special' => 3000, 'bonus' => 2000, 'deductions' => 2000],
            'Asst. Principal'   => ['basic' => 25000, 'house' => 8000,  'medical' => 1500, 'transport' => 1500, 'special' => 2000, 'bonus' => 1500, 'deductions' => 1500],
            'Senior Teacher'    => ['basic' => 20000, 'house' => 6000,  'medical' => 1200, 'transport' => 1200, 'special' => 1500, 'bonus' => 1000, 'deductions' => 1000],
            'Assistant Teacher' => ['basic' => 15000, 'house' => 5000,  'medical' => 1000, 'transport' => 1000, 'special' => 1000, 'bonus' => 500,  'deductions' => 500],
            'Junior Teacher'    => ['basic' => 12000, 'house' => 4000,  'medical' => 800,  'transport' => 800,  'special' => 500,  'bonus' => 0,    'deductions' => 300],
            'Office Manager'    => ['basic' => 18000, 'house' => 5000,  'medical' => 1000, 'transport' => 1000, 'special' => 1500, 'bonus' => 1000, 'deductions' => 800],
            'Accountant'        => ['basic' => 15000, 'house' => 4000,  'medical' => 800,  'transport' => 800,  'special' => 1000, 'bonus' => 500,  'deductions' => 500],
            'Lab Assistant'     => ['basic' => 10000, 'house' => 3000,  'medical' => 600,  'transport' => 600,  'special' => 500,  'bonus' => 0,    'deductions' => 300],
            'Office Assistant'  => ['basic' => 8000,  'house' => 2500,  'medical' => 500,  'transport' => 500,  'special' => 0,    'bonus' => 0,    'deductions' => 200],
            'Peon'              => ['basic' => 6000,  'house' => 2000,  'medical' => 400,  'transport' => 400,  'special' => 0,    'bonus' => 0,    'deductions' => 100],
        ];

        foreach ($salaryDefaults as $name => $s) {
            if (!isset($desigMap[$name])) continue;
            DesignationSalaryDefault::updateOrCreate(
                ['designation_id' => $desigMap[$name]],
                [
                    'basic_salary'        => $s['basic'],
                    'house_rent'          => $s['house'],
                    'medical_allowance'   => $s['medical'],
                    'transport_allowance' => $s['transport'],
                    'special_allowance'   => $s['special'],
                    'bonus'               => $s['bonus'],
                    'other_deductions'    => $s['deductions'],
                ]
            );
        }

        // ── 3. Employees ──────────────────────────────────────────────────
        $employees = [
            ['id' => 'EMP-001', 'name' => 'Dr. Rafiqul Islam',    'desig' => 'Principal',         'type' => 'teacher', 'dept' => 'Administration', 'gender' => 'male',   'dob' => '1970-05-15', 'joining' => '2010-01-01'],
            ['id' => 'EMP-002', 'name' => 'Nasrin Akter',         'desig' => 'Asst. Principal',   'type' => 'teacher', 'dept' => 'Administration', 'gender' => 'female', 'dob' => '1975-08-20', 'joining' => '2012-03-01'],
            ['id' => 'EMP-003', 'name' => 'Md. Kamal Hossain',    'desig' => 'Senior Teacher',    'type' => 'teacher', 'dept' => 'Science',        'gender' => 'male',   'dob' => '1978-11-10', 'joining' => '2013-06-01'],
            ['id' => 'EMP-004', 'name' => 'Fatema Begum',         'desig' => 'Senior Teacher',    'type' => 'teacher', 'dept' => 'Mathematics',    'gender' => 'female', 'dob' => '1980-03-25', 'joining' => '2014-01-01'],
            ['id' => 'EMP-005', 'name' => 'Md. Jahangir Alam',    'desig' => 'Assistant Teacher', 'type' => 'teacher', 'dept' => 'English',        'gender' => 'male',   'dob' => '1985-07-12', 'joining' => '2015-07-01'],
            ['id' => 'EMP-006', 'name' => 'Roksana Khanam',       'desig' => 'Assistant Teacher', 'type' => 'teacher', 'dept' => 'Bengali',        'gender' => 'female', 'dob' => '1987-09-18', 'joining' => '2016-01-01'],
            ['id' => 'EMP-007', 'name' => 'Md. Shahinur Rahman',  'desig' => 'Junior Teacher',    'type' => 'teacher', 'dept' => 'Social Science', 'gender' => 'male',   'dob' => '1990-02-28', 'joining' => '2018-01-01'],
            ['id' => 'EMP-008', 'name' => 'Sumaiya Islam',        'desig' => 'Junior Teacher',    'type' => 'teacher', 'dept' => 'Religion',       'gender' => 'female', 'dob' => '1992-06-14', 'joining' => '2019-06-01'],
            ['id' => 'EMP-009', 'name' => 'Md. Rezaul Karim',     'desig' => 'Office Manager',    'type' => 'staff',   'dept' => 'Administration', 'gender' => 'male',   'dob' => '1975-12-05', 'joining' => '2011-01-01'],
            ['id' => 'EMP-010', 'name' => 'Taslima Akter',        'desig' => 'Accountant',        'type' => 'staff',   'dept' => 'Finance',        'gender' => 'female', 'dob' => '1983-04-22', 'joining' => '2014-07-01'],
            ['id' => 'EMP-011', 'name' => 'Md. Abul Kalam',       'desig' => 'Lab Assistant',     'type' => 'staff',   'dept' => 'Science',        'gender' => 'male',   'dob' => '1988-10-30', 'joining' => '2016-06-01'],
            ['id' => 'EMP-012', 'name' => 'Shirin Akter',         'desig' => 'Office Assistant',  'type' => 'staff',   'dept' => 'Administration', 'gender' => 'female', 'dob' => '1993-01-17', 'joining' => '2020-01-01'],
            ['id' => 'EMP-013', 'name' => 'Md. Rahim Mia',        'desig' => 'Peon',              'type' => 'staff',   'dept' => 'General',        'gender' => 'male',   'dob' => '1985-08-08', 'joining' => '2015-01-01'],
        ];

        $empMap = [];
        foreach ($employees as $e) {
            $email = strtolower(str_replace([' ', '.'], ['_', ''], $e['name'])) . '@school.edu';
            $user  = User::firstOrCreate(
                ['email' => $email],
                ['name' => $e['name'], 'password' => Hash::make('password')]
            );
            $emp = Employee::updateOrCreate(
                ['employee_id' => $e['id']],
                [
                    'user_id'        => $user->id,
                    'name'           => $e['name'],
                    'employee_type'  => $e['type'],
                    'designation_id' => $desigMap[$e['desig']] ?? null,
                    'department'     => $e['dept'],
                    'gender'         => $e['gender'],
                    'dob'            => $e['dob'],
                    'joining_date'   => $e['joining'],
                    'status'         => 'active',
                    'phone'          => '017' . rand(10000000, 99999999),
                ]
            );
            $empMap[$e['id']] = ['model' => $emp, 'desig' => $e['desig']];
        }

        // ── 4. Salary Structures ──────────────────────────────────────────
        foreach ($empMap as $eid => $data) {
            $emp   = $data['model'];
            $desig = $data['desig'];
            $s     = $salaryDefaults[$desig] ?? null;
            if (!$s) continue;

            SalaryStructure::firstOrCreate(
                ['employee_id' => $emp->id, 'effective_from' => '2024-01-01'],
                [
                    'designation_id'      => $emp->designation_id,
                    'basic_salary'        => $s['basic'],
                    'house_rent'          => $s['house'],
                    'medical_allowance'   => $s['medical'],
                    'transport_allowance' => $s['transport'],
                    'special_allowance'   => $s['special'],
                    'bonus'               => $s['bonus'],
                    'other_deductions'    => $s['deductions'],
                ]
            );
        }

        // ── 5. Payment Information ────────────────────────────────────────
        $methods = ['bank', 'cash', 'mobile_wallet'];
        foreach ($empMap as $data) {
            $emp    = $data['model'];
            $method = $methods[array_rand($methods)];
            PaymentInformation::firstOrCreate(
                ['employee_id' => $emp->id],
                [
                    'payment_method'          => $method,
                    'bank_name'               => $method === 'bank_transfer' ? 'Dutch-Bangla Bank' : null,
                    'account_number'          => $method === 'bank_transfer' ? '10' . rand(1000000000, 9999999999) : null,
                    'mobile_wallet_provider'  => $method === 'mobile_banking' ? 'bKash' : null,
                    'mobile_wallet_number'    => $method === 'mobile_banking' ? '017' . rand(10000000, 99999999) : null,
                ]
            );
        }

        // ── 6. Leave Balances ─────────────────────────────────────────────
        $leaveTypes = [
            'casual'    => 10,
            'sick'      => 14,
            'annual'    => 20,
            'maternity' => 90,
        ];

        foreach ($empMap as $data) {
            $emp = $data['model'];
            foreach ($leaveTypes as $type => $total) {
                if ($type === 'maternity' && $emp->gender !== 'female') continue;
                $used = rand(0, (int)($total * 0.4));
                LeaveBalance::updateOrCreate(
                    ['employee_id' => $emp->id, 'leave_type' => $type],
                    [
                        'total_leave'     => $total,
                        'used_leave'      => $used,
                        'remaining_leave' => $total - $used,
                    ]
                );
            }
        }

        // ── 7. Payroll (current month) ────────────────────────────────────
        $month = now()->month;
        $year  = now()->year;

        foreach ($empMap as $data) {
            $emp = $data['model'];
            $sal = SalaryStructure::where('employee_id', $emp->id)->latest('effective_from')->first();
            if (!$sal) continue;

            HrPayroll::firstOrCreate(
                ['employee_id' => $emp->id, 'payroll_month' => $month, 'payroll_year' => $year],
                [
                    'gross_salary'    => $sal->gross_salary,
                    'other_deductions'=> $sal->other_deductions,
                    'net_salary'      => $sal->net_salary,
                    'payment_method'  => $emp->paymentInformation?->payment_method ?? 'cash',
                    'status'          => 'pending',
                    'is_locked'       => false,
                ]
            );
        }

        $this->command->info('✅ HR & Payroll seeded:');
        $this->command->info('   Designations      : ' . Designation::count());
        $this->command->info('   Salary Defaults   : ' . DesignationSalaryDefault::count());
        $this->command->info('   Employees         : ' . Employee::count());
        $this->command->info('   Salary Structures : ' . SalaryStructure::count());
        $this->command->info('   Leave Balances    : ' . LeaveBalance::count());
        $this->command->info('   Payroll Records   : ' . HrPayroll::count());
    }
}
