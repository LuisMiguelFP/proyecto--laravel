<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedSlot extends Model
{
    use HasFactory;

    protected $table = 'blocked_slots';

    protected $fillable = [
        'space_id', 'start_time', 'end_time', 'reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function scopeOverlapping($query, $start, $end)
    {
        return $query->where('start_time', '<', $end)
                     ->where('end_time',   '>', $start);
    }
}