<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->longText('description')->nullable()->after('title');
            $table->dateTime('event_date')->nullable()->after('description');
            $table->string('location')->nullable()->after('event_date');
            $table->string('image')->nullable()->after('location');
            $table->boolean('is_published')->default(true)->after('image');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->unsignedInteger('sort_order')->default(0)->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'event_date',
                'location',
                'image',
                'is_published',
                'published_at',
                'sort_order',
            ]);
        });
    }
};
