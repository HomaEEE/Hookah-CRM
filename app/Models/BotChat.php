<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat;

class BotChat extends TelegraphChat
{
    protected $table = 'telegraph_chats';

    // public function bot()
    // {
    //     return $this->belongsTo(Bot::class, 'bot_id');
    // }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
