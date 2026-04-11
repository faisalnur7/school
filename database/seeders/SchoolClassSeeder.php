<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            'Play','Nursery', 'KG', 'One', 'Two', 'Three','Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten','Nazera','Hifz',
        ];

        $groupClasses = ['Nine', 'Ten'];
        $groups = ['Business Studies', 'Science','Humanities'];

        foreach ($classes as $className) {
            $class = SchoolClass::firstOrCreate(['name_en' => $className], [
                'name_bn' => $className
            ]);

            foreach (['A'] as $section) {
                Section::firstOrCreate([
                    'school_class_id' => $class->id,
                    'name_en'         => $section,
                ], ['name_bn' => $section]);
            }

            if (in_array($className, $groupClasses)) {
                foreach ($groups as $group) {
                    Group::firstOrCreate([
                        'school_class_id' => $class->id,
                        'name_en'         => $group,
                    ], [
                        'name_bn' => $group
                    ]);
                }
            }
        }
    }
}
