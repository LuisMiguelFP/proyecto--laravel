@component('mail::message')
# Reserva recibida

Hola *{{ $reservation->user_name }}*, tu solicitud de reserva fue recibida exitosamente.

| | |
|---|---|
| *Espacio* | {{ $reservation->space->name }} |
| *Fecha* | {{ $reservation->start_time->format('d/m/Y') }} |
| *Horario* | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| *Estado* | Pendiente de confirmación |

@if($reservation->notes)
*Notas:* {{ $reservation->notes }}
@endif

Te notificaremos cuando el administrador confirme o rechace tu reserva.

{{ config('app.name') }}
@endcomponent