<?php

namespace App\Http\Controllers;

use DefStudio\Telegraph\Facades\Telegraph;

class TelegramBotController extends Controller
{
    public function sendTestMessage($chatId, $data)
    {
        Telegraph::chat($chatId)->message($data['message'])->send();
    }

    public function setWebhook($bot_id)
    {
        Telegraph::registerWebhook($bot_id)->send();
    }
}
