<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_id', 'day_of_week', 'start_time', 'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function getDayNameAttribute(): string
    {
        return [
            0 => 'Domingo',  1 => 'Lunes',    2 => 'Martes',
            3 => 'Miércoles', 4 => 'Jueves',  5 => 'Viernes',
            6 => 'Sábado',
        ][$this->day_of_week] ?? '—';
    }
}