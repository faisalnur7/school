<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentParentInfoSeeder extends Seeder
{
    private array $fatherFirstNames = [
        'Mohammad', 'Abdul', 'Md.', 'Ahmed', 'Karim', 'Rahim', 'Islam', 'Hossain',
        'Rahman', 'Ali', 'Hassan', 'Hussain', 'Miah', 'Sheikh', 'Sarkar', 'Khan',
        'Uddin', 'Chowdhury', 'Bhuiyan', 'Molla', 'Mondal', 'Biswas', 'Das', 'Roy',
    ];

    private array $fatherLastNames = [
        'Hossain', 'Rahman', 'Islam', 'Ahmed', 'Ali', 'Khan', 'Miah', 'Uddin',
        'Chowdhury', 'Sarkar', 'Sheikh', 'Bhuiyan', 'Molla', 'Mondal', 'Biswas',
        'Karim', 'Rahim', 'Hassan', 'Hussain', 'Akter', 'Begum', 'Khatun', 'Das',
    ];

    private array $motherFirstNames = [
        'Fatema', 'Rahela', 'Nasrin', 'Sultana', 'Begum', 'Akter', 'Khatun', 'Parvin',
        'Marium', 'Halima', 'Sumaiya', 'Taslima', 'Roksana', 'Shirin', 'Nargis',
        'Bilkis', 'Morjina', 'Salma', 'Amena', 'Hasina', 'Monira', 'Lovely', 'Popy',
    ];

    private array $motherLastNames = [
        'Begum', 'Akter', 'Khatun', 'Parvin', 'Sultana', 'Islam', 'Rahman', 'Hossain',
        'Chowdhury', 'Bhuiyan', 'Molla', 'Mondal', 'Biswas', 'Das', 'Roy', 'Khan',
        'Sarkar', 'Sheikh', 'Ahmed', 'Ali', 'Miah', 'Uddin', 'Hassan',
    ];

    public function run(): void
    {
        $this->command->info('Seeding parent info for ' . Student::count() . ' students...');

        $bar = $this->command->getOutput()->createProgressBar(Student::count());
        $bar->start();

        Student::query()->chunkById(200, function ($students) use ($bar) {
            foreach ($students as $student) {
                $student->update([
                    'father_name'  => $this->randomName($this->fatherFirstNames, $this->fatherLastNames),
                    'father_phone' => $this->randomPhone(),
                    'mother_name'  => $this->randomName($this->motherFirstNames, $this->motherLastNames),
                    'mother_phone' => $this->randomPhone(),
                ]);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done.');
    }

    private function randomName(array $firstNames, array $lastNames): string
    {
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function randomPhone(): string
    {
        $prefixes = ['013', '014', '015', '016', '017', '018', '019'];
        return $prefixes[array_rand($prefixes)] . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }
}
