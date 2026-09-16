<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'admin_id',
        'sender',
        'message',
        'is_read',
    ];

    public function getSenderTypeAttribute()
    {
        return $this->sender;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
