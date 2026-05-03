<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Administrative leadership and office operations.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Accounts', 'code' => 'ACCTS', 'description' => 'Finance, billing, and accounting operations.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Academic Affairs', 'code' => 'ACADEMIC', 'description' => 'Academic coordination, curriculum, and classroom oversight.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT systems, networking, and technical support.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Library', 'code' => 'LIB', 'description' => 'Library services, books, and reading facilities.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Science Laboratory', 'code' => 'LAB', 'description' => 'Science lab resources, preparation, and supervision.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Transport', 'code' => 'TRANS', 'description' => 'School vehicle operations and transport support.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
            ['name' => 'Maintenance', 'code' => 'MNT', 'description' => 'Building upkeep, electrical work, and general maintenance.', 'employee_type' => 'staff', 'status' => 'active', 'is_active' => true],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }

        $this->command?->info('Departments seeded: ' . count($departments));
    }
}
