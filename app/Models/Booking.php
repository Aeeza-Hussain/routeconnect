<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'vehicle_id',
        'booking_type',
        'from_stop_id',
        'to_stop_id',
        'pickup_location',
        'destination_location',
        'start_datetime',
        'end_datetime',
        'days',
        'daily_rate',
        'passenger_notes',
        'seats',
        'seat_numbers',
        'total_fare',
        'booking_reference',
        'status',
        'booking_status',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime'   => 'datetime',
            'days'           => 'integer',
            'seats'          => 'integer',
            'daily_rate'     => 'decimal:2',
            'total_fare'     => 'decimal:2',
        ];
    }

    public function getBookingStatusAttribute(): ?string
    {
        return $this->attributes['booking_status'] ?? $this->attributes['status'] ?? 'confirmed';
    }

    public function setBookingStatusAttribute($value): void
    {
        $this->attributes['booking_status'] = $value;
        $this->attributes['status'] = $value;
    }

    public function isConfirmed(): bool
    {
        return strtolower($this->booking_status ?? $this->status) === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return strtolower($this->booking_status ?? $this->status) === 'cancelled';
    }

    public function isPrivate(): bool
    {
        return strtolower($this->booking_type ?? 'shared') === 'private';
    }

    public function isShared(): bool
    {
        return !$this->isPrivate();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fromStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    public function toStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'confirmed_booking_id');
    }
}
