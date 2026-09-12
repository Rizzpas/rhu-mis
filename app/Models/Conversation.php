<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = ['type'];

    /**
     * Users participating in this conversation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * All messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The latest message in this conversation (for preview).
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Scope to only conversations a user belongs to.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Get the other participant in a direct conversation.
     */
    public function getOtherUser(int $currentUserId): ?User
    {
        return $this->users->firstWhere('id', '!=', $currentUserId);
    }

    /**
     * Count unread messages for a specific user.
     */
    public function unreadCountFor(int $userId): int
    {
        $pivot = $this->users()->where('users.id', $userId)->first()?->pivot;
        $lastReadAt = $pivot?->last_read_at;

        $query = $this->messages()->where('sender_id', '!=', $userId);

        if ($lastReadAt) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }

    /**
     * Find or create a direct conversation between two users.
     */
    public static function findOrCreateDirect(int $userIdA, int $userIdB): self
    {
        // Look for an existing direct conversation between these two users
        $conversation = static::where('type', 'direct')
            ->whereHas('users', function ($q) use ($userIdA) {
                $q->where('users.id', $userIdA);
            })
            ->whereHas('users', function ($q) use ($userIdB) {
                $q->where('users.id', $userIdB);
            })
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Create a new direct conversation
        $conversation = static::create(['type' => 'direct']);
        $conversation->users()->attach([
            $userIdA => ['last_read_at' => now()],
            $userIdB => ['last_read_at' => null],
        ]);

        return $conversation;
    }
}
