<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'from_stop_id',
        'to_stop_id',
        'seats',
        'seat_numbers',
        'total_fare',
        'booking_reference',
        'status',
        'booking_status',
    ];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function fromStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    public function toStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }
}
