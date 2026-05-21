<?php

namespace App\Services;

use App\Mail\StudentAbsentAlertMail;
use App\Models\AttendanceItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AttendanceAbsentEmailService
{
    public function handle(): int
    {
        $query = AttendanceItem::query()
            ->where('status', 'absent')
            ->where('is_absent_email_sent', false)
            ->with([
                'attendance:id,class_id,section_id,date',
                'attendance.schoolClass:id,name_en',
                'attendance.section:id,name_en',
                'student:id,full_name_en,father_email,mother_email',
            ])
            ->whereHas('student', function ($q) {
                $q->whereNotNull('father_email')
                    ->orWhereNotNull('mother_email');
            })
            ->whereHas('attendance', fn ($q) => $q->whereDate('date', '<=', now()->toDateString()))
            ->orderBy('attendance_id')
            ->orderBy('id');

        $processed = 0;

        $query->chunkById(200, function ($items) use (&$processed) {
            foreach ($items as $item) {
                $attendance = $item->attendance;
                $student = $item->student;

                if (! $attendance || ! $student) {
                    continue;
                }

                $emails = collect([$student->father_email, $student->mother_email])
                    ->filter(fn ($email) => is_string($email) && trim($email) !== '')
                    ->map(fn ($email) => trim($email))
                    ->unique()
                    ->values();

                if ($emails->isEmpty()) {
                    continue;
                }

                $allSucceeded = true;
                foreach ($emails as $email) {
                    try {
                        Mail::to($email)->send(new StudentAbsentAlertMail($attendance, $student));
                    } catch (\Throwable $e) {
                        $allSucceeded = false;
                        Log::warning('Failed to send absent alert email.', [
                            'attendance_id' => $attendance->id,
                            'attendance_item_id' => $item->id,
                            'student_id' => $student->id,
                            'email' => $email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if ($allSucceeded) {
                    $item->is_absent_email_sent = true;
                    $item->save();
                    $processed++;
                }

                Log::info('Processed attendance absent email status for attendance item.', [
                    'attendance_id' => $attendance->id,
                    'attendance_item_id' => $item->id,
                    'student_id' => $student->id,
                    'marked_as_sent' => $allSucceeded,
                ]);
            }
        }, 'id');

        return $processed;
    }
}
