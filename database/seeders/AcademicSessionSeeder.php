<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['2026'] as $year) {
            AcademicSession::firstOrCreate(['name_en' => $year], [
                'name_bn' => $year,
                'status'  => 1,
            ]);
        }
    }
}
