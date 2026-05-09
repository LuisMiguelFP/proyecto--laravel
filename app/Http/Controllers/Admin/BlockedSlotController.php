<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedSlot;
use App\Models\Space;
use Illuminate\Http\Request;

class BlockedSlotController extends Controller
{
    // POST /admin/spaces/{space}/blocked-slots
    public function store(Request $request, Space $space)
    {
        $validated = $request->validate([
            'start_time' => ['required', 'date', 'after:now'],
            'end_time'   => ['required', 'date', 'after:start_time'],
            'reason'     => ['nullable', 'string', 'max:255'],
        ]);

        $space->blockedSlots()->create($validated);

        return back()->with('success', 'Bloqueo creado correctamente.');
    }

    // DELETE /admin/blocked-slots/{blockedSlot}
    public function destroy(BlockedSlot $blockedSlot)
    {
        $blockedSlot->delete();
        return back()->with('success', 'Bloqueo eliminado.');
    }
}