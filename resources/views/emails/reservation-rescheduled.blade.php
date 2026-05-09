@component('mail::message')
# Reserva reagendada

Hola *{{ $reservation->user_name }}*,

Tu reserva ha sido *reagendada* exitosamente. Estos son los nuevos detalles:

| | |
|---|---|
| *Espacio* | {{ $reservation->space->name }} |
| *Nueva fecha* | {{ $reservation->start_time->format('d/m/Y') }} |
| *Nuevo horario* | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| *Estado* | Pendiente de confirmación |

@if($reservation->notes)
*Notas:* {{ $reservation->notes }}
@endif

{{ config('app.name') }}
@endcomponent
