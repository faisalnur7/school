<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('bn_name')->unique();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $data = [
            ['id'=>1,'bn_name' => 'চট্টগ্রাম', 'name' => 'Chattogram'],
            ['id'=>2,'bn_name' => 'রাজশাহী', 'name' => 'Rajshahi'],
            ['id'=>3,'bn_name' => 'খুলনা', 'name' => 'Khulna'],
            ['id'=>4,'bn_name' => 'বরিশাল', 'name' => 'Barishal'],
            ['id'=>5,'bn_name' => 'সিলেট', 'name' => 'Sylhet'],
            ['id'=>6,'bn_name' => 'ঢাকা', 'name' => 'Dhaka'],
            ['id'=>7,'bn_name' => 'রংপুর', 'name' => 'Rangpur'],
            ['id'=>8,'bn_name' => 'ময়মনসিংহ', 'name' => 'Mymensingh'],
        ];

        \DB::table('divisions')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
