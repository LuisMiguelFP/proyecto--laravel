<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'type', 'capacity',
        'description', 'rules', 'price_per_hour', 'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'price_per_hour' => 'decimal:2',
        'capacity'       => 'integer',
    ];

    // Route model binding por slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Auto-generar slug al crear/actualizar
    protected static function booted(): void
    {
        static::creating(function (Space $space) {
            if (empty($space->slug)) {
                $space->slug = static::generateUniqueSlug($space->name);
            }
        });

        static::updating(function (Space $space) {
            if ($space->isDirty('name') && ! $space->isDirty('slug')) {
                $space->slug = static::generateUniqueSlug($space->name, $space->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    // Relaciones
    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function blockedSlots()
    {
        return $this->hasMany(BlockedSlot::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Verifica si un rango está dentro del horario semanal
    public function isWithinSchedule(\Carbon\Carbon $start, \Carbon\Carbon $end): bool
    {
        return $this->availabilities()
            ->where('day_of_week', $start->dayOfWeek)
            ->where('start_time', '<=', $start->format('H:i'))
            ->where('end_time',   '>=', $end->format('H:i'))
            ->exists();
    }
}