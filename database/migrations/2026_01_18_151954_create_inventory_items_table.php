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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('inventory_categories');
            $table->enum('item_type', ['common', 'classwise'])->default('common');

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('minimum_stock_alert')->default(0);
            $table->string('unit')->nullable();
            $table->boolean('is_active')->default(true);

            // Classwise fields (nullable for common items)
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes');
            $table->foreignId('group_id')->nullable()->constrained('groups');

            $table->index(['category_id']);
            $table->index(['is_active']);
            $table->index(['item_type']);
            $table->unique(['category_id', 'name', 'school_class_id', 'group_id'], 'inv_items_books_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
