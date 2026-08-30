<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_amount_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_id')->index();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('old_amount', 12, 2);
            $table->decimal('new_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_amount_histories');
    }
};
