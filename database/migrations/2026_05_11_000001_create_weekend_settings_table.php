<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekend_settings', function (Blueprint $table) {
            $table->id();
            // JSON array of weekday integers: 0=Sunday,1=Monday,...,6=Saturday
            $table->json('weekend_days');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekend_settings');
    }
};
