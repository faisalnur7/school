<?php

namespace App\Mail;

use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAbsentAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Attendance $attendance,
        public Student $student,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Absent Alert',
        );
    }

    public function content(): Content
    {
        $school = SchoolSetting::current();
        $logoUrl = ! empty($school->logo) ? asset($school->logo) : rtrim(config('app.url'), '/') . '/assets/dist/img/AdminLTELogo.png';
        $schoolName = $school->name ?: config('app.name');
        $schoolContacts = implode(' | ', array_filter([$school->contact_number_1, $school->contact_number_2]));

        return new Content(
            view: 'emails.attendance.student-absent-alert',
            with: [
                'schoolLogoUrl' => $logoUrl,
                'schoolName' => $schoolName,
                'schoolContacts' => $schoolContacts,
                'schoolPhone1' => $school->contact_number_1,
                'schoolPhone2' => $school->contact_number_2,
                'schoolSupportEmail' => $school->email ?: config('mail.from.address'),
                'schoolWhatsapp' => $school->whatsapp_number,
                'schoolWebsite' => $school->website,
                'attendanceDate' => optional($this->attendance->date)->format('d M Y') ?: (string) $this->attendance->date,
                'className' => optional($this->attendance->schoolClass)->name_en ?: 'N/A',
                'sectionName' => optional($this->attendance->section)->name_en ?: 'N/A',
                'studentName' => $this->student->full_name_en,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
