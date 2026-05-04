<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->boolean('is_absent')->default(false)->after('total');
            $table->string('letter_grade', 5)->nullable()->after('is_absent');
            $table->decimal('gpa', 4, 2)->nullable()->after('letter_grade');
        });
    }

    public function down(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->dropColumn(['is_absent', 'letter_grade', 'gpa']);
        });
    }
};
