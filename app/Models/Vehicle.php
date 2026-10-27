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
        'rental_rate_per_day',
        'is_available_for_rental',
        'image',
        'description',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'rental_rate_per_day'     => 'decimal:2',
            'is_available_for_rental' => 'boolean',
            'total_seats'             => 'integer',
        ];
    }

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

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(VehicleUnavailability::class);
    }

    /**
     * Calculate rental price given start and end Carbon datetime or date strings.
     */
    public function calculateRentalPrice($start, $end): array
    {
        $startCarbon = $start instanceof \Carbon\Carbon ? $start : \Carbon\Carbon::parse($start);
        $endCarbon   = $end instanceof \Carbon\Carbon ? $end : \Carbon\Carbon::parse($end);

        // Calculate full or partial days (minimum 1 day)
        $diffHours = $startCarbon->diffInHours($endCarbon);
        $days = (int) ceil(max(1, $diffHours / 24));
        if ($days < 1) {
            $days = 1;
        }

        $dailyRate = (float) ($this->rental_rate_per_day ?: 4000.00);
        $totalFare = $days * $dailyRate;

        return [
            'days'        => $days,
            'daily_rate'  => $dailyRate,
            'total_fare'  => $totalFare,
        ];
    }

    /**
     * Check if vehicle is available for a specified datetime range.
     */
    public function isAvailableForRange($start, $end, $excludeBookingId = null): bool
    {
        return $this->getConflictReason($start, $end, $excludeBookingId) === null;
    }

    /**
     * Get descriptive conflict reason if unavailable, or null if available.
     */
    public function getConflictReason($start, $end, $excludeBookingId = null): ?string
    {
        if (!$this->isApproved()) {
            return 'Vehicle is not approved by administrators.';
        }

        if ($this->driver && !$this->driver->isApprovedDriver()) {
            return 'Driver is not approved by administrators.';
        }

        if ($this->is_available_for_rental === false) {
            return 'Vehicle is currently not offered for private rentals by its owner.';
        }

        $startCarbon = $start instanceof \Carbon\Carbon ? $start : \Carbon\Carbon::parse($start);
        $endCarbon   = $end instanceof \Carbon\Carbon ? $end : \Carbon\Carbon::parse($end);

        if ($endCarbon->lessThanOrEqualTo($startCarbon)) {
            return 'End date and time must be after start date and time.';
        }

        // Check 1: Existing Confirmed Private Bookings
        $conflictingBooking = Booking::where('vehicle_id', $this->id)
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(booking_status)'), ['cancelled'])
            ->where(function ($q) use ($startCarbon, $endCarbon) {
                $q->where('start_datetime', '<', $endCarbon)
                  ->where('end_datetime', '>', $startCarbon);
            })
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->first();

        if ($conflictingBooking) {
            $bStart = $conflictingBooking->start_datetime ? $conflictingBooking->start_datetime->format('d M Y h:i A') : 'N/A';
            $bEnd   = $conflictingBooking->end_datetime ? $conflictingBooking->end_datetime->format('d M Y h:i A') : 'N/A';
            return "Vehicle already has a confirmed private reservation (#{$conflictingBooking->booking_reference}) from {$bStart} to {$bEnd}.";
        }

        // Check 2: Scheduled Public Trips using this vehicle
        $trips = Trip::where('vehicle_id', $this->id)
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['cancelled', 'completed'])
            ->get();

        foreach ($trips as $trip) {
            $tripStart = $trip->getStartDateTime();
            $tripEnd   = $trip->getEndDateTime();

            if ($startCarbon->lessThan($tripEnd) && $endCarbon->greaterThan($tripStart)) {
                return "Vehicle is scheduled for public trip #{$trip->id} on " . $tripStart->format('d M Y h:i A') . ".";
            }
        }

        // Check 3: Driver Blocked Dates
        $blocked = $this->unavailabilities()
            ->where('start_date', '<', $endCarbon)
            ->where('end_date', '>', $startCarbon)
            ->first();

        if ($blocked) {
            $reasonText = $blocked->reason ? " ({$blocked->reason})" : '';
            return "Vehicle has been marked unavailable by driver{$reasonText} from " . 
                $blocked->start_date->format('d M Y') . " to " . $blocked->end_date->format('d M Y') . ".";
        }

        return null;
    }
}
