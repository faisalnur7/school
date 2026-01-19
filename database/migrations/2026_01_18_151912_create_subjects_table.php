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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            // Link subject to class
            $table->unsignedBigInteger('school_class_id')->comment('Class ID');

            // Subject names
            $table->string('name_bn')->comment('Subject name in Bengali');
            $table->string('name_en')->comment('Subject name in English');

            $table->boolean('status')->default(true)->comment('Active/Inactive subject');

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('school_class_id')
                  ->references('id')
                  ->on('school_classes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
