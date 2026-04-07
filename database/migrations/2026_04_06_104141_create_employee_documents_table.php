<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('document_type', ['nid', 'passport', 'certificate', 'contract', 'photo_id', 'other']);
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_documents'); }
};
