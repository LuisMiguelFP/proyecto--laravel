@component('mail::message')
# ✅ Reserva recibida

Hola **{{ $reservation->user_name }}**,

Tu solicitud de reserva fue recibida exitosamente. A continuación los detalles:

| | |
|---|---|
| **Espacio** | {{ $reservation->space->name }} |
| **Nivel** | {{ \App\Models\Reservation::LEVELS[$reservation->level]['label'] ?? $reservation->level }} |
| **Fecha** | {{ $reservation->start_time->format('d/m/Y') }} |
| **Horario** | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| **Duración** | {{ round($reservation->start_time->diffInMinutes($reservation->end_time) / 60, 1) }} hora(s) |
| **Precio total** | ${{ number_format($reservation->total_price, 0, ',', '.') }} COP |
| **Estado** | ⏳ Pendiente de confirmación |

@if($reservation->notes)
**Notas:** {{ $reservation->notes }}
@endif

Te notificaremos por este mismo correo cuando el administrador confirme o rechace tu reserva.

Gracias,<br>
{{ config('app.name') }}
@endcomponent