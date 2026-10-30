<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'destination_location',
        'travel_date',
        'travel_time',
        'seats_needed',
        'budget',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'travel_date'  => 'date',
            'seats_needed' => 'integer',
            'budget'       => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(DriverApplication::class);
    }

    public function isOpen(): bool
    {
        return strtolower($this->status) === 'open';
    }

    public function isConfirmed(): bool
    {
        return strtolower($this->status) === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return strtolower($this->status) === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return strtolower($this->status) === 'completed';
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}

