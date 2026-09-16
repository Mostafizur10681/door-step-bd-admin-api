<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedChat extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'blocked_by',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public static function isBlocked(?string $sessionId = null, ?int $userId = null): bool
    {
        if (!$sessionId && !$userId) {
            return false;
        }

        // Direct check in blocked_chats table
        $isDirectlyBlocked = self::where(function ($q) use ($sessionId, $userId) {
            if ($sessionId) {
                $q->orWhere('session_id', $sessionId);
            }
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->exists();

        if ($isDirectlyBlocked) {
            return true;
        }

        // If session_id is provided, check if any user associated with this session is blocked
        if ($sessionId) {
            $associatedUserIds = ChatMessage::where('session_id', $sessionId)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            if (!empty($associatedUserIds)) {
                $isAssociatedUserBlocked = self::whereIn('user_id', $associatedUserIds)->exists();
                if ($isAssociatedUserBlocked) {
                    return true;
                }

                $isUserStatusBlocked = User::whereIn('id', $associatedUserIds)
                    ->where('status', 'blocked')
                    ->exists();
                if ($isUserStatusBlocked) {
                    return true;
                }
            }
        }

        // If user_id is provided, check if user account itself has status 'blocked'
        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->status === 'blocked') {
                return true;
            }

            // Also check all session_ids used by this user
            $associatedSessionIds = ChatMessage::where('user_id', $userId)
                ->whereNotNull('session_id')
                ->pluck('session_id')
                ->unique()
                ->toArray();

            if (!empty($associatedSessionIds)) {
                $isAnySessionBlocked = self::whereIn('session_id', $associatedSessionIds)->exists();
                if ($isAnySessionBlocked) {
                    return true;
                }
            }
        }

        return false;
    }
}
