<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->boolean('is_active')->default(true)->after('description');
            $table->unsignedBigInteger('active_template_id')->nullable()->after('is_active');
            $table->integer('sort_order')->default(0)->after('active_template_id');
        });

        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificates')->cascadeOnDelete();
            $table->string('name');
            $table->longText('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'slug',
                'description',
                'is_active',
                'active_template_id',
                'sort_order',
            ]);
        });
    }
};
