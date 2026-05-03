<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_class_assignments', function (Blueprint $table) {
            $table->id();

            // Subject relationship
            $table->unsignedBigInteger('subject_id')->comment('Subject ID');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            // Class relationship
            $table->unsignedBigInteger('school_class_id')->comment('Class ID');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');

            // Group (Science/Arts/Commerce) - nullable means applies to all groups
            $table->unsignedBigInteger('group_id')->nullable()->comment('Group ID');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            // Student filters
            $table->enum('gender', ['all', 'male', 'female'])->default('all')->comment('Gender filter');
            $table->string('religion')->default('all')->comment('Religion filter');

            // Subject type for this class
            $table->boolean('is_optional')->default(false)->comment('Is optional subject');
            $table->boolean('is_compulsory')->default(true)->comment('Is compulsory subject');

            // For exclusive groups like Science: Biology OR Higher Math
            $table->string('exclusive_group_key')->nullable()->comment('Exclusive group key');

            // Status
            $table->boolean('is_active')->default(true)->comment('Active/Inactive');

            // Timestamps
            $table->timestamps();

            // Unique constraint: prevent duplicate assignment
            $table->unique(['subject_id', 'school_class_id', 'group_id', 'gender', 'religion'], 'subject_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_class_assignments');
    }
};