<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('employee_type', ['teacher', 'staff']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('designation_id')->constrained('departments')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('employees', 'department')) {
            $legacyDepartments = DB::table('employees')
                ->select('department', 'employee_type')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->get();

            foreach ($legacyDepartments as $legacyDepartment) {
                $existingDepartmentId = DB::table('departments')
                    ->where('name', $legacyDepartment->department)
                    ->value('id');

                $departmentId = $existingDepartmentId ?: DB::table('departments')->insertGetId([
                    'name' => $legacyDepartment->department,
                    'employee_type' => in_array($legacyDepartment->employee_type, ['teacher', 'staff'], true)
                        ? $legacyDepartment->employee_type
                        : 'staff',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('employees')
                    ->where('department', $legacyDepartment->department)
                    ->where('employee_type', $legacyDepartment->employee_type)
                    ->update(['department_id' => $departmentId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }
        });

        Schema::dropIfExists('departments');
    }
};
