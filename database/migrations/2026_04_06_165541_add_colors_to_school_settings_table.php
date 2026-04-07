<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('primary_color')->default('#1e3a5f')->after('contact_number_2');
            $table->string('secondary_color')->default('#2563eb')->after('primary_color');
            $table->string('id_card_color')->default('#1e3a5f')->after('secondary_color');
        });
    }
    public function down(): void {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['primary_color', 'secondary_color', 'id_card_color']);
        });
    }
};
