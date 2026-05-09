<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpaceController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    // GET /
    public function index(Request $request)
    {
        $query = Space::active()->orderBy('name');

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        $spaces = $query->get()->map(fn($s) => [
            'id'             => $s->id,
            'name'           => $s->name,
            'slug'           => $s->slug,
            'type'           => $s->type,
            'capacity'       => $s->capacity,
            'description'    => $s->description,
            'price_per_hour' => $s->price_per_hour,
        ]);

        $types = Space::active()->distinct()->pluck('type')->sort()->values();

        return Inertia::render('Public/Spaces/Index', [
            'spaces'     => $spaces,
            'types'      => $types,
            'activeType' => $request->type,
        ]);
    }

    // GET /spaces/{slug}
    public function show(Request $request, Space $space)
    {
        abort_unless($space->is_active, 404);

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        return Inertia::render('Public/Spaces/Show', [
            'space' => [
                'id'             => $space->id,
                'name'           => $space->name,
                'slug'           => $space->slug,
                'type'           => $space->type,
                'capacity'       => $space->capacity,
                'description'    => $space->description,
                'rules'          => $space->rules,
                'price_per_hour' => $space->price_per_hour,
            ],
            'calendar'      => $this->availability->getWeeklyCalendarPublic($space, $weekStart),
            'upcomingSlots' => $this->availability->getUpcomingSlots($space),
            'weekStart'     => $weekStart->toDateString(),
            'prevWeek'      => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek'      => $weekStart->copy()->addWeek()->toDateString(),
        ]);
    }
}