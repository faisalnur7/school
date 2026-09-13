<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The original routine table was created before employees existed and
        // incorrectly pointed teacher_id at the legacy teachers table.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('class_routines', function (Blueprint $table) {
            $table->dropForeign('class_routines_teacher_id_foreign');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('class_routines', function (Blueprint $table) {
            $table->dropForeign('class_routines_teacher_id_foreign');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->nullOnDelete();
        });
    }
};
