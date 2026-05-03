<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->enum('type', ['tutorial', 'term'])->default('tutorial')->after('name');
            $table->foreignId('class_id')->constrained('school_classes')->after('type');
            $table->year('year')->after('class_id');
            $table->date('start_date')->nullable()->after('year');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('status', ['draft', 'published'])->default('draft')->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumns(['name', 'type', 'class_id', 'year', 'start_date', 'end_date', 'status']);
        });
    }
};
