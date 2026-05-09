<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->reservation->id,
            'slug'       => $this->reservation->slug,
            'space_name' => $this->reservation->space?->name,
            'user_name'  => $this->reservation->user_name,
            'user_email' => $this->reservation->user_email,
            'start_time' => $this->reservation->start_time->format('d/m/Y H:i'),
            'end_time'   => $this->reservation->end_time->format('H:i'),
            'status'     => $this->reservation->status,
            'level'      => $this->reservation->level,
            'notes'      => $this->reservation->notes,
            'created_at' => $this->reservation->created_at->diffForHumans(),
        ];
    }
}
