<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AttendanceAbsentEmailService;
use App\Services\ResultMarksImportService;
use App\Services\StudentPushNotificationService;
use App\Jobs\SendStudentAbsentPushesJob;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('attendance:send-absent-emails', function (AttendanceAbsentEmailService $service) {
    $processed = $service->handle();
    $this->info("Marked {$processed} attendance item(s) as absent-email-sent.");
})->purpose('Send absent student alert emails to fathers/mothers.');

Schedule::command('attendance:send-absent-emails')->everyTenMinutes();

Artisan::command('attendance:send-absent-pushes', function (StudentPushNotificationService $service) {
    $this->info('Sent '.$service->sendAbsentAttendance().' absent-student push notification(s).');
})->purpose('Send deduplicated FCM alerts to active students marked absent today.');
Schedule::job(new SendStudentAbsentPushesJob)->everyTenMinutes()->withoutOverlapping();

Artisan::command('students:create-mobile-account {student : Student ID or CID} {--password= : Initial password; never uses a shared default}', function () {
    $student = Student::query()->whereKey($this->argument('student'))->orWhere('student_cid', $this->argument('student'))->first();
    if (!$student) { $this->error('Student not found.'); return 1; }
    if (!$student->status || !$student->latestAcademicInformation?->is_current || $student->latestAcademicInformation?->academic_status !== 'active') { $this->error('Only an active student can receive a mobile account.'); return 1; }
    $password = (string) $this->option('password');
    if (strlen($password) < 8) { $this->error('Provide --password with at least 8 characters.'); return 1; }
    $role = Role::firstOrCreate(['name' => 'Student'], ['description' => 'Student mobile application accounts']);
    $email = 'student-'.$student->id.'@student.local';
    $user = User::updateOrCreate(['student_id' => $student->id], ['name' => $student->full_name_en ?: $student->full_name_bn, 'email' => $email, 'password' => Hash::make($password), 'role_id' => $role->id, 'is_active' => true]);
    $this->info("Mobile account {$user->email} is ready for {$student->student_cid}.");
})->purpose('Create or reset one active student mobile account with an explicitly supplied password.');

Artisan::command('students:create-mobile-accounts {--password= : Initial password for every account; use a unique password per student when possible} {--reset-existing : Also reset the password of accounts that already exist}', function () {
    $password = (string) $this->option('password');
    if (strlen($password) < 8) {
        $this->error('Provide --password with at least 8 characters.');
        return 1;
    }

    $role = Role::firstOrCreate(
        ['name' => 'Student'],
        ['description' => 'Student mobile application accounts']
    );

    $students = Student::query()
        ->where('status', 1)
        ->whereHas('latestAcademicInformation', fn ($query) => $query
            ->where('is_current', true)
            ->where('academic_status', 'active'))
        ->with('user')
        ->orderBy('student_cid')
        ->get();

    $created = 0;
    $skipped = 0;
    $reset = 0;

    foreach ($students as $student) {
        $user = $student->user;
        if ($user && ! $this->option('reset-existing')) {
            $skipped++;
            continue;
        }

        $attributes = [
            'name' => $student->full_name_en ?: $student->full_name_bn,
            'email' => 'student-'.$student->id.'@student.local',
            'role_id' => $role->id,
            'is_active' => true,
        ];

        if ($user) {
            $user->update($attributes + ['password' => Hash::make($password)]);
            $reset++;
        } else {
            User::create($attributes + [
                'student_id' => $student->id,
                'password' => Hash::make($password),
            ]);
            $created++;
        }
    }

    $this->info("Student mobile accounts ready: created={$created}, reset={$reset}, skipped={$skipped}.");
    $this->line('Students log in to the mobile app with their Student CID and this password.');
})->purpose('Create mobile accounts for all active students without resetting existing accounts by default.');

Artisan::command('results:seed-marks {--session=} {--all}', function (ResultMarksImportService $service) {
    $sessionId = $this->option('session') ? (int) $this->option('session') : null;
    $all = (bool) $this->option('all');
    $result = $service->run($sessionId, false, $all);

    $this->info("Seeded marks for session {$result['session']['id']} ({$result['session']['name']}).");
    foreach ($result['summary'] as $row) {
        $this->line(sprintf(
            '%s: %d students, %d subjects, %d exams, created=%d, updated=%d, skipped=%d',
            $row['cohort'],
            $row['students'],
            $row['subjects'],
            $row['exams'],
            $row['created'],
            $row['updated'],
            $row['skipped']
        ));
    }
})->purpose('Seed realistic result marks for the target cohorts.');

Artisan::command('results:sweep-marks {--session=} {--all}', function (ResultMarksImportService $service) {
    $sessionId = $this->option('session') ? (int) $this->option('session') : null;
    $all = (bool) $this->option('all');
    $result = $service->sweep($sessionId, $all);

    $this->warn("Swept marks for session {$result['session']['id']} ({$result['session']['name']}).");
    foreach ($result['summary'] as $row) {
        $this->line(sprintf(
            '%s: deleted %d records',
            $row['cohort'],
            $row['records_deleted']
        ));
    }
})->purpose('Sweep generated result marks from the target cohorts or the full session when --all is used.');
