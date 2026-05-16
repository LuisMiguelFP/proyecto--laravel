<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationStatusChanged;
use App\Models\Reservation;
use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ReservationAdminController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    // GET /admin/reservations
    public function index(Request $request)
    {
        $query = Reservation::with('space')->orderByDesc('start_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('space')) {
            $query->whereHas('space', fn($q) => $q->where('slug', $request->space));
        }
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $reservations = $query->paginate(20)->through(fn($r) => [
            'id'         => $r->id,
            'slug'       => $r->slug,
            'space_name' => $r->space->name,
            'space_slug' => $r->space->slug,
            'user_name'  => $r->user_name,
            'user_email' => $r->user_email,
            'start_time' => $r->start_time->format('d/m/Y H:i'),
            'end_time'   => $r->end_time->format('H:i'),
            'status'     => $r->status,
            'notes'      => $r->notes,
        ]);

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
            'spaces'       => Space::active()->orderBy('name')->get(['name', 'slug']),
            'filters'      => $request->only(['status', 'space', 'date']),
            'statuses'     => Reservation::STATUSES,
        ]);
    }

    // POST /admin/reservations/{reservation}/accept
    public function accept(Reservation $reservation)
    {
        abort_unless($reservation->isPending(), 422, 'Solo se pueden confirmar reservas pendientes.');

        $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);

        try {
            $reservation->load('space');
            Mail::to($reservation->user_email)->queue(new ReservationStatusChanged($reservation));
        } catch (\Exception $e) {
            Log::error('Error encolando email de confirmación al usuario: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_email' => $reservation->user_email,
            ]);
        }

        return back()->with('success', 'Reserva confirmada. Se envió notificación al usuario.');
    }

    // POST /admin/reservations/{reservation}/reject
    public function reject(Reservation $reservation)
    {
        abort_unless($reservation->isPending(), 422, 'Solo se pueden rechazar reservas pendientes.');

        $reservation->update(['status' => Reservation::STATUS_REJECTED]);

        try {
            $reservation->load('space');
            Mail::to($reservation->user_email)->queue(new ReservationStatusChanged($reservation));
        } catch (\Exception $e) {
            Log::error('Error encolando email de rechazo al usuario: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_email' => $reservation->user_email,
            ]);
        }

        return back()->with('success', 'Reserva rechazada. Se envió notificación al usuario.');
    }

    // POST /admin/reservations/{reservation}/cancel
    public function cancel(Reservation $reservation)
    {
        abort_unless($reservation->isConfirmed(), 422, 'Solo se pueden cancelar reservas confirmadas.');

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        try {
            $reservation->load('space');
            Mail::to($reservation->user_email)->queue(new ReservationStatusChanged($reservation));
        } catch (\Exception $e) {
            Log::error('Error encolando email de cancelación al usuario: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_email' => $reservation->user_email,
            ]);
        }

        return back()->with('success', 'Reserva cancelada. Se envió notificación al usuario.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Reserva eliminada permanentemente.');
    }
}