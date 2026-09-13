<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_routines', function (Blueprint $table) {
            $table->foreignId('time_schedule_id')->nullable()->after('classroom_id')->constrained('class_schedules')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('class_routines', function (Blueprint $table) {
            $table->dropForeign(['time_schedule_id']);
            $table->dropColumn('time_schedule_id');
        });
    }
};
