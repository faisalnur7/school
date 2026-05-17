<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('tutorial_marks', 5, 2)
                ->default(0)
                ->after('viva_marks')
                ->comment('Tutorial exam full marks');
        });

        Schema::table('exam_marks', function (Blueprint $table) {
            $table->decimal('tutorial_marks', 5, 2)
                ->nullable()
                ->after('viva_marks');
        });
    }

    public function down(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->dropColumn('tutorial_marks');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('tutorial_marks');
        });
    }
};

