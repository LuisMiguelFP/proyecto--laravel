@component('mail::message')
# 🔔 Nueva reserva pendiente

Hola Admin,

El usuario **{{ $reservation->user_name }}** ({{ $reservation->user_email }}) ha solicitado una nueva reserva.

| | |
|---|---|
| **Espacio** | {{ $reservation->space->name }} |
| **Nivel** | {{ \App\Models\Reservation::LEVELS[$reservation->level]['label'] ?? $reservation->level }} |
| **Fecha** | {{ $reservation->start_time->format('d/m/Y') }} |
| **Horario** | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| **Duración** | {{ round($reservation->start_time->diffInMinutes($reservation->end_time) / 60, 1) }} hora(s) |
| **Precio total** | ${{ number_format($reservation->total_price, 0, ',', '.') }} COP |

@if($reservation->notes)
**Notas del usuario:** {{ $reservation->notes }}
@endif

Por favor, ingresa al panel de administración para confirmarla o rechazarla.

@component('mail::button', ['url' => url('/admin/reservations')])
Ver Reservas Pendientes
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
