<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'type',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_read'  => 'boolean',
            'read_at'  => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isSender(User $user): bool
    {
        return $this->sender_id === $user->id;
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        if (!$this->hasAttachment()) {
            return false;
        }

        if ($this->attachment_type && str_starts_with($this->attachment_type, 'image/')) {
            return true;
        }

        $extension = strtolower(pathinfo($this->attachment_name ?? $this->attachment_path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    public function formattedAttachmentSize(): string
    {
        $bytes = (int) ($this->attachment_size ?? 0);
        if ($bytes <= 0) {
            return '';
        }
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }
}

