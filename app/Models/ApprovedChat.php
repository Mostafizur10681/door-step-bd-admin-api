<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovedChat extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'approved_by',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function isApproved(?string $sessionId = null, ?int $userId = null): bool
    {
        if (!$sessionId && !$userId) {
            return false;
        }

        // Direct check in approved_chats table
        $isDirectlyApproved = self::where(function ($q) use ($sessionId, $userId) {
            if ($sessionId) {
                $q->orWhere('session_id', $sessionId);
            }
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->where('status', 'approved')->exists();

        if ($isDirectlyApproved) {
            return true;
        }

        // If session_id is provided, check if any user associated with this session is approved
        if ($sessionId) {
            $associatedUserIds = ChatMessage::where('session_id', $sessionId)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            if (!empty($associatedUserIds)) {
                $isAssociatedUserApproved = self::whereIn('user_id', $associatedUserIds)
                    ->where('status', 'approved')
                    ->exists();
                if ($isAssociatedUserApproved) {
                    return true;
                }
            }
        }

        return false;
    }
}
