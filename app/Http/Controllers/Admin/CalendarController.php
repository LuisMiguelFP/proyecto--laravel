<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    // GET /admin/calendar?space={slug}&week={date}
    public function index(Request $request)
    {
        $spaces = Space::active()->orderBy('name')->get(['id', 'name', 'slug']);
        $space  = $request->filled('space')
            ? Space::where('slug', $request->space)->firstOrFail()
            : $spaces->first();

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        return Inertia::render('Admin/Calendar/Index', [
            'spaces'     => $spaces,
            'space'      => $space ? ['id' => $space->id, 'name' => $space->name, 'slug' => $space->slug] : null,
            'calendar'   => $space ? $this->availability->getWeeklyCalendarAdmin($space, $weekStart) : [],
            'weekStart'  => $weekStart->toDateString(),
            'prevWeek'   => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek'   => $weekStart->copy()->addWeek()->toDateString(),
        ]);
    }
}
