<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'destination_location',
        'intermediate_stops',
        'travel_date',
        'travel_time',
        'seats_needed',
        'preferred_vehicle_type',
        'budget',
        'notes',
        'status',
        'expires_at',
        'selected_application_id',
    ];

    protected function casts(): array
    {
        return [
            'travel_date'             => 'date',
            'intermediate_stops'      => 'array',
            'seats_needed'            => 'integer',
            'budget'                  => 'decimal:2',
            'expires_at'              => 'datetime',
            'selected_application_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(DriverApplication::class);
    }

    public function driverApplications(): HasMany
    {
        return $this->hasMany(DriverApplication::class);
    }

    public function selectedApplication(): BelongsTo
    {
        return $this->belongsTo(DriverApplication::class, 'selected_application_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'travel_request_id');
    }

    public function isOpen(): bool
    {
        return strtolower($this->status) === 'open' && !$this->isExpired();
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

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Atomically select a single driver application for this travel request.
     * Enforces the constraint: "A passenger can receive multiple applications but can select only one driver."
     */
    public function selectApplication(DriverApplication $application): bool
    {
        if ($application->travel_request_id !== $this->id) {
            throw new \InvalidArgumentException('Application does not belong to this travel request.');
        }

        return DB::transaction(function () use ($application) {
            // 1. Mark the selected driver application as accepted
            $application->update(['status' => 'accepted']);

            // 2. Reject or supersede any other applications for this request
            $this->applications()
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // 3. Mark the travel request as confirmed and save the selected driver application reference
            $this->update([
                'status'                  => 'confirmed',
                'selected_application_id' => $application->id,
            ]);

            return true;
        });
    }
}
