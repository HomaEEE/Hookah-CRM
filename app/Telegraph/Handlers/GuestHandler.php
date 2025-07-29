<?php

namespace App\Telegraph\Handlers;

use App\Models\BotChat;
use App\Models\Guest;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class GuestHandler extends WebhookHandler
{
    protected ?Guest $currentGuest = null;

    public function start(): void
    {
        $this->loadGuest();
        $this->showMainMenu();
    }

    public function handleChatMessage($text): void
    {
        $this->loadGuest();

        match ($text) {
            //  'Меню' => $this->handleMenu(),
            // 'Бронь' => $this->handleBooking(),
            // 'Предзаказ' => $this->handlePreorder(),
            default => $this->showMainMenu(),
        };
    }

    protected function loadGuest(): void
    {
        $botChat = BotChat::where('chat_id', $this->chat->chat_id)->first();
        $this->currentGuest = Guest::find($botChat->guest_id);
    }

    protected function showMainMenu(): void
    {
        $buttons = [
            ['Меню', 'Бронь'],
        ];

        if ($this->currentGuest?->preorder) {
            $buttons[0][] = 'Предзаказ';
        }

        $this->chat->message('Выберите действие:')
            ->replyKeyboard($buttons)
            ->send();
    }

    // Add other guest-specific methods...
}
