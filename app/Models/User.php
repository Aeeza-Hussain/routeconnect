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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'license_no',
        'profile_photo',
        'password',
        'role',
        'driver_status',
    ];

    /**
     * Role Helper Methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isApprovedDriver(): bool
    {
        return $this->role === 'driver' && $this->driver_status === 'approved';
    }

    public function isPassenger(): bool
    {
        return $this->role === 'passenger';
    }

    /**
     * Relationships
     */
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
