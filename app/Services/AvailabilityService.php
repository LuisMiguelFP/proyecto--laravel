<?php

namespace App\Services;

use App\Models\BlockedSlot;
use App\Models\Reservation;
use App\Models\Space;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityService
{
    private int $slotMinutes;

    public function __construct()
    {
        $this->slotMinutes = (int) env('RESERVATION_SLOT_MINUTES', 60);
    }

    // ── Validación completa de un rango ───────────────────────────────────
    // Retorna null si es válido, o string con el error
    public function validate(
        Space $space,
        Carbon $start,
        Carbon $end,
        ?int $ignoreReservationId = null
    ): ?string {
        $minutes = $start->diffInMinutes($end);

        if ($minutes < $this->slotMinutes || $minutes % $this->slotMinutes !== 0) {
            return "La duración mínima es {$this->slotMinutes} minutos.";
        }

        if ($start->gte($end)) {
            return 'La hora de inicio debe ser anterior a la hora de fin.';
        }

        if ($start->lt(now())) {
            return 'No se pueden realizar reservas en fechas pasadas.';
        }

        if (! $space->isWithinSchedule($start, $end)) {
            return 'El horario está fuera de la disponibilidad del espacio.';
        }

        $blocked = BlockedSlot::where('space_id', $space->id)
            ->overlapping($start, $end)
            ->exists();

        if ($blocked) {
            return 'El horario está bloqueado por mantenimiento o evento interno.';
        }

        $collision = Reservation::where('space_id', $space->id)
            ->overlapping($start, $end)
            ->when($ignoreReservationId, fn($q) => $q->where('id', '!=', $ignoreReservationId))
            ->exists();

        if ($collision) {
            return 'El horario ya está ocupado por otra reserva.';
        }

        return null;
    }

    // ── Slots disponibles para un día concreto ────────────────────────────
    public function getAvailableSlotsForDate(Space $space, Carbon $date): array
    {
        $availabilities = $space->availabilities()
            ->where('day_of_week', $date->dayOfWeek)
            ->get();

        if ($availabilities->isEmpty()) return [];

        $reservations = Reservation::where('space_id', $space->id)
            ->whereNotIn('status', [Reservation::STATUS_REJECTED, Reservation::STATUS_CANCELLED])
            ->whereDate('start_time', $date->toDateString())
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        $blocks = BlockedSlot::where('space_id', $space->id)
            ->whereDate('start_time', $date->toDateString())
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        $slots = [];

        foreach ($availabilities as $avail) {
            $cursor = $date->copy()->setTimeFromTimeString($avail->start_time);
            $limit  = $date->copy()->setTimeFromTimeString($avail->end_time);

            while ($cursor->copy()->addMinutes($this->slotMinutes)->lte($limit)) {
                $slotStart = $cursor->copy();
                $slotEnd   = $cursor->copy()->addMinutes($this->slotMinutes);

                if (! $this->isOccupied($slotStart, $slotEnd, $reservations, $blocks)
                    && $slotStart->gt(now())) {
                    $slots[] = [
                        'start'       => $slotStart->toIso8601String(),
                        'end'         => $slotEnd->toIso8601String(),
                        'start_label' => $slotStart->format('H:i'),
                        'end_label'   => $slotEnd->format('H:i'),
                    ];
                }

                $cursor->addMinutes($this->slotMinutes);
            }
        }

        return $slots;
    }

    // ── Próximos slots disponibles (vista detalle público) ────────────────
    public function getUpcomingSlots(Space $space, int $days = 14, int $limit = 10): array
    {
        $result = [];

        for ($i = 0; $i < $days && count($result) < $limit; $i++) {
            $date  = now()->startOfDay()->addDays($i);
            $slots = $this->getAvailableSlotsForDate($space, $date);

            foreach ($slots as $slot) {
                $result[] = $slot;
                if (count($result) >= $limit) break;
            }
        }

        return $result;
    }

    // ── Calendario semanal para el panel admin ────────────────────────────
    public function getWeeklyCalendarAdmin(Space $space, Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();

        $reservations = Reservation::where('space_id', $space->id)
            ->whereNotIn('status', [Reservation::STATUS_REJECTED, Reservation::STATUS_CANCELLED])
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->orderBy('start_time')
            ->get();

        $blocks = BlockedSlot::where('space_id', $space->id)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->orderBy('start_time')
            ->get();

        $days = [];
        foreach (CarbonPeriod::create($weekStart, '1 day', $weekEnd) as $day) {
            $dayKey  = $day->toDateString();
            $days[] = [
                'date'  => $dayKey,
                'label' => $day->locale('es')->isoFormat('dddd D MMM'),
                'reservations' => $reservations
                    ->filter(fn($r) => $r->start_time->toDateString() === $dayKey)
                    ->values()
                    ->map(fn($r) => [
                        'id'        => $r->id,
                        'slug'      => $r->slug,
                        'user_name' => $r->user_name,
                        'start'     => $r->start_time->format('H:i'),
                        'end'       => $r->end_time->format('H:i'),
                        'status'    => $r->status,
                    ])->toArray(),
                'blocks' => $blocks
                    ->filter(fn($b) => $b->start_time->toDateString() === $dayKey)
                    ->values()
                    ->map(fn($b) => [
                        'id'     => $b->id,
                        'start'  => $b->start_time->format('H:i'),
                        'end'    => $b->end_time->format('H:i'),
                        'reason' => $b->reason,
                    ])->toArray(),
            ];
        }

        return $days;
    }

    // ── Calendario semanal público (solo slots libres) ────────────────────
    public function getWeeklyCalendarPublic(Space $space, Carbon $weekStart): array
    {
        $days = [];
        foreach (CarbonPeriod::create($weekStart, '1 day', $weekStart->copy()->endOfWeek()) as $day) {
            $days[] = [
                'date'  => $day->toDateString(),
                'label' => $day->locale('es')->isoFormat('dddd D MMM'),
                'slots' => $this->getAvailableSlotsForDate($space, $day->copy()),
            ];
        }
        return $days;
    }

    // ── Privado ───────────────────────────────────────────────────────────
    private function isOccupied(Carbon $start, Carbon $end, $reservations, $blocks): bool
    {
        foreach ($reservations as $r) {
            if ($r->start_time < $end && $r->end_time > $start) return true;
        }
        foreach ($blocks as $b) {
            if ($b->start_time < $end && $b->end_time > $start) return true;
        }
        return false;
    }
}