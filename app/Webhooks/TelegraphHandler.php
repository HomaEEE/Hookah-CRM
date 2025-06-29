<?php

namespace App\Webhooks;

use App\Models\Bill;
use App\Models\BotChat;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\Income;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class TelegraphHandler extends WebhookHandler
{
    protected int $messageId;

    protected int $bill_id;

    protected int $category_id;

    protected int $sum;

    public function start()
    {

        BotChat::firstOrCreate(
            ['chat_id' => $this->chat->chat_id],
            ['chat_id' => $this->chat->chat_id,
                'name' => $this->chat->id,
                'telegraph_bot_id' => $this->bot->id,
            ]
        );

        $this->chat->html('test hello')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('🗑️ Расход')->action('expense'),
                // Button::make("📖 Доход")->action("income"),
            ])->chunk(2))->send();

        $this->messageId = $this->message->id();
    }

    public function menu()
    {
        $this->chat->edit($this->messageId)->html('edit')
            ->keyboard(Keyboard::make()->buttons([
                Button::make('🗑️ Расход')->action('expense'),
                Button::make('📖 Доход')->action('income'),
            ])->chunk(2))->send();
    }

    public function income()
    {
        $categories = Category::where('type', 1)->get();
        $buttons = [];
        foreach ($categories as $cat) {
            $buttons[] = Button::make($cat->name)->action('bills')->param('category_id', $cat->id);
        }

        $buttons[] = Button::make('Меню')->action('menu');

        $this->chat->edit($this->messageId)->html('Добавить доход')
            ->replaceKeyboard(
                messageId: $this->messageId,
                newKeyboard: Keyboard::make()->buttons($buttons)
            )->send();
    }

    public function bills()
    {
        $bills = Bill::get();

        foreach ($bills as $bill) {
            $buttons[] = Button::make($bill->name)->action('handleMessage')->param('bill_id', $bill->id);
        }

        $buttons[] = Button::make('Назад')->action('menu');

        $this->chat->edit($this->messageId)->html('Выбирите счет')
            ->replaceKeyboard(
                messageId: $this->messageId,
                newKeyboard: Keyboard::make()->buttons($buttons)
            )->send();
    }

    public function handleMessage(): void
    {
        $this->chat->html('Введите сумму');
        $this->message->text();
        $this->addIncome($this->category_id, $this->bill_id, $this->sum);
    }

    public function addIncome($category_id, $bill_id, $sum)
    {
        Income::create([
            'comment' => 'test',
            'category_id' => $category_id,
            'bill_id' => $bill_id,
            'sum' => $sum,
        ]);
        $this->menu();
    }

    public function expense()
    {

        $categories = ExpenseCategory::get();
        $buttons = [];
        foreach ($categories as $cat) {
            $buttons[] = Button::make($cat->name)->action('add_expensr')->param('id', $cat->id);
        }

        $buttons[] = Button::make('Меню')->action('menu');

        $this->chat->edit($this->messageId)->message('Расход')
            ->replaceKeyboard(
                messageId: $this->messageId,
                newKeyboard: Keyboard::make()->buttons($buttons)
            )->send();
    }
}
