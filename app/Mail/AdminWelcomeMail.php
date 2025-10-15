<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL; // ← add this

class AdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Admin $admin) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Admin Welcome Mail');
    }

    public function content(): Content
    {
        $loginUrl = Route::has('admin.login') ? route('admin.login') : url('/admin/login');

        $verificationUrl = null;
        if (empty($this->admin->email_verified_at)) {
            $verificationUrl = URL::temporarySignedRoute( // now resolved correctly
                'admin.verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $this->admin->getKey(),
                    'hash' => sha1($this->admin->email),
                ]
            );
        }

        return new Content(
            markdown: 'mail.admin.welcome',
            with: [
                'admin' => $this->admin,
                'loginUrl' => $loginUrl,
                'verificationUrl' => $verificationUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
