<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat;

class BotChat extends TelegraphChat
{
    protected $table = 'telegraph_chats';

    protected $fillable = ['place_id', 'name', 'telegraph_bot_id'];

    // public function bot()
    // {
    //     return $this->belongsTo(Bot::class, 'bot_id');
    // }

    public const GUEST = 'guest';

    public const STAFF = 'staff';

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'user_id')->where('user_type', 'guest');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->where('user_type', 'user');
    }

    public function chats()
    {
        return $this->hasMany(BotChat::class, 'id', 'telegraph_bot_id');

    }

    public function getOwnerAttribute()
    {
        return $this->user_type === 'user'
            ? $this->user
            : $this->guest;
    }
}
