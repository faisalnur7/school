<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('eiin')->nullable();
            $table->unsignedBigInteger('from_class')->nullable();
            $table->unsignedBigInteger('to_class')->nullable();
            $table->string('slogan')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('logo')->nullable();
            $table->string('letter_head')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
