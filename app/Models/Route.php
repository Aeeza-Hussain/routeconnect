<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_location',
        'end_location',
        'status',
        'start_stop_id',
        'end_stop_id',
    ];

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class)->orderBy('stop_order');
    }

    public function stops(): BelongsToMany
    {
        return $this->belongsToMany(Stop::class, 'route_stops')
            ->withPivot('id', 'stop_order')
            ->withTimestamps()
            ->orderByPivot('stop_order');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function startStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'start_stop_id');
    }

    public function endStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'end_stop_id');
    }

    /**
     * Backward-compatible accessors for origin and destination
     */
    public function getOriginAttribute(): ?string
    {
        return $this->start_location ?? $this->startStop?->name;
    }

    public function getDestinationAttribute(): ?string
    {
        return $this->end_location ?? $this->endStop?->name;
    }

    public function isActive(): bool
    {
        return strtolower($this->status) === 'active';
    }
}
