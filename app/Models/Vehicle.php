<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'registration_no',
        'model',
        'total_seats',
        'status',
        'rejection_reason',
    ];

    /**
     * Owner / Driver of the vehicle
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user (since owner is always an approved driver)
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Accessor alias for vehicle number
     */
    public function getVehicleNumberAttribute(): ?string
    {
        return $this->registration_no;
    }

    public function setVehicleNumberAttribute($value): void
    {
        $this->attributes['registration_no'] = $value;
    }

    public function isActive(): bool
    {
        return in_array(strtolower($this->status), ['active', 'approved']);
    }

    public function isApproved(): bool
    {
        return in_array(strtolower($this->status), ['approved', 'active']);
    }

    public function isPending(): bool
    {
        return strtolower($this->status) === 'pending';
    }

    public function isRejected(): bool
    {
        return strtolower($this->status) === 'rejected';
    }
}
