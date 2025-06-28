<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use DefStudio\Telegraph\Facades\Telegraph;

class TelegramBotController extends Controller
{
    public function sendTestMessage($chatId, $data)
    {
        Telegraph::chat($chatId)->message($data['message'])->send();
    }

    public function setWebhook($bot_id)
    {
        $bot = Bot::where('id', $bot_id);
        $bot->registerWebhook()->send();
        $bot->webhook = 1;
        $bot->save();
    }
}
