@component('mail::message')
# 📅 Reserva reagendada

Hola **{{ $reservation->user_name }}**,

Tu reserva ha sido **reagendada** exitosamente. Estos son los nuevos detalles:

| | |
|---|---|
| **Espacio** | {{ $reservation->space->name }} |
| **Nivel** | {{ \App\Models\Reservation::LEVELS[$reservation->level]['label'] ?? $reservation->level }} |
| **Nueva fecha** | {{ $reservation->start_time->format('d/m/Y') }} |
| **Nuevo horario** | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| **Duración** | {{ round($reservation->start_time->diffInMinutes($reservation->end_time) / 60, 1) }} hora(s) |
| **Precio total** | ${{ number_format($reservation->total_price, 0, ',', '.') }} COP |
| **Estado** | ⏳ Pendiente de confirmación |

@if($reservation->notes)
**Notas:** {{ $reservation->notes }}
@endif

El administrador deberá confirmar nuevamente tu reserva con el nuevo horario.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
