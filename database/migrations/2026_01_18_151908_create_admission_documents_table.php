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
        Schema::create('admission_documents', function (Blueprint $table) {
            $table->id();

            // Link to the admission application
            $table->unsignedBigInteger('admission_application_id');

            // Document info
            $table->string('document_type'); // e.g., birth_certificate, photo, transcript
            $table->string('file_path');     // storage path
            $table->string('original_name'); // original uploaded filename
            $table->string('file_type')->nullable(); // mime type
            $table->unsignedBigInteger('uploaded_by')->nullable(); // user/admin who uploaded

            $table->timestamp('uploaded_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Indexes & foreign key
            $table->index('admission_application_id');
            $table->foreign('admission_application_id')
                ->references('id')
                ->on('admission_applications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_documents');
    }
};
