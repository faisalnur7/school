<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class StudentMobileAccountService
{
    public function createOrReset(Student $student): User
    {
        $cid = trim((string) $student->student_cid);

        if ($cid === '') {
            throw new InvalidArgumentException('A student must have a CID before a mobile account can be created.');
        }

        $role = Role::firstOrCreate(
            ['name' => 'Student'],
            ['description' => 'Student mobile application accounts']
        );

        return User::updateOrCreate(
            ['student_id' => $student->id],
            [
                'name' => $student->full_name_en ?: $student->full_name_bn ?: $cid,
                // Users currently authenticate through the student CID in the mobile API.
                // Keep a unique internal email because the users table requires one.
                'email' => 'student-'.$student->id.'@student.local',
                'password' => Hash::make($cid),
                'role_id' => $role->id,
                'is_active' => true,
            ]
        );
    }
}
