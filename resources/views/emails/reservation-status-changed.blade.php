@component('mail::message')
@if($reservation->status === 'confirmada')
# ✅ ¡Tu reserva ha sido confirmada!

Hola **{{ $reservation->user_name }}**,

¡Buenas noticias! Tu reserva ha sido **confirmada** por el administrador. Te esperamos en la fecha acordada.

@elseif($reservation->status === 'rechazada')
# ❌ Tu reserva ha sido rechazada

Hola **{{ $reservation->user_name }}**,

Lamentamos informarte que tu reserva ha sido **rechazada**. Puedes intentar reservar en otro horario o contactarnos para más información.

@elseif($reservation->status === 'cancelada')
# 🚫 Tu reserva ha sido cancelada

Hola **{{ $reservation->user_name }}**,

Tu reserva ha sido **cancelada**. Si tienes alguna duda, no dudes en contactarnos.

@else
# Actualización de tu reserva

Hola **{{ $reservation->user_name }}**,

El estado de tu reserva ha cambiado a: **{{ ucfirst($reservation->status) }}**.

@endif

| | |
|---|---|
| **Espacio** | {{ $reservation->space->name }} |
| **Nivel** | {{ \App\Models\Reservation::LEVELS[$reservation->level]['label'] ?? $reservation->level }} |
| **Fecha** | {{ $reservation->start_time->format('d/m/Y') }} |
| **Horario** | {{ $reservation->start_time->format('H:i') }} – {{ $reservation->end_time->format('H:i') }} |
| **Precio total** | ${{ number_format($reservation->total_price, 0, ',', '.') }} COP |
| **Estado** | {{ ucfirst($reservation->status) }} |

Gracias,<br>
{{ config('app.name') }}
@endcomponent