<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_category_id')->nullable()->after('id');
            $table->string('name')->after('asset_category_id');
            $table->text('description')->nullable()->after('name');
            $table->integer('quantity')->default(0)->after('description');
            $table->string('status')->default('active')->after('quantity'); // active, inactive, disposed

            $table->foreign('asset_category_id')->references('id')->on('asset_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['asset_category_id']);
            $table->dropColumn(['asset_category_id', 'name', 'description', 'quantity', 'status']);
        });
    }
};
