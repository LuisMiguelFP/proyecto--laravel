<?php

namespace App\Http\Controllers;

use App\Events\ReservationCreated as ReservationCreatedEvent;
use App\Mail\ReservationCreated;
use App\Mail\ReservationRescheduled;
use App\Mail\ReservationStatusChanged;
use App\Models\Reservation;
use App\Models\Space;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    // GET /reservations/new?space={slug}&start={datetime}
    public function create(Request $request)
    {
        $space = Space::active()->where('slug', $request->space)->firstOrFail();
        $start = $request->filled('start') ? Carbon::parse($request->start) : null;
        $end   = $start?->copy()->addMinutes((int) env('RESERVATION_SLOT_MINUTES', 60));

        return Inertia::render('Public/Reservations/Create', [
            'space' => [
                'id'   => $space->id,
                'name' => $space->name,
                'slug' => $space->slug,
            ],
            'prefill' => [
                'start' => $start?->toIso8601String(),
                'end'   => $end?->toIso8601String(),
            ],
            'levels'      => Reservation::LEVELS,
            'slotMinutes' => (int) env('RESERVATION_SLOT_MINUTES', 60),
            'auth'        => [
                'user' => Auth::check() ? [
                    'name'  => Auth::user()->name,
                    'email' => Auth::user()->email,
                ] : null,
            ],
        ]);
    }

    // POST /reservations
    public function store(Request $request)
    {
        $validated = $request->validate([
            'space_id'   => ['required', 'integer', 'exists:spaces,id'],
            'level'      => ['required', 'in:basica,estandar,gold'],
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
            'user_name'  => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $space = Space::findOrFail($validated['space_id']);
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);

        $error = $this->availability->validate($space, $start, $end);
        if ($error) {
            return back()->withErrors(['availability' => $error]);
        }

        $validated['user_id'] = Auth::id();

        $reservation = Reservation::create($validated);
        $reservation->load('space');

        event(new ReservationCreatedEvent($reservation));

        Mail::to($reservation->user_email)->send(new ReservationCreated($reservation));

        $admins = \App\Models\User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new \App\Mail\AdminReservationCreated($reservation));
        }

        return Inertia::render('Public/Reservations/Confirmation', [
            'reservation' => [
                'slug'        => $reservation->slug,
                'space_name'  => $reservation->space->name,
                'level'       => $reservation->level,
                'level_label' => Reservation::LEVELS[$reservation->level]['label'],
                'tools'       => Reservation::LEVELS[$reservation->level]['tools'],
                'start_time'  => $reservation->start_time->format('d/m/Y H:i'),
                'end_time'    => $reservation->end_time->format('H:i'),
                'total_price' => $reservation->total_price,
                'status'      => $reservation->status,
                'user_name'   => $reservation->user_name,
                'user_email'  => $reservation->user_email,
            ],
        ]);
    }

    // GET /reservations/{slug}/confirmation
    public function confirmation($slug)
    {
        $reservation = Reservation::where('slug', $slug)->firstOrFail();
        return Inertia::render('Public/Reservations/Confirmation', [
            'reservation' => [
                'slug'        => $reservation->slug,
                'space_name'  => $reservation->space->name,
                'level'       => $reservation->level,
                'level_label' => Reservation::LEVELS[$reservation->level]['label'],
                'tools'       => Reservation::LEVELS[$reservation->level]['tools'],
                'start_time'  => $reservation->start_time->format('d/m/Y H:i'),
                'end_time'    => $reservation->end_time->format('H:i'),
                'total_price' => $reservation->total_price,
                'status'      => $reservation->status,
                'user_name'   => $reservation->user_name,
                'user_email'  => $reservation->user_email,
            ],
        ]);
    }

    // GET /my-reservations  (usuario autenticado)
    public function myReservations()
    {
        $reservations = Reservation::with('space')
            ->where('user_id', Auth::id())
            ->orderByDesc('start_time')
            ->get()
            ->map(fn($r) => [
                'slug'        => $r->slug,
                'space_name'  => $r->space->name,
                'level'       => $r->level,
                'level_label' => Reservation::LEVELS[$r->level]['label'] ?? $r->level,
                'level_color' => Reservation::LEVELS[$r->level]['color'] ?? 'gray',
                'start_time'  => $r->start_time->format('d/m/Y H:i'),
                'end_time'    => $r->end_time->format('H:i'),
                'status'      => $r->status,
                'total_price' => $r->total_price,
                'notes'       => $r->notes,
            ]);

        return Inertia::render('Public/Reservations/MyReservations', [
            'reservations' => $reservations,
        ]);
    }

    // PUT /my-reservations/{reservation}/reschedule
    public function reschedule(Request $request, Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);
        abort_unless($reservation->isPending(), 422, 'Solo se pueden reagendar reservas pendientes.');

        $validated = $request->validate([
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
        ]);

        $space = $reservation->space;
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);

        $error = $this->availability->validate($space, $start, $end, $reservation->id);
        if ($error) {
            return back()->withErrors(['availability' => $error]);
        }

        $reservation->update([
            'start_time' => $start,
            'end_time'   => $end,
        ]);

        $reservation->load('space');
        Mail::to($reservation->user_email)->send(new ReservationRescheduled($reservation));

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ReservationRescheduled($reservation));
        }

        return back()->with('success', 'Reserva reagendada exitosamente.');
    }

    public function destroy(Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);
        abort_unless(in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED]), 422);

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        $reservation->load('space');
        Mail::to($reservation->user_email)->send(new ReservationStatusChanged($reservation));

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ReservationStatusChanged($reservation));
        }

        return back()->with('success', 'Reserva cancelada.');
    }
}