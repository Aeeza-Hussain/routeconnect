<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
    ];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot('id', 'stop_order')
            ->withTimestamps();
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function tripStops(): HasMany
    {
        return $this->hasMany(TripStop::class);
    }

    public function isActive(): bool
    {
        return strtolower($this->status) === 'active';
    }
}
