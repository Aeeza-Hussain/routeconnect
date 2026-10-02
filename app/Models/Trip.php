<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'route_id',
        'trip_date',
        'departure_time',
        'available_seats',
        'fare',
        'pickup_point',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'trip_date'       => 'date',
            'available_seats' => 'integer',
            'fare'            => 'decimal:2',
        ];
    }

    public function isScheduled(): bool
    {
        return strtolower($this->status) === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return strtolower($this->status) === 'completed';
    }

    public function isCancelled(): bool
    {
        return strtolower($this->status) === 'cancelled';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function tripStops(): HasMany
    {
        return $this->hasMany(TripStop::class)->orderBy('stop_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tripMessages(): HasMany
    {
        return $this->hasMany(TripMessage::class)->latest();
    }
}
