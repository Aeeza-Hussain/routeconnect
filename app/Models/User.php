<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * user_type values:
     *   0 = Passenger (normal user)
     *   1 = Admin
     *   2 = Driver
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'dob',
        'cnic',
        'bio',
        'license_no',
        'license_issue_date',
        'license_expiry_date',
        'cnic_front_image',
        'cnic_back_image',
        'license_front_image',
        'license_back_image',
        'emergency_contact_name',
        'emergency_contact_phone',
        'profile_photo',
        'password',
        'user_type',
        'driver_status',
        'rejection_reason',
        'terms_accepted_at',
        'privacy_accepted_at',
        'declaration_confirmed_at',
        'verification_consent_at',
    ];

    // ─────────────────────────────────────────
    // Simple user_type helper methods
    // ─────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->user_type == 1;
    }

    public function isDriver(): bool
    {
        return $this->user_type == 2;
    }

    public function isApprovedDriver(): bool
    {
        return $this->user_type == 2 && $this->driver_status === 'approved';
    }

    public function isPassenger(): bool
    {
        return $this->user_type == 0;
    }

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tripMessages(): HasMany
    {
        return $this->hasMany(TripMessage::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'user_type'                => 'integer',
            'license_issue_date'       => 'date',
            'license_expiry_date'      => 'date',
            'terms_accepted_at'        => 'datetime',
            'privacy_accepted_at'      => 'datetime',
            'declaration_confirmed_at' => 'datetime',
            'verification_consent_at'  => 'datetime',
        ];
    }
}
