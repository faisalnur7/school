<?php

namespace App\Mail;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Verification Code',
        );
    }

    public function content(): Content
    {
        $school = SchoolSetting::current();
        $logoUrl = ! empty($school->logo) ? asset($school->logo) : rtrim(config('app.url'), '/') . '/assets/dist/img/AdminLTELogo.png';
        $schoolName = $school->name ?: config('app.name');
        $schoolContacts = implode(' | ', array_filter([$school->contact_number_1, $school->contact_number_2]));

        return new Content(
            view: 'emails.auth.login-verification-code',
            with: [
                'schoolLogoUrl' => $logoUrl,
                'schoolName' => $schoolName,
                'schoolContacts' => $schoolContacts,
                'schoolAddress' => $school->address ?: 'School Address Not Set',
                'schoolSupportEmail' => $school->email ?: config('mail.from.address'),
                'schoolSignature' => $schoolName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
