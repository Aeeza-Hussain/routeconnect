<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'driver_id',
        'vehicle_id',
        'offered_fare',
        'proposed_time',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'offered_fare' => 'decimal:2',
        ];
    }

    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isPending(): bool
    {
        return strtolower($this->status) === 'pending';
    }

    public function isAccepted(): bool
    {
        return strtolower($this->status) === 'accepted';
    }

    public function isConfirmed(): bool
    {
        return strtolower($this->status) === 'confirmed';
    }

    public function isRejected(): bool
    {
        return strtolower($this->status) === 'rejected';
    }
}

