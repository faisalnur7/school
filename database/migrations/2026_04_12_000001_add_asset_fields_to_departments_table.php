<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('code');
            $table->boolean('is_active')->default(true)->after('description');
        });

        $departments = DB::table('departments')->select('id', 'name')->orderBy('id')->get();
        $usedCodes = [];

        foreach ($departments as $department) {
            $baseCode = strtoupper(Str::limit(preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::slug($department->name, ''))), 10, ''));
            $baseCode = $baseCode !== '' ? $baseCode : 'DEPT';
            $code = $baseCode;
            $suffix = 1;

            while (in_array($code, $usedCodes, true) || DB::table('departments')->where('code', $code)->where('id', '!=', $department->id)->exists()) {
                $code = Str::limit($baseCode, 8, '') . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
                $suffix++;
            }

            $usedCodes[] = $code;

            DB::table('departments')
                ->where('id', $department->id)
                ->update([
                    'code' => $code,
                    'is_active' => true,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'description', 'is_active']);
        });
    }
};
