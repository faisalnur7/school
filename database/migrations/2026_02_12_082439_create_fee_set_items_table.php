<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('fee_set_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('fee_set_id');
            $table->unsignedBigInteger('fee_category_id');

            $table->decimal('amount', 10, 2);

            $table->timestamps();

            // Foreign Keys
            $table->foreign('fee_set_id')
                  ->references('id')
                  ->on('fee_sets')
                  ->onDelete('cascade');

            $table->foreign('fee_category_id')
                  ->references('id')
                  ->on('fee_categories')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_set_items');
    }
};
