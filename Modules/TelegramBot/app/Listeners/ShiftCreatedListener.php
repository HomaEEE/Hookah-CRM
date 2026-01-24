<?php

namespace Modules\TelegramBot\Listeners;

use App\Models\Shift;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Models\Bot;
use Modules\TelegramBot\Models\Channel;

class ShiftCreatedListener
{
    public function handle($event)
    {
        $shift = $event->shift ?? $event; // assuming it's passed as shift or the model itself

        // Assuming Shift has place_id, and Bot has place_id
        $bots = Bot::where('place_id', $shift->place_id)->get();

        foreach ($bots as $bot) {
            $channels = Channel::where('bot_id', $bot->id)->whereNotNull('chat_id')->get();

            foreach ($channels as $channel) {
                // Send message to channel's chat_id
                try {
                    $bot->message($channel->chat_id)
                        ->text($this->generateShiftReport($shift))
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Failed to send shift report to Telegram: '.$e->getMessage());
                }
            }
        }
    }

    private function generateShiftReport(Shift $shift): string
    {
        // Generate a simple report
        $placeName = $shift->place ? $shift->place->name : 'Unknown';
        $userName = $shift->user ? $shift->user->name : 'Unknown';

        return "New Shift Report:\n".
               "User: {$userName}\n".
               "Cash: {$shift->cash}\n".
               "Income: {$shift->income}\n".
               // Add more fields as needed
               '';
    }
}
