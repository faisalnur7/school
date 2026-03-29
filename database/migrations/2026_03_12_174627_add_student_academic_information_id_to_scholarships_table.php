<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->unsignedBigInteger('student_academic_information_id')->nullable()->after('student_id');
            
            $table->foreign('student_academic_information_id')->references('id')->on('student_academic_information')->onDelete('set null');
            $table->index('student_academic_information_id');
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropForeign(['student_academic_information_id']);
            $table->dropColumn('student_academic_information_id');
        });
    }
};
