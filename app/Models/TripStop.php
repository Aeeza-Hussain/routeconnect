<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'stop_id',
        'stop_order',
        'expected_time',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }
}
