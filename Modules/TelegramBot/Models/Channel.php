<?php

namespace Modules\TelegramBot\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'telegraph_channels';

    protected $fillable = ['name', 'bot_id', 'enabled', 'chat_id', 'type'];

    public function chats()
    {
        return $this->belongsTo(BotChat::class, 'id', 'telegraph_bot_id');
    }

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}
