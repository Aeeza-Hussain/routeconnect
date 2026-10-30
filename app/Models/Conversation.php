<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject_type',
        'subject_id',
        'status',
        'arrangement_status',
        'arrangement_details',
        'confirmed_booking_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'arrangement_details' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'is_blocked', 'blocked_by'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function confirmedBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'confirmed_booking_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ConversationReport::class);
    }

    /**
     * Get the other user in a 2-person conversation.
     */
    public function otherUser(User $currentUser): ?User
    {
        return $this->users->first(fn($u) => $u->id !== $currentUser->id);
    }

    /**
     * Check if a given user is a participant.
     */
    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a conversation is blocked for a user.
     */
    public function isBlocked(): bool
    {
        return $this->participants()->where('is_blocked', true)->exists();
    }

    public function isBlockedByUser(User $user): bool
    {
        return $this->participants()->where('is_blocked', true)->where('blocked_by', $user->id)->exists();
    }

    /**
     * Get count of unread messages for a specific user.
     */
    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        if (!$participant) {
            return 0;
        }

        $query = $this->messages()->where('sender_id', '!=', $user->id);
        if ($participant->last_read_at) {
            $query->where('created_at', '>', $participant->last_read_at);
        }

        return $query->count();
    }

    /**
     * Fetch the subject entity.
     */
    public function getSubjectAttribute(): mixed
    {
        if ($this->subject_type === 'vehicle' && $this->subject_id) {
            return Vehicle::with('driver')->find($this->subject_id);
        }
        if ($this->subject_type === 'travel_request' && $this->subject_id) {
            return TravelRequest::with('user')->find($this->subject_id);
        }
        if ($this->subject_type === 'driver_application' && $this->subject_id) {
            return DriverApplication::with(['travelRequest', 'driver', 'vehicle'])->find($this->subject_id);
        }
        return null;
    }
}

