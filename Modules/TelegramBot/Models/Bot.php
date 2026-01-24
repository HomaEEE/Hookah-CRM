<?php

namespace Modules\TelegramBot\Models;

use App\Models\Place;
use DefStudio\Telegraph\Models\TelegraphBot as BaseTelegraphBot;

class Bot extends BaseTelegraphBot
{
    protected $table = 'telegraph_bots';

    protected $fillable = ['token', 'name', 'place_id'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function chat()
    {
        return $this->belongsTo(BotChat::class, 'id', 'telegraph_bot_id');
    }

    public function channels()
    {
        return $this->belongsTo(Channel::class);

    }
}
