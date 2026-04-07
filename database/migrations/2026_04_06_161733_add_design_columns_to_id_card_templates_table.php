<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('id_card_templates', function (Blueprint $table) {
            $table->json('design_front')->nullable()->after('orientation');
            $table->json('design_back')->nullable()->after('design_front');
            $table->string('front_bg_image')->nullable()->after('design_back');
            $table->string('back_bg_image')->nullable()->after('front_bg_image');
            $table->string('front_bg_color')->default('#ffffff')->after('back_bg_image');
            $table->string('back_bg_color')->default('#ffffff')->after('front_bg_color');
        });
    }
    public function down(): void {
        Schema::table('id_card_templates', function (Blueprint $table) {
            $table->dropColumn(['design_front','design_back','front_bg_image','back_bg_image','front_bg_color','back_bg_color']);
        });
    }
};
