<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Space;
use Inertia\Inertia;

class DashboardController extends Controller
{
    // GET /admin/home
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pending'   => Reservation::pending()->count(),
                'confirmed' => Reservation::confirmed()->count(),
                'upcoming'  => Reservation::upcoming()->count(),
                'spaces'    => Space::active()->count(),
            ],
            'pendingReservations' => Reservation::with('space')
                ->pending()
                ->orderBy('start_time')
                ->limit(10)
                ->get()
                ->map(fn($r) => [
                    'slug'       => $r->slug,
                    'space_name' => $r->space->name,
                    'user_name'  => $r->user_name,
                    'user_email' => $r->user_email,
                    'start_time' => $r->start_time->format('d/m/Y H:i'),
                    'end_time'   => $r->end_time->format('H:i'),
                ]),
            'upcomingReservations' => Reservation::with('space')
                ->upcoming()
                ->orderBy('start_time')
                ->limit(5)
                ->get()
                ->map(fn($r) => [
                    'slug'       => $r->slug,
                    'space_name' => $r->space->name,
                    'user_name'  => $r->user_name,
                    'start_time' => $r->start_time->format('d/m/Y H:i'),
                    'status'     => $r->status,
                ]),
        ]);
    }
}