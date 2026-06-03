<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('excerpt');
            $table->string('page_type')->default('custom')->after('slug'); // home|about|contact|notices|events|calendar|custom
        });

        Schema::table('website_sections', function (Blueprint $table) {
            $table->string('image')->nullable()->after('content');
            $table->string('image_position')->default('right')->after('image'); // left|right|top|background
            $table->boolean('is_active')->default(true)->after('sort_order');
        });

        Schema::table('website_banners', function (Blueprint $table) {
            $table->string('button_style')->default('white')->after('cta_url'); // white|outline
        });
    }

    public function down(): void
    {
        Schema::table('website_pages', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'page_type']);
        });
        Schema::table('website_sections', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_position', 'is_active']);
        });
        Schema::table('website_banners', function (Blueprint $table) {
            $table->dropColumn('button_style');
        });
    }
};
