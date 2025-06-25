<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotChat extends Model
{
    protected $table = 'telegraph_chats';

    use HasFactory;

    public function bot()
    {
        return $this->belongsTo(Bot::class, 'bot_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
