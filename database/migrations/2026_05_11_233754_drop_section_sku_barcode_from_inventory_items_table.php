<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropUnique('inv_items_books_unique');
            $table->unique(['category_id', 'name', 'school_class_id', 'group_id'], 'inv_items_books_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->unique(['category_id', 'sku'], 'inventory_items_category_id_sku_unique');
            $table->unique(['category_id', 'name', 'school_class_id', 'section_id', 'group_id'], 'inv_items_books_unique');
        });
    }
};
