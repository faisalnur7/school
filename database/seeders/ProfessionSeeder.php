<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            ['name' => 'Farmer',              'bn_name' => 'কৃষক'],
            ['name' => 'Business',            'bn_name' => 'ব্যবসায়ী'],
            ['name' => 'Service',             'bn_name' => 'চাকরিজীবী'],
            ['name' => 'Teacher',             'bn_name' => 'শিক্ষক'],
            ['name' => 'Doctor',              'bn_name' => 'ডাক্তার'],
            ['name' => 'Engineer',            'bn_name' => 'প্রকৌশলী'],
            ['name' => 'Lawyer',              'bn_name' => 'আইনজীবী'],
            ['name' => 'Banker',              'bn_name' => 'ব্যাংকার'],
            ['name' => 'Police',              'bn_name' => 'পুলিশ'],
            ['name' => 'Army',                'bn_name' => 'সেনাবাহিনী'],
            ['name' => 'Driver',              'bn_name' => 'চালক'],
            ['name' => 'Shopkeeper',          'bn_name' => 'দোকানদার'],
            ['name' => 'Tailor',              'bn_name' => 'দর্জি'],
            ['name' => 'Carpenter',           'bn_name' => 'কাঠমিস্ত্রি'],
            ['name' => 'Mason',               'bn_name' => 'রাজমিস্ত্রি'],
            ['name' => 'Electrician',         'bn_name' => 'ইলেকট্রিশিয়ান'],
            ['name' => 'Nurse',               'bn_name' => 'নার্স'],
            ['name' => 'Journalist',          'bn_name' => 'সাংবাদিক'],
            ['name' => 'Accountant',          'bn_name' => 'হিসাবরক্ষক'],
            ['name' => 'Garments Worker',     'bn_name' => 'গার্মেন্টস কর্মী'],
            ['name' => 'Day Laborer',         'bn_name' => 'দিনমজুর'],
            ['name' => 'Housewife',           'bn_name' => 'গৃহিণী'],
            ['name' => 'Expatriate',          'bn_name' => 'প্রবাসী'],
            ['name' => 'NGO Worker',          'bn_name' => 'এনজিও কর্মী'],
            ['name' => 'Retired',             'bn_name' => 'অবসরপ্রাপ্ত'],
            ['name' => 'Unemployed',          'bn_name' => 'বেকার'],
            ['name' => 'Other',               'bn_name' => 'অন্যান্য'],
        ];

        foreach ($professions as $p) {
            Profession::firstOrCreate(['name' => $p['name']], ['bn_name' => $p['bn_name']]);
        }

        $this->command->info('✅ Professions seeded: ' . Profession::count());
    }
}
