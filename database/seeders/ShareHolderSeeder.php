<?php

namespace Database\Seeders;

use App\Models\Shareholder;
use Illuminate\Database\Seeder;

class ShareHolderSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Mohammed Abdul Alam',
            'Md Solaiman',
            'Mohammad Younus',
            'Obaidul Akbar Tutul',
            'Rokan Uddin Azam',
            'Mohammad Abdul Manan (CIP)',
            'Ridwanul Haque',
        ];

        foreach ($names as $name) {
            Shareholder::firstOrCreate(['name' => $name]);
        }
    }
}
