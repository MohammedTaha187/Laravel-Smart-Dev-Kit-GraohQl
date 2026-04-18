<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $token) {}

    public function build()
    {
        return $this->subject('Password Reset Code')
                    ->markdown('emails.auth.password_reset');
    }
}
