<?php
namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['name' => 'Principal',         'employee_type' => 'teacher', 'hierarchy_level' => 1],
            ['name' => 'Asst. Principal',   'employee_type' => 'teacher', 'hierarchy_level' => 2],
            ['name' => 'Assistant Teacher', 'employee_type' => 'teacher', 'hierarchy_level' => 3],
            ['name' => 'Junior Teacher',    'employee_type' => 'teacher', 'hierarchy_level' => 4],
            ['name' => 'Lab Assistant',     'employee_type' => 'staff',   'hierarchy_level' => 1],
            ['name' => 'Office Assistant',  'employee_type' => 'staff',   'hierarchy_level' => 2],
            ['name' => 'Peon',              'employee_type' => 'staff',   'hierarchy_level' => 3],
            ['name' => 'Aya',               'employee_type' => 'staff',   'hierarchy_level' => 4],
        ];

        foreach ($designations as $d) {
            Designation::firstOrCreate(
                ['name' => $d['name'], 'employee_type' => $d['employee_type']],
                array_merge($d, ['status' => 'active'])
            );
        }
    }
}
