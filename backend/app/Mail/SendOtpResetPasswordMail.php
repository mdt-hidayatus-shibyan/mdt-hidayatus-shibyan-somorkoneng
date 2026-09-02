<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $namaUstadz;
    public string $otpCode;
    public int $expiredMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $namaUstadz, string $otpCode, int $expiredMinutes = 15)
    {
        $this->namaUstadz = $namaUstadz;
        $this->otpCode = $otpCode;
        $this->expiredMinutes = $expiredMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Pemulihan Kata Sandi - MDT Hidayatus Shibyan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp_reset_password',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
