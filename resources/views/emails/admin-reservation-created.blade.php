@component('mail::message')
# Nueva reserva recibida

Hola Admin, el usuario **{{ $reservation->user_name }}** ({{ $reservation->user_email }}) ha solicitado una reserva.

| | |
|---|---|
| *Espacio* | {{ $reservation->space->name }} |
| *Nivel* | {{ $reservation->level }} |
| *Fecha* | {{ $reservation->start_time->format('d/m/Y') }} |
| *Horario* | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |

@if($reservation->notes)
*Notas:* {{ $reservation->notes }}
@endif

Por favor, ingresa al panel de administración para confirmarla o rechazarla.

@component('mail::button', ['url' => url('/admin/home')])
Ir al Panel
@endcomponent

{{ config('app.name') }}
@endcomponent
