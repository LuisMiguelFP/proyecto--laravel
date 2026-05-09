<?php

use App\Http\Controllers\Admin\BlockedSlotController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationAdminController;
use App\Http\Controllers\Admin\SpaceAdminController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SpaceController;
use App\Models\Reservation;
use App\Models\Space;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->is_admin) {
            return redirect('/admin/home');
        }
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])
    ->group(function () {
        Route::get('/dashboard', function () {
            $reservations = Reservation::with('space')
                ->where('user_id', auth()->id())
                ->orderByDesc('start_time')
                ->get()
                ->map(fn($r) => [
                    'slug'        => $r->slug,
                    'space_name'  => $r->space->name,
                    'level'       => $r->level,
                    'level_label' => Reservation::LEVELS[$r->level]['label'] ?? $r->level,
                    'start_time'  => $r->start_time->format('d/m/Y H:i'),
                    'end_time'    => $r->end_time->format('H:i'),
                    'status'      => $r->status,
                    'total_price' => $r->total_price,
                    'notes'       => $r->notes,
                ]);

            $spaces = Space::active()->orderBy('name')->get(['id', 'name']);

            return inertia('Dashboard', [
                'reservations' => $reservations,
                'spaces'       => $spaces,
                'levels'       => Reservation::LEVELS,
            ]);
        })->name('dashboard');

        Route::get('/spaces', [SpaceController::class, 'index'])->name('spaces.index');
        Route::get('/spaces/{space}', [SpaceController::class, 'show'])->name('spaces.show');
        Route::get('/reservations/new', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{slug}/confirmation', [ReservationController::class, 'confirmation'])->name('reservations.confirmation');
        Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('my-reservations');
        Route::delete('/my-reservations/{reservation}', [ReservationController::class, 'destroy'])->name('my-reservations.destroy');
        Route::put('/my-reservations/{reservation}/reschedule', [ReservationController::class, 'reschedule'])->name('my-reservations.reschedule');

        Route::get('/notifications/pending-count', function () {
            if (! auth()->user()->is_admin) {
                return response()->json(['count' => 0]);
            }
            return response()->json(['count' => Reservation::pending()->count()]);
        })->name('notifications.pending-count');
    });

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/home', [DashboardController::class, 'index'])->name('home');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::resource('spaces', SpaceAdminController::class);
        Route::post('/spaces/{space}/blocked-slots', [BlockedSlotController::class, 'store'])->name('spaces.blocked-slots.store');
        Route::delete('/blocked-slots/{blockedSlot}', [BlockedSlotController::class, 'destroy'])->name('blocked-slots.destroy');
        Route::resource('reservations', ReservationAdminController::class)->only(['index']);
        Route::post('/reservations/{reservation}/accept', [ReservationAdminController::class, 'accept'])->name('reservations.accept');
        Route::post('/reservations/{reservation}/reject', [ReservationAdminController::class, 'reject'])->name('reservations.reject');
        Route::post('/reservations/{reservation}/cancel', [ReservationAdminController::class, 'cancel'])->name('reservations.cancel');
        Route::delete('/reservations/{reservation}', [ReservationAdminController::class, 'destroy'])->name('reservations.destroy');
    });
