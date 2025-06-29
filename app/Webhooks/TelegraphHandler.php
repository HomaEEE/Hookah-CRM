<?php

namespace App\Webhooks;

use App\Models\BotChat;
use App\Models\Income;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Filament\Facades\Filament;

class TelegraphHandler extends WebhookHandler
{
    protected int $messageId;

    protected int $bill_id;

    protected int $category_id;

    protected int $sum;

    public function start()
    {

        // BotChat::firstOrCreate(
        //     ['chat_id' => $this->chat->chat_id],
        //     ['chat_id' => $this->chat->chat_id,
        //         'name' => $this->chat->id,
        //         'telegraph_bot_id' => $this->bot->id,
        //     ]
        // );

        // $this->chat->html('test hello')
        //     ->keyboard(Keyboard::make()->buttons([
        //         Button::make('🗑️ Расход')->action('expense'),
        //         // Button::make("📖 Доход")->action("income"),
        //     ])->chunk(2))->send();

        // $this->messageId = $this->message->id();

        $chatId = $this->chat->chat_id;
        $text = $this->message->text();

        // Находим чат
        $chat = BotChat::where('chat_id', $chatId)->first();

        if (! $chat) {
            // Первое сообщение — создаём и ставим как "guest"
            $chat = BotChat::create([
                'chat_id' => $chatId,
                'telegraph_bot_id' => $this->bot->id,
                'role' => BotChat::GUEST,
            ]);
        }

        // Привязка к тенанту, если ещё не выбрано
        if (! $chat->place_id) {
            // Показываем кнопки городов (например, из базы)
            $cities = \App\Models\Place::pluck('city')->chunk(2)->toArray();
            $this->chat->message('Выберите ваш город:')
                ->keyboard($cities)
                ->send();

            return;
        }

        // ✅ Установка контекста арендатора
        Filament::setTenant($chat->place_id);

    }
}
