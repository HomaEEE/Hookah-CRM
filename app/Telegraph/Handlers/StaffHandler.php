<?php

namespace App\Telegraph\Handlers;

use DefStudio\Telegraph\Handlers\WebhookHandler;

class StaffHandler extends WebhookHandler
{
    public function start(): void
    {
        $this->showMainMenu();
    }

    public function handleChatMessage($text): void
    {
        match ($text) {
            'Добавить расход' => $this->handleExpense(),
            // 'Получить смену' => $this->handleShift(),
            // 'Админ-панель' => $this->handleAdmin(),
            default => $this->showMainMenu(),
        };
    }

    protected function showMainMenu(): void
    {
        $this->chat->message('Панель управления:')
            ->replyKeyboard([
                ['Добавить расход', 'Получить смену'],
                ['Админ-панель'],
            ])
            ->send();

    }

    protected function handleExpense(): void
    {
        $this->chat->message('Расход:')
            ->replyKeyboard([
                ['Добавить расход', 'Получить смену'],
                ['Админ-панель'],
            ])
            ->send();
    }

    // Add other staff-specific methods...
}
