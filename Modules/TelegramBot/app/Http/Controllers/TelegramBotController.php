<?php

namespace Modules\TelegramBot\app\Http\Controllers;

use DefStudio\Telegraph\Facades\Telegraph;
use Modules\TelegramBot\Models\Bot;
use Nwidart\Modules\Routing\Controller;

class TelegramBotController extends Controller
{
    public function sendTestMessage($chatId, $data)
    {
        Telegraph::chat($chatId)->message($data['message'])->send();
    }

    public function sendTestReport($chatId)
    {
        $report = "Test Shift Report:\n".
                  "Place: Test Place\n".
                  "User: Test User\n".
                  "Cash: 1000\n".
                  "Income: 1500\n".
                  'This is a test message.';

        Telegraph::chat($chatId)->message($report)->send();
    }

    public function setWebhook($bot_id)
    {
        $bot = Bot::where('id', $bot_id);
        $bot->registerWebhook()->send();
        $bot->webhook = 1;
        $bot->save();
    }
}
