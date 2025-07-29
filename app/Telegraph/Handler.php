<?php

namespace App\Telegraph;

use App\Models\BotChat;
use App\Telegraph\Handlers\GuestHandler;
use App\Telegraph\Handlers\PreRegisterHandler;
use App\Telegraph\Handlers\StaffHandler;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class Handler extends WebhookHandler
{
    protected function detectHandler(): string
    {
        $chatId = $this->chat->chat_id;
        $botChat = BotChat::where('chat_id', $chatId)->first();

        if (! $botChat->type) {
            return PreRegisterHandler::class;
        }

        return match ($botChat->type) {
            'staff' => StaffHandler::class,
            'guest' => GuestHandler::class,
            default => PreRegisterHandler::class,
        };
    }

    public function start(): void
    {
        $handlerClass = $this->detectHandler();
        app($handlerClass)->handle($this->request, $this->bot);
    }

    public function handleChatMessage($text): void
    {
        $handlerClass = $this->detectHandler();
        app($handlerClass)->handleChatMessage($text);
    }
}
