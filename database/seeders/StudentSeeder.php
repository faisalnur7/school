<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    private array $maleNames = [
        'Abdullah', 'Ibrahim', 'Yusuf', 'Omar', 'Ali', 'Hassan', 'Hussain',
        'Ahmad', 'Muhammad', 'Bilal', 'Khalid', 'Tariq', 'Zaid', 'Hamza',
        'Usman', 'Salman', 'Faisal', 'Imran', 'Adnan', 'Kamrul', 'Rakib',
        'Sabbir', 'Tanvir', 'Mahfuz', 'Shahriar', 'Nayeem', 'Rifat',
        'Raihan', 'Sohel', 'Arif',
    ];

    private array $femaleNames = [
        'Fatima', 'Aisha', 'Khadija', 'Maryam', 'Zainab', 'Hafsa', 'Ruqayyah',
        'Sumaya', 'Asma', 'Nusaybah', 'Ramlah', 'Safiyya', 'Maymuna',
        'Umm Kulthum', 'Sumaira', 'Nadia', 'Sadia', 'Tania', 'Rima',
        'Lamia', 'Tasnim', 'Nusrat', 'Sharmin', 'Bristy', 'Mitu',
        'Puja', 'Riya', 'Sanjida', 'Mahfuza', 'Taslima',
    ];

    private array $lastNames = [
        'Khan', 'Ahmed', 'Hossain', 'Islam', 'Rahman', 'Akter', 'Begum',
        'Chowdhury', 'Miah', 'Sheikh', 'Sarkar', 'Mondal', 'Talukder',
        'Bhuiyan', 'Siddiqui',
    ];

    private function randomName(int $gender): string
    {
        $first = $gender === Student::MALE
            ? $this->maleNames[array_rand($this->maleNames)]
            : $this->femaleNames[array_rand($this->femaleNames)];

        return $first . ' ' . $this->lastNames[array_rand($this->lastNames)];
    }

    private function randomDob(): string
    {
        $year  = rand(2010, 2020);
        $month = rand(1, 12);
        $day   = rand(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    private function randomBloodGroup(): int
    {
        return array_rand(Student::BLOOD_GROUPS);
    }

    public function run(): void
    {
        $sessions    = AcademicSession::all();
        $classes     = SchoolClass::all();
        $bloodGroups = array_keys(Student::BLOOD_GROUPS);

        foreach ($sessions as $session) {
            foreach ($classes as $class) {
                $sections = Section::where('school_class_id', $class->id)->get();
                $groups   = Group::where('school_class_id', $class->id)->get();

                foreach ($sections as $section) {
                    for ($i = 1; $i <= 30; $i++) {
                        $gender = $i % 2 === 0 ? Student::FEMALE : Student::MALE;

                        $student = Student::create([
                            'full_name_en' => $this->randomName($gender),
                            'gender'       => $gender,
                            'date_of_birth'=> $this->randomDob(),
                            'blood_group'  => $bloodGroups[array_rand($bloodGroups)],
                            'religion'     => Student::ISLAM,
                            'status'       => 1,
                        ]);

                        StudentAcademicInformation::create([
                            'student_id'          => $student->id,
                            'academic_session_id' => $session->id,
                            'school_class_id'     => $class->id,
                            'section_id'          => $section->id,
                            'group_id'            => $groups->isNotEmpty() ? $groups->random()->id : null,
                            'roll'                => $i,
                        ]);
                    }
                }
            }
        }
    }
}
