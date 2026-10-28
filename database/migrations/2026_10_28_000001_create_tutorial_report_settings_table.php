<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutorial_report_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('results_per_page')->default(2);
            $table->decimal('page_width_mm', 6, 2)->default(210);
            $table->decimal('page_height_mm', 6, 2)->default(297);
            $table->decimal('page_margin_top_mm', 6, 2)->default(10);
            $table->decimal('page_margin_right_mm', 6, 2)->default(12);
            $table->decimal('page_margin_bottom_mm', 6, 2)->default(10);
            $table->decimal('page_margin_left_mm', 6, 2)->default(12);
            $table->decimal('card_gap_mm', 6, 2)->default(2);
            $table->unsignedTinyInteger('font_scale_percent')->default(100);
            $table->unsignedTinyInteger('content_width_percent')->default(96);
            $table->decimal('table_padding_mm', 6, 2)->default(1.2);
            $table->decimal('card_border_width_mm', 5, 2)->default(0.3);
            $table->string('card_border_color', 20)->default('#cbd6d2');
            $table->unsignedTinyInteger('card_radius_mm')->default(2);
            $table->json('element_order')->nullable();
            $table->json('element_positions')->nullable();
            $table->json('element_sizes')->nullable();
            $table->json('element_visibility')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutorial_report_settings');
    }
};
