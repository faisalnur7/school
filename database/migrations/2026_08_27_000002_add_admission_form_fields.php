<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'birth_certificate_number' => 'string',
            'gender' => 'unsignedTinyInteger',
            'disable' => 'boolean',
            'father_nid_number' => 'string',
            'fathers_profession_id' => 'unsignedBigInteger',
            'mother_nid_number' => 'string',
            'mothers_profession_id' => 'unsignedBigInteger',
            'annual_income' => 'string',
            'present_division_id' => 'unsignedBigInteger',
            'present_district_id' => 'unsignedBigInteger',
            'present_police_station_id' => 'unsignedBigInteger',
            'present_post_office_id' => 'unsignedBigInteger',
            'permanent_division_id' => 'unsignedBigInteger',
            'permanent_district_id' => 'unsignedBigInteger',
            'permanent_police_station_id' => 'unsignedBigInteger',
            'permanent_post_office_id' => 'unsignedBigInteger',
            'guardian_type' => 'unsignedTinyInteger',
            'guardian_profession_id' => 'unsignedBigInteger',
            'previous_class_appeared' => 'string',
            'tc_number' => 'string',
            'image' => 'string',
        ];

        Schema::table('admission_applications', function (Blueprint $table) use ($columns) {
            foreach ($columns as $name => $type) {
                $column = $table->{$type}($name)->nullable();
                if ($type === 'string') $column->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'birth_certificate_number', 'gender', 'disable', 'father_nid_number',
            'fathers_profession_id', 'mother_nid_number', 'mothers_profession_id',
            'annual_income', 'present_division_id', 'present_district_id',
            'present_police_station_id', 'present_post_office_id', 'permanent_division_id',
            'permanent_district_id', 'permanent_police_station_id', 'permanent_post_office_id',
            'guardian_type', 'guardian_profession_id', 'previous_class_appeared', 'tc_number', 'image',
        ];

        Schema::table('admission_applications', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
