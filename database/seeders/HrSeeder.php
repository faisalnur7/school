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
                            ['name' => 'Assistant Teacher',  'employee_type' => 'teacher', 'hierarchy_level' => 2],
                            ['name' => 'Admin Officer',      'employee_type' => 'staff',   'hierarchy_level' => 1],
                            ['name' => 'Account Officer',    'employee_type' => 'staff',   'hierarchy_level' => 2],
                            ['name' => 'SRO',                'employee_type' => 'staff',   'hierarchy_level' => 3],
                            ['name' => 'Computer Operator',  'employee_type' => 'staff',   'hierarchy_level' => 4],
                            ['name' => 'Office Assistant',   'employee_type' => 'staff',   'hierarchy_level' => 5],
                            ['name' => 'Aya',                'employee_type' => 'staff',   'hierarchy_level' => 6],
                            ['name' => 'Security Guard',     'employee_type' => 'staff',   'hierarchy_level' => 7],
                            ['name' => 'Driver',             'employee_type' => 'staff',   'hierarchy_level' => 8],
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
                        ['id'=>'EMP-001','name'=>'Md. Raqib Hossain','desig'=>'Principal','type'=>'teacher','dept'=>'Administration','gender'=>'male','phone'=>'01630100775','dob'=>null,'joining'=>'2024-12-01'],
                        ['id'=>'EMP-002','name'=>'Towhidul Islam','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01839078370','dob'=>null,'joining'=>'2024-09-10'],
                        ['id'=>'EMP-003','name'=>'Yeasmin Akter','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01829433094','dob'=>null,'joining'=>'2024-09-10'],
                        ['id'=>'EMP-004','name'=>'Azma Rashid Ema','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01890495520','dob'=>null,'joining'=>'2024-09-10'],
                        ['id'=>'EMP-005','name'=>'Resmi Chakraborty','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01612393997','dob'=>null,'joining'=>'2024-11-10'],
                        ['id'=>'EMP-006','name'=>'Helal Uddin','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01629565777','dob'=>null,'joining'=>'2024-12-01'],
                        ['id'=>'EMP-007','name'=>'Sanjida Lipi','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01815184142','dob'=>null,'joining'=>'2024-12-01'],
                        ['id'=>'EMP-008','name'=>'Akhi Sultana','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01865221422','dob'=>null,'joining'=>'2024-12-01'],
                        ['id'=>'EMP-009','name'=>'Tinni Das','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01621942382','dob'=>null,'joining'=>'2025-01-01'],
                        ['id'=>'EMP-010','name'=>'Tonnita Chakraborty','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01882371856','dob'=>null,'joining'=>'2025-01-06'],
                        ['id'=>'EMP-011','name'=>'Hafz Md Ismail','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01856859140','dob'=>null,'joining'=>'2025-01-04'],
                        ['id'=>'EMP-012','name'=>'Hafz Nujrul Islam Sayem','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01605774240','dob'=>null,'joining'=>'2025-01-09'],
                        ['id'=>'EMP-013','name'=>'Ayesha Jahan Papri','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01791230698','dob'=>null,'joining'=>'2025-01-15'],
                        ['id'=>'EMP-014','name'=>'Asak Alahe Forhed','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01882053885','dob'=>null,'joining'=>'2025-01-19'],
                        ['id'=>'EMP-015','name'=>'Any Paul','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01840272917','dob'=>null,'joining'=>'2026-01-01'],
                        ['id'=>'EMP-016','name'=>'Md. Atahur Rahman','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01831328579','dob'=>null,'joining'=>'2025-08-17'],
                        ['id'=>'EMP-017','name'=>'Md. Elias','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01858578118','dob'=>null,'joining'=>'2026-01-20'],
                        ['id'=>'EMP-018','name'=>'Orni Chodhury','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01643516814','dob'=>null,'joining'=>'2026-04-01'],
                        ['id'=>'EMP-019','name'=>'Showkatul Islam','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'male','phone'=>'01850314709','dob'=>null,'joining'=>'2026-04-22'],
                        ['id'=>'EMP-020','name'=>'Reba Chodhury','desig'=>'Assistant Teacher','type'=>'teacher','dept'=>'General','gender'=>'female','phone'=>'01897046304','dob'=>null,'joining'=>'2026-04-22'],
                        ['id'=>'EMP-021','name'=>'Md. Hasanul Akbar','desig'=>'Admin Officer','type'=>'staff','dept'=>'Administration','gender'=>'male','phone'=>'01838584458','dob'=>null,'joining'=>'2025-06-01'],
                        ['id'=>'EMP-022','name'=>'Mehedi Hasan','desig'=>'Account Officer','type'=>'staff','dept'=>'Finance','gender'=>'male','phone'=>'01604900109','dob'=>null,'joining'=>'2025-01-19'],
                        ['id'=>'EMP-023','name'=>'Tofaituj Jannat (Shammi)','desig'=>'SRO','type'=>'staff','dept'=>'Administration','gender'=>'female','phone'=>'01846002583','dob'=>null,'joining'=>'2025-01-19'],
                        ['id'=>'EMP-024','name'=>'Md. Arif','desig'=>'Computer Operator','type'=>'staff','dept'=>'IT','gender'=>'male','phone'=>'01893397845','dob'=>null,'joining'=>'2025-08-17'],
                        ['id'=>'EMP-025','name'=>'Khadija Akter','desig'=>'Aya','type'=>'staff','dept'=>'General','gender'=>'female','phone'=>'01585815972','dob'=>null,'joining'=>'2024-12-01'],
                        ['id'=>'EMP-026','name'=>'Sarmin Akter','desig'=>'Aya','type'=>'staff','dept'=>'General','gender'=>'female','phone'=>'01888372864','dob'=>null,'joining'=>'2024-12-15'],
                        ['id'=>'EMP-027','name'=>'Shilpi Acharjee','desig'=>'Office Assistant','type'=>'staff','dept'=>'Administration','gender'=>'female','phone'=>'01621942382','dob'=>null,'joining'=>'2025-01-04'],
                        ['id'=>'EMP-028','name'=>'Baby Das','desig'=>'Office Assistant','type'=>'staff','dept'=>'Administration','gender'=>'female','phone'=>'01864693491','dob'=>null,'joining'=>'2025-01-06'],
                        ['id'=>'EMP-029','name'=>'Jakir Hossain','desig'=>'Security Guard','type'=>'staff','dept'=>'Security','gender'=>'male','phone'=>'01856642027','dob'=>null,'joining'=>'2024-12-10'],
                        ['id'=>'EMP-030','name'=>'Mohammed Hosain','desig'=>'Security Guard','type'=>'staff','dept'=>'Security','gender'=>'male','phone'=>'01831836658','dob'=>null,'joining'=>'2024-12-22'],
                        ['id'=>'EMP-031','name'=>'Md. Rasel','desig'=>'Driver','type'=>'staff','dept'=>'Transport','gender'=>'male','phone'=>'01319258233','dob'=>null,'joining'=>'2025-01-05'],
                        ['id'=>'EMP-032','name'=>'Md. Unas','desig'=>'Driver','type'=>'staff','dept'=>'Transport','gender'=>'male','phone'=>'01848363213','dob'=>null,'joining'=>'2025-01-05'],
                        ['id'=>'EMP-033','name'=>'Md. Abdullah','desig'=>'Driver','type'=>'staff','dept'=>'Transport','gender'=>'male','phone'=>null,'dob'=>null,'joining'=>null],
                    ];

        $empMap = [];
        foreach ($employees as $e) {
            $email = strtolower(str_replace([' ', '.'], ['_', ''], $e['name'])) . '@gmail.com';
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
                    'phone'          => $e['phone'] ?? null,
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
        $methods = ['cash'];
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

        // foreach ($empMap as $data) {
        //     $emp = $data['model'];
        //     $sal = SalaryStructure::where('employee_id', $emp->id)->latest('effective_from')->first();
        //     if (!$sal) continue;

        //     HrPayroll::firstOrCreate(
        //         ['employee_id' => $emp->id, 'payroll_month' => $month, 'payroll_year' => $year],
        //         [
        //             'gross_salary'    => $sal->gross_salary,
        //             'other_deductions'=> $sal->other_deductions,
        //             'net_salary'      => $sal->net_salary,
        //             'payment_method'  => $emp->paymentInformation?->payment_method ?? 'cash',
        //             'status'          => 'pending',
        //             'is_locked'       => false,
        //         ]
        //     );
        // }

        $this->command->info('✅ HR & Payroll seeded:');
        $this->command->info('   Designations      : ' . Designation::count());
        $this->command->info('   Salary Defaults   : ' . DesignationSalaryDefault::count());
        $this->command->info('   Employees         : ' . Employee::count());
        $this->command->info('   Salary Structures : ' . SalaryStructure::count());
        $this->command->info('   Leave Balances    : ' . LeaveBalance::count());
        $this->command->info('   Payroll Records   : ' . HrPayroll::count());
    }
}
