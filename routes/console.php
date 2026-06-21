<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AttendanceAbsentEmailService;
use App\Services\ResultMarksImportService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('attendance:send-absent-emails', function (AttendanceAbsentEmailService $service) {
    $processed = $service->handle();
    $this->info("Marked {$processed} attendance item(s) as absent-email-sent.");
})->purpose('Send absent student alert emails to fathers/mothers.');

Schedule::command('attendance:send-absent-emails')->everyTenMinutes();

Artisan::command('results:seed-marks {--session=}', function (ResultMarksImportService $service) {
    $sessionId = $this->option('session') ? (int) $this->option('session') : null;
    $result = $service->run($sessionId);

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
