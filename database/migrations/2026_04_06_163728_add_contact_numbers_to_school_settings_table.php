<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('contact_number_1')->nullable()->after('whatsapp_number');
            $table->string('contact_number_2')->nullable()->after('contact_number_1');
        });
    }
    public function down(): void {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['contact_number_1', 'contact_number_2']);
        });
    }
};
