<?php

namespace App\Mail;

use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentResultReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public string $reportTitle,
        public array $meta,
        public array $rows,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reportTitle . ' - ' . ($this->student->full_name_en ?: 'Student')
        );
    }

    public function content(): Content
    {
        $school = SchoolSetting::current();
        $logoUrl = ! empty($school->logo) ? asset($school->logo) : rtrim(config('app.url'), '/') . '/assets/dist/img/AdminLTELogo.png';

        return new Content(
            view: 'emails.results.student-result-report',
            with: [
                'schoolLogoUrl' => $logoUrl,
                'schoolName' => $school->name ?: config('app.name'),
                'schoolContacts' => implode(' | ', array_filter([$school->contact_number_1, $school->contact_number_2])),
                'schoolSupportEmail' => $school->email ?: config('mail.from.address'),
                'schoolWhatsapp' => $school->whatsapp_number,
                'schoolWebsite' => $school->website,
                'student' => $this->student,
                'reportTitle' => $this->reportTitle,
                'meta' => $this->meta,
                'rows' => $this->rows,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
