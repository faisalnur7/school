<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    public function run(): void
    {
        SchoolSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Green Chartered School and College',
                'address' => 'CIP Tower, Hazari Dighir Par, Dohazari, Chandanish, Chattogram',
                'eiin' => '121212',
                'from_class' => 1,
                'to_class' => 13,
                'slogan' => 'Stay Green, Be Bright',
                'website' => null,
                'email' => 'gcsc2025@gmail.com',
                'facebook_page' => null,
                'whatsapp_number' => '+880 1886-780641',
                'whatsapp_qr' => null,
                'contact_number_1' => '+880 1886-780641',
                'contact_number_2' => '+880 1886-780642',
                'primary_color' => '#1e3a5f',
                'secondary_color' => '#2563eb',
                'id_card_color' => '#1e3a5f',
                'logo' => 'assets/img/logo.png',
                'letter_head' => null,
                'created_at' => '2026-05-20 03:18:53',
                'updated_at' => '2026-05-20 03:18:53',
            ]
        );
    }
}
