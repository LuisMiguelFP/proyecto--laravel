@component('mail::message')
# Actualización de tu reserva

Hola *{{ $reservation->user_name }}*,

@if($reservation->status === 'confirmada')
Tu reserva ha sido *confirmada* ✓
@elseif($reservation->status === 'rechazada')
Lamentablemente tu reserva ha sido *rechazada*.
@elseif($reservation->status === 'cancelada')
Tu reserva ha sido *cancelada*.
@endif

| | |
|---|---|
| *Espacio* | {{ $reservation->space->name }} |
| *Fecha* | {{ $reservation->start_time->format('d/m/Y') }} |
| *Horario* | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| *Estado* | {{ ucfirst($reservation->status) }} |

{{ config('app.name') }}
@endcomponent