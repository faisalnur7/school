<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_academic_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'academic_session_id']);
            $table->index(['academic_session_id', 'employee_id']);
        });

        $teacherIds = DB::table('employees')
            ->where('employee_type', 'teacher')
            ->pluck('id');
        $sessionIds = DB::table('academic_sessions')->pluck('id');
        $now = now();
        $assignments = [];

        foreach ($teacherIds as $employeeId) {
            foreach ($sessionIds as $sessionId) {
                $assignments[] = [
                    'employee_id' => $employeeId,
                    'academic_session_id' => $sessionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($assignments, 500) as $chunk) {
            DB::table('employee_academic_session')->insert($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_academic_session');
    }
};
