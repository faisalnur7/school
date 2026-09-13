<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kind')->default('teaching');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kind', 'is_active', 'sort_order']);
        });

        $now = now();
        DB::table('class_schedules')->insert([
            ['name' => 'Assembly', 'kind' => 'assembly', 'start_time' => '08:15:00', 'end_time' => '08:30:00', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '1st Period', 'kind' => 'teaching', 'start_time' => '08:30:00', 'end_time' => '09:20:00', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '2nd Period', 'kind' => 'teaching', 'start_time' => '09:20:00', 'end_time' => '10:05:00', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '3rd Period', 'kind' => 'teaching', 'start_time' => '10:05:00', 'end_time' => '10:50:00', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '4th Period', 'kind' => 'teaching', 'start_time' => '10:50:00', 'end_time' => '11:30:00', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tiffin Time', 'kind' => 'tiffin', 'start_time' => '11:30:00', 'end_time' => '11:50:00', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '5th Period', 'kind' => 'teaching', 'start_time' => '11:50:00', 'end_time' => '12:35:00', 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '6th Period', 'kind' => 'teaching', 'start_time' => '12:35:00', 'end_time' => '13:15:00', 'sort_order' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '7th Period', 'kind' => 'teaching', 'start_time' => '13:15:00', 'end_time' => '14:00:00', 'sort_order' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Prayer Time', 'kind' => 'prayer', 'start_time' => '14:00:00', 'end_time' => '14:20:00', 'sort_order' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
