<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    const STATUS_PENDING   = 'pendiente';
    const STATUS_CONFIRMED = 'confirmada';
    const STATUS_REJECTED  = 'rechazada';
    const STATUS_CANCELLED = 'cancelada';
    const STATUS_FINISHED  = 'finalizada';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
        self::STATUS_FINISHED,
    ];

    const LEVEL_BASIC    = 'basica';
    const LEVEL_STANDARD = 'estandar';
    const LEVEL_GOLD     = 'gold';

    const LEVELS = [
        self::LEVEL_BASIC => [
            'label' => 'Básica',
            'price_per_hour' => 15000,
            'color' => 'blue',
            'tools' => [
                'Cautín estándar x2',
                'Estaño para soldar',
                'Multímetro digital',
                'Pinzas y destornilladores',
                'Protoboard',
            ],
        ],
        self::LEVEL_STANDARD => [
            'label' => 'Estándar',
            'price_per_hour' => 25000,
            'color' => 'indigo',
            'tools' => [
                'Todo lo de Básica',
                'Osciloscopio digital',
                'Fuente de poder regulable',
                'Generador de señales',
                'Cautín de temperatura controlada',
                'Componentes SMD básicos',
            ],
        ],
        self::LEVEL_GOLD => [
            'label' => 'Gold',
            'price_per_hour' => 45000,
            'color' => 'yellow',
            'tools' => [
                'Todo lo de Estándar',
                'Máquina lavadora de circuitos',
                'Estación de soldadura SMD',
                'Analizador lógico',
                'Microscopio de inspección',
                'Impresora 3D',
                'Fresadora CNC básica',
            ],
        ],
    ];

    protected $fillable = [
        'space_id', 'user_id', 'slug', 'level',
        'start_time', 'end_time', 'status',
        'user_name', 'user_email', 'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Reservation $r) {
            if (empty($r->slug))   $r->slug   = (string) Str::uuid();
            if (empty($r->status)) $r->status = self::STATUS_PENDING;
        });
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
                     ->where('start_time', '>=', now());
    }

    public function scopeOverlapping($query, $start, $end)
    {
        return $query
            ->whereNotIn('status', [self::STATUS_REJECTED, self::STATUS_CANCELLED])
            ->where('start_time', '<', $end)
            ->where('end_time',   '>',  $start);
    }

    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isConfirmed(): bool { return $this->status === self::STATUS_CONFIRMED; }
    public function isRejected(): bool  { return $this->status === self::STATUS_REJECTED; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }
    public function isFinished(): bool  { return $this->status === self::STATUS_FINISHED; }

    public function getLevelInfoAttribute(): array
    {
        return self::LEVELS[$this->level] ?? self::LEVELS[self::LEVEL_BASIC];
    }

    public function getDurationHoursAttribute(): float
    {
        return $this->start_time->diffInMinutes($this->end_time) / 60;
    }

    public function getTotalPriceAttribute(): float
    {
        $pricePerHour = self::LEVELS[$this->level]['price_per_hour'] ?? 15000;
        return round($this->duration_hours * $pricePerHour, 2);
    }
}