<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        $status = ucfirst($this->reservation->status);
        return new Envelope(
            subject: "Tu reserva — {$this->reservation->space->name} — {$status}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation-status-changed',
        );
    }
}