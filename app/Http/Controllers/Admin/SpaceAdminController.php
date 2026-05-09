<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Space;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpaceAdminController extends Controller
{
    // GET /admin/spaces
    public function index()
    {
        $spaces = Space::withCount('reservations')
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id'                  => $s->id,
                'name'                => $s->name,
                'slug'                => $s->slug,
                'type'                => $s->type,
                'capacity'            => $s->capacity,
                'price_per_hour'      => $s->price_per_hour,
                'is_active'           => $s->is_active,
                'reservations_count'  => $s->reservations_count,
            ]);

        return Inertia::render('Admin/Spaces/Index', ['spaces' => $spaces]);
    }

    // GET /admin/spaces/create
    public function create()
    {
        return Inertia::render('Admin/Spaces/Form', ['space' => null]);
    }

    // POST /admin/spaces
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['required', 'string', 'max:100'],
            'capacity'       => ['required', 'integer', 'min:1'],
            'description'    => ['nullable', 'string'],
            'rules'          => ['nullable', 'string'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'is_active'      => ['boolean'],
            'availabilities' => ['array'],
            'availabilities.*.day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'availabilities.*.start_time'  => ['required', 'date_format:H:i'],
            'availabilities.*.end_time'    => ['required', 'date_format:H:i', 'after:availabilities.*.start_time'],
        ]);

        $space = Space::create($validated);

        if (! empty($validated['availabilities'])) {
            foreach ($validated['availabilities'] as $avail) {
                $space->availabilities()->create($avail);
            }
        }

        return redirect()->route('admin.spaces.index')
            ->with('success', 'Espacio creado correctamente.');
    }

    // GET /admin/spaces/{space}/edit
    public function edit(Space $space)
    {
        return Inertia::render('Admin/Spaces/Form', [
            'space' => [
                'id'             => $space->id,
                'name'           => $space->name,
                'slug'           => $space->slug,
                'type'           => $space->type,
                'capacity'       => $space->capacity,
                'description'    => $space->description,
                'rules'          => $space->rules,
                'price_per_hour' => $space->price_per_hour,
                'is_active'      => $space->is_active,
                'availabilities' => $space->availabilities()->orderBy('day_of_week')->get([
                    'id', 'day_of_week', 'start_time', 'end_time'
                ]),
            ],
        ]);
    }

    // PUT /admin/spaces/{space}
    public function update(Request $request, Space $space)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['required', 'string', 'max:100'],
            'capacity'       => ['required', 'integer', 'min:1'],
            'description'    => ['nullable', 'string'],
            'rules'          => ['nullable', 'string'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'is_active'      => ['boolean'],
            'availabilities' => ['nullable', 'array'],
            'availabilities.*.day_of_week' => ['required_with:availabilities', 'integer', 'min:0', 'max:6'],
            'availabilities.*.start_time'  => ['required_with:availabilities', 'date_format:H:i'],
            'availabilities.*.end_time'    => ['required_with:availabilities', 'date_format:H:i'],
        ]);

        $space->update($validated);

        if ($request->has('availabilities')) {
            $space->availabilities()->delete();
            foreach ($validated['availabilities'] ?? [] as $avail) {
                $space->availabilities()->create($avail);
            }
        }

        return redirect()->route('admin.spaces.index')
            ->with('success', 'Espacio actualizado correctamente.');
    }

    // DELETE /admin/spaces/{space}
    public function destroy(Space $space)
    {
        $space->delete();
        return redirect()->route('admin.spaces.index')
            ->with('success', 'Espacio eliminado.');
    }
}