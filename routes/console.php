<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AttendanceAbsentEmailService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('attendance:send-absent-emails', function (AttendanceAbsentEmailService $service) {
    $processed = $service->handle();
    $this->info("Marked {$processed} attendance item(s) as absent-email-sent.");
})->purpose('Send absent student alert emails to fathers/mothers.');

Schedule::command('attendance:send-absent-emails')->everyTenMinutes();
